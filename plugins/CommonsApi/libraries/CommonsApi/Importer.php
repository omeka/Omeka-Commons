<?php
/*
 * This will later be sorted out into a real importer. Now, it is mostly for testing and developing
 *
 *
 * process* methods decide whether it is an update or an import/insert, and branch as needed
 */

class CommonsApi_Importer
{
    public $response = array();
    private $installation;
    private $installation_url;
    private $db;
    private $key;
    
    public function __construct($data)
    {
        $this->db = get_db();
        
        
        Omeka_Context::getInstance()->setAcl(null);

        $this->validate($data);
        $this->key = $data['key'];
        $this->installation_url = $data['installation_url'];
        $installations = $this->db->getTable('Installation')->findBy(array('key'=>$data['key']));
        if($installations) {
            $installation = $installations[0];
        } else {
            $installation = new Installation();
        }
        foreach($data['installation'] as $key=>$value) {
            $installation->$key = $value;
        }
        $installation->save();
        $this->installation = $installation;
    }
    
    public function processInstallation($data)
    {
        foreach($data as $key=>$value) {
            $this->installation->$key = $value;
        }
        $this->installation->last_import = Zend_Date::now()->toString('yyyy-MM-dd HH:mm:ss');
        $this->installation->key = $this->key;
        $this->installation->save();
    }

    
    public function processItem($data)
    {

        $installationItems = $this->db->getTable('InstallationItem')->findByInstallationIdAndOrigId($this->installation->id, $data['orig_id']);

        if(empty($installationItems)) {
            $item = $this->importItem($data);
            $installationItem = new InstallationItem();
            $installationItem->item_id = $item->id;
        } else {
            $installationItem = $installationItems[0];
            $item = $installationItem->findItem();
            $this->updateItem($item, $data);
        }

        if(!empty($data['files'])) {
            $this->processItemFiles($item, $data['files']);
        }
        if(!empty($data['tags'])) {
            $this->processItemTags($item, $data['tags']);
        }

        $installationItem->installation_id = $this->installation->id;
        $installationItem->orig_id = $data['orig_id'];
        $installationItem->item_id = $item->id;
        $installationItem->url = $data['url'];
        $installationItem->license = $data['license'];
        $installationItem->save();

        //update or add to collection information

        if(isset($data['collection'])) {

            //collections are imported before items, so this should already exist
            $instCollection = $this->db->getTable('InstallationContext_Collection')->findByInstallationIdAndOrigId($this->installation->id,$data['collection']);
            
            $has_collection = $this->db->getTable('RecordRelationsProperty')->findByVocabAndPropertyName(SIOC, 'has_container');
            $options = array(
                'subject_record_type' => 'InstallationItem',
                'subject_id' => $installationItem->id,
                'object_record_type' => 'InstallationCollection',
                'object_id' => $instCollection->id,
                'property_id' => $has_collection->id
            );

            //use record relations here, so we can keep the history if a site
            //changes the collection an item is in
            
            //check if relation already exists
            $relation = $this->db->getTable('RecordRelationsRelation')->findOne($options);

            if($instCollection && !$relation) {
                $relation = new RecordRelationsRelation();
                $relation->property_id = $has_collection->id;
                $relation->subject_record_type = 'InstallationItem';
                $relation->object_record_type = 'InstallationContext_Collection';
                $relation->subject_id = $installationItem->id;
                $relation->object_id = $instCollection->id;
                $relation->user_id = 1; //@TODO remove the magic number
                $relation->save();
            }
            
        }
        $this->response['status'] = 'success';
    }
    
    public function processContext($data, $context)
    {
        $contextRecord = $this->db->getTable('InstallationContext_' . $context)->findByInstallationIdAndOrigId($this->installation->id, $data['orig_id']);
        if(!$contextRecord) {
            $class = 'InstallationContext_' . $context;
            $contextRecord = new $class();
        }
        
        $contextRecord->installation_id = $this->installation->id;
        foreach($data as $key=>$value) {
            $contextRecord->$key = $value;
        }
        $contextRecord->save();
        
    }
    

    /**
     *
     * Used on a reported update to a file
     * @param array $fileData
     */
    
    public function processFile($fileData)
    {
        $item = $this->db->getTable('InstallationItem')->findItemBy(array('orig_id'=>$fileData['item_orig_id']) );
        $this->processItemFiles($item, $fileData['url'] );
    }
                    
