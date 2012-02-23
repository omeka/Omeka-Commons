<?php

class CommonsApi_Importer
{
    public $data = array();
    public $response = array();
    private $site;
    private $site_url;
    private $db;
    private $key;

    public function __construct($data)
    {
        $this->db = get_db();
        Omeka_Context::getInstance()->setAcl(null);

        $this->validate($data);
        $this->key = $data['key'];
        $this->site_url = $data['site_url'];
        $sites = $this->db->getTable('Site')->findBy(array('key'=>$data['key']));
        if($sites) {
            $site = $sites[0];
        } else {
            $site = new Site();
        }
        foreach($data['site'] as $key=>$value) {
            $site->$key = $value;
        }
        $site->save();
        $this->site = $site;
    }

    public function processSite($data)
    {
        foreach($data as $key=>$value) {
            $this->site->$key = $value;
        }

        if(!empty($_FILES['logo']['name'])) {
            $fileName = $this->site->id  . $_FILES['logo']['name'];
            $filePath = SITES_PLUGIN_DIR . '/views/images/' . $fileName;
            if(!move_uploaded_file($_FILES['logo']['tmp_name'], $filePath)) {
                _log('Could not save the file to ' . $filePath);
            }
            $this->site->logo_url = WEB_ROOT . '/plugins/Sites/views/images/' . $fileName;
        }

        $this->site->last_import = Zend_Date::now()->toString('yyyy-MM-dd HH:mm:ss');
        $data = $this->preprocessSiteCss($data);
        foreach($data as $field=>$value) {
            $this->site->$field = $value;
        }
        $this->site->save();
    }


    public function processItem($data)
    {

        $siteItem = $this->db->getTable('SiteItem')->findBySiteIdAndOrigId($this->site->id, $data['orig_id']);

        if($siteItem) {
            $item = $siteItem->findItem();
            $this->updateItem($item, $data);
        } else {

            $item = $this->importItem($data);
            $siteItem = new SiteItem();
            $siteItem->item_id = $item->id;
        }

        if(!empty($data['files'])) {
            $this->processItemFiles($item, $data['files']);
        }
        if(!empty($data['tags'])) {
            $this->processItemTags($item, $data['tags']);
        }

        $siteItem->site_id = $this->site->id;
        $siteItem->orig_id = $data['orig_id'];
        $siteItem->item_id = $item->id;
        $siteItem->url = $data['url'];
        $siteItem->license = $data['license'];
        $siteItem->save();

        //update or add to collection information
        $has_container = $this->db->getTable('RecordRelationsProperty')->findByVocabAndPropertyName(SIOC, 'has_container');

        if(isset($data['collection'])) {

            //collections are imported before items, so this should already exist
            $instCollection = $this->db->getTable('SiteContext_Collection')->findBySiteIdAndOrigId($this->site->id,$data['collection']);
            $options = array(
                'subject_record_type' => 'SiteItem',
                'subject_id' => $siteItem->id,
                'object_record_type' => 'SiteContext_Collection',
                'object_id' => $instCollection->id,
                'property_id' => $has_container->id,
                'user_id' => 1
            );

            //use record relations here, so we can keep the history if a site
            //changes the collection an item is in

            //check if relation already exists
            $relation = $this->db->getTable('RecordRelationsRelation')->findOne($options);

            if($instCollection && !$relation) {

                $relation = new RecordRelationsRelation();
                $relation->property_id = $has_container->id;
                $relation->subject_record_type = 'SiteItem';
                $relation->object_record_type = 'SiteContext_Collection';
                $relation->subject_id = $siteItem->id;
                $relation->object_id = $instCollection->id;
                $relation->user_id = 1; //@TODO remove the magic number
                $relation->save();
            }
        }

        //build relations to exhibit data

        if(isset($data['exhibitPages'])) {
            $options = array(
                'subject_record_type' => 'SiteItem',
                'subject_id' => $siteItem->id,
                'object_record_type' => 'SiteCollection',
                'object_id' => $instCollection->id,
                'property_id' => $has_container->id,
                'user_id' => 1
            );

            foreach($data['exhibitPages'] as $pageId) {
                $options['object_record_type'] = 'SiteContext_ExhibitSectionPage';
                $pageContext = $this->db->getTable('SiteContext_ExhibitSectionPage')->findBySiteIdAndOrigId($this->site->id,$pageId);
                $options['object_id'] = $pageContext->id;
                $relation = $this->db->getTable('RecordRelationsRelation')->findOne($options);
                if(!$relation) {
                    $relation = new RecordRelationsRelation();
                    $relation->setProps($options);
                    $relation->save();
                }

                $sectionId = $pageContext->site_section_id;

                $options['object_record_type'] = 'SiteContext_ExhibitSection';
                $sectionContext = $this->db->getTable('SiteContext_ExhibitSection')->findBySiteIdAndOrigId($this->site->id,$sectionId);
                $options['object_id'] = $sectionContext->id;
                $relation = $this->db->getTable('RecordRelationsRelation')->findOne($options);
                if(!$relation) {
                    $relation = new RecordRelationsRelation();
                    $relation->setProps($options);
                    $relation->save();
                }

                $exhibitId = $sectionContext->site_exhibit_id;
                $options['object_record_type'] = 'SiteContext_Exhibit';
                $exhibitContext = $this->db->getTable('SiteContext_Exhibit')->findBySiteIdAndOrigId($this->site->id, $exhibitId);
                $options['object_id'] = $exhibitContext->id;
                $relation = $this->db->getTable('RecordRelationsRelation')->findOne($options);
                if(!$relation) {
                    $relation = new RecordRelationsRelation();
                    $relation->setProps($options);
                    $relation->save();
                }
            }

        }
        $this->response['status'] = 'success';
    }

    public function processContext($data, $context)
    {
        $contextRecord = $this->db->getTable('SiteContext_' . $context)->findBySiteIdAndOrigId($this->site->id, $data['orig_id']);
        if(!$contextRecord) {
            $class = 'SiteContext_' . $context;
            $contextRecord = new $class();
        }

        $contextRecord->site_id = $this->site->id;
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
        $item = $this->db->getTable('SiteItem')->findItemBy(array('orig_id'=>$fileData['item_orig_id']) );
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
            _log($e);
        }
    }

    private function processItemTags($item, $tags)
    {
        $entity = $this->site->getEntity();
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

        //data might be a string if we're doing a pull from site, array if a push
        if(is_string($data)) {
            $itemTypeData = $this->parseSiteItemTypeData($data);
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

    private function parseSiteItemTypeData($name, $description = null)
    {
        $returnArray = array();

        //remove the 'Item Type Metadata' if it's there from how Commons exports
        $offset = strpos($name, ' Item Type Metadata');
        if($offset !== false) {
            $name = substr($name, 0, $offset);
        }
        $returnArray['name'] = $this->site_url . '/customItemTypes/' . $name;
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
        if(!isset($data['key']) || !isset($data['site_url'])) {
            throw new Exception('Importer: Data array not set');
        }

    }

    private function preprocessSiteCss($data)
    {
        $css = "h1 {color: " . $data['commons_title_color'] .  "; }";


        $data['css'] = $css;
        unset($data['commons_title_color']);
        return $data;
    }
}