    private function processItemFiles($item, $filesData)
    {
        //check if files have already been imported
        $fileTable = $this->db->getTable('File');
        foreach($filesData as $index=>$fileName) {
            $select = $fileTable->getSelectForCount();
            $select->where('original_filename = ?', $fileName );
            $count = $this->db->fetchOne($select);
            if($count != 0) {
                unset($filesData[$index]);
            }
        }
        $transferStrategy = 'Url';
        $options = array( );

        try {
            $result = insert_files_for_item($item, $transferStrategy, $filesData, $options);
        } catch (Exception $e) {
                    _log($e->getMessage());
                    _log($e->getTraceAsString());
        }
    }

    private function processItemTags($item, $tags)
    {
        $entity = $this->installation->getEntity();
        $item->addTags($tags, $entity);
    }
    
    private function importItem($data)
    {
        $itemMetadata = $data;
        unset($itemMetadata['tags']);
        $itemElementTexts = $this->processItemElements($data);
        $itemMetadata['public'] = true;

        $item = insert_item($itemMetadata, $itemElementTexts);
        $this->response['id'] = $item->id;
        return $item;
    }
    
    private function updateItem($item, $data)
    {

        $itemMetadata = $data;
        $itemMetadata['overwriteElementTexts'] = true;
        unset($itemMetadata['tags']);
        $itemMetadata['public'] = true;
        $itemElementTexts = $this->processItemElements($data);
        update_item($item, $itemMetadata, $itemElementTexts);
        $this->response['id'] = $item->id;
    }
    
    public function processItemElements($data)
    {

        //process ItemTypes and ItemType Metadata to make sure they all exist first
        $newElementTexts = array();
        foreach($data['elementTexts'] as $elSet=>$elTexts) {
            if(strpos($elSet, 'Item Type Metadata') !== false) {
               $itemType = $this->processItemType($elSet);
               $newElementTexts['Item Type Metadata'] = $elTexts;
               $this->processItemTypeElements($itemType, $elTexts);
            } else {
                $newElementTexts[$elSet] = $elTexts;
            }
        }

        return $newElementTexts;
        //@TODO: prefix custom elements somewhere
    }
    
    public function processItemType($data)
    {

        //data might be a string if we're doing a pull from installation, array if a push
        if(is_string($data)) {
            $itemTypeData = $this->parseInstallationItemTypeData($data);
        }

        $itemTypesTable = $this->db->getTable('ItemType');
        $itemType = $itemTypesTable->findByName($itemTypeData['name']);

        if(!$itemType) {
            $itemType = $this->importItemType($itemTypeData);
        } else {
            
            if($itemType->name != $itemTypeData['name']) {
                $itemType->name = $itemTypeData['name'];
            }
            
            if( $itemType->description != $itemTypeData['description'] ) {
                $itemType->description = $itemTypeData['description'];
            }

            $itemType->save();
            
            
        }

        return $itemType;
        
    }
    
    public function processItemTypeElements($itemType, $data)
    {
        //make sure the elements exist and are updated
        foreach($data as $elName=>$elData) {
            if(! $itemType->hasElement($elName)) {
                $elData['name'] = $elName;

                $elementArray = array(array('name'=>$elData['name']));
                $itemType->addElements($elementArray);
            }
        }
        
        //@TODO: updating elements
    }
    
    public function importItemType($itemTypeData)
    {
        $itemType = new ItemType();
        $itemType->name = $itemTypeData['name'];
        if(isset($itemTypeData['description'])) {
            $itemType->description = $itemTypeData['description'];
        }
        $itemType->save();
        return $itemType;
    }
    
    private function parseInstallationItemTypeData($name, $description = null)
    {
        $returnArray = array();
        
        //remove the 'Item Type Metadata' if it's there from how Commons exports
        $offset = strpos($name, ' Item Type Metadata');
        if($offset !== false) {
            $name = substr($name, 0, $offset);
        }
        $returnArray['name'] = $this->installation_url . '/customItemTypes/' . $name;
        if($description) {
            $returnArray['description'] = $description;
        }
        return $returnArray;
    }
    
    private function validate($data)
    {
        if(!is_array($data)) {
            throw new Exception('Importer: Data is not an array');
        }
        if(!isset($data['key']) || !isset($data['installation_url'])) {
            throw new Exception('Importer: Data array not set');
        }
        
    }
}