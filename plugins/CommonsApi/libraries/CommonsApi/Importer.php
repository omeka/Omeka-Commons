<?php

class CommonsApi_Importer
{
    public $data = array();
    public $status = array();
    public $hasErrors = false;
    public $has_container_id;
    public $site;
    public $site_url;
    public $db;
    public $key;

    public function __construct($data)
    {
        if(! is_array($data)) {
            $data = json_decode($data, true);
        }
        $this->sites['items'] = array();
        $this->sites['collections'] = array();
        $this->db = get_db();
        Omeka_Context::getInstance()->setAcl(null);

        if($this->validate($data)) {
            $this->data = $data;
        }

        if($this->setSite()) {
            $this->processSite();
        } else {
            return false;
        }
        $has_container = $this->db->getTable('RecordRelationsProperty')->findByVocabAndPropertyName(SIOC, 'has_container');
        $this->has_container_id = $has_container->id;
    }

    public function processSite()
    {
        foreach($this->data['site'] as $key=>$value) {
            $this->site->$key = $value;
        }

        if(!empty($_FILES['logo']['name'])) {

            $fileName = $this->site->id  . $_FILES['logo']['name'];
            $filePath = SITES_PLUGIN_DIR . '/views/images/' . $fileName;
            if(!move_uploaded_file($_FILES['logo']['tmp_name'], $filePath)) {
                _log('Could not save the file to ' . $filePath);
                $this->hasErrors = true;
                $this->status[] = array('errorMessage' =>'Could not save the file to ' . $filePath );
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
        try {
            $siteItem->save();
            $this->status['items'][$siteItem->orig_id] = array('status'=>'ok', 'commons_item_id'=>$item->id, 'status_message'=>'OK');
        } catch(Exception $e) {
            _log($e);
            $this->hasErrors = true;
            $this->status['items'][$siteItem->orig_id] = array('status'=>'error', 'commons_item_id'=>$item->id, 'status_message'=>$e->getMessage());
        }

        //update or add to collection information via RecordRelations
        $has_container = $this->db->getTable('RecordRelationsProperty')->findByVocabAndPropertyName(SIOC, 'has_container');

        if(isset($data['collection'])) {
            //collections are imported before items, so this should already exist
            $siteCollection = $this->db->getTable('SiteContext_Collection')->findBySiteIdAndOrigId($this->site->id,$data['collection']);
            $this->buildRelation($siteItem, $siteCollection);
        }

        //build relations to exhibit data
        //exhibits are imported before items, so they should already exist.

        if(isset($data['exhibitPages'])) {
            foreach($data['exhibitPages'] as $pageId) {
                $pageContext = $this->db->getTable('SiteContext_ExhibitSectionPage')->findBySiteIdAndOrigId($this->site->id,$pageId);
                $this->buildRelation($siteItem, $pageContext);

                $sectionId = $pageContext->site_section_id;
                $sectionContext = $this->db->getTable('SiteContext_ExhibitSection')->findBySiteIdAndOrigId($this->site->id,$sectionId);
                $this->buildRelation($siteItem, $sectionContext);

                $exhibitId = $sectionContext->site_exhibit_id;
                $exhibitContext = $this->db->getTable('SiteContext_Exhibit')->findBySiteIdAndOrigId($this->site->id, $exhibitId);
                $this->buildRelation($siteItem, $exhibitContext);
            }
        }
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
        $contextRecord->last_update = Zend_Date::now()->toString('yyyy-MM-dd HH:mm:ss');
        $contextRecord->save();
        return $contextRecord;
    }

    public function buildRelation($item, $contextRecord)
    {
        $options = array(
            'subject_record_type' => 'SiteItem',
            'subject_id' => $siteItem->id,
            'object_record_type' => get_class($contextRecord),
            'object_id' => $contextRecord->id,
            'property_id' => $this->has_container_id,
            'user_id' => 1
        );

        //use record relations here, so we can keep the history if a site
        //changes the collection an item is in

        //check if relation already exists
        $relation = $this->db->getTable('RecordRelationsRelation')->findOne($options);
        if(!$relation) {
            $relation = new RecordRelationsRelation();
            $relation->setProps($options);
            $relation->save();
        }
    }

    public function processItemFiles($item, $filesData)
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
            insert_files_for_item($item, $transferStrategy, $filesData, $options);
        } catch (Exception $e) {
            _log($e);
            $this->status[] = array('status'=>'error', 'item'=>$item->id, 'error'=>$e);
        }
    }

    public function processItemTags($item, $tags)
    {
        $item->addTags($tags);
    }

    public function importItem($data)
    {
        $itemMetadata = $data;
        unset($itemMetadata['tags']);
        $itemElementTexts = $this->processItemElements($data);
        $itemMetadata['public'] = true;

        try {
            $item = insert_item($itemMetadata, $itemElementTexts);
        } catch (Exception $e) {
            _log($e);
            $this->status[] = array('status'=>'error', 'error'=>$e);
        }
        return $item;
    }

    public function updateItem($item, $data)
    {
        $itemMetadata = $data;
        $itemMetadata['overwriteElementTexts'] = true;
        unset($itemMetadata['tags']);
        $itemMetadata['public'] = true;
        $itemElementTexts = $this->processItemElements($data);
        try {
            update_item($item, $itemMetadata, $itemElementTexts);
        } catch (Exception $e) {
            _log($e);
            $this->status[] = array('status'=>'error', 'error'=>$e);
        }


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
                try {
                    $itemType->addElements($elementArray);
                } catch (Exception $e) {
                    _log($e);
                    $this->addError(array('item'=>$item->id, 'error'=>$e));
                }
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

    public function parseSiteItemTypeData($name, $description = null)
    {
        $returnArray = array();

        //remove the 'Item Type Metadata' if it's there from how Commons exports
        $offset = strpos($name, ' Item Type Metadata');
        if($offset !== false) {
            $name = substr($name, 0, $offset);
        }
        $returnArray['name'] = $this->site->url . '/customItemTypes/' . $name;
        if($description) {
            $returnArray['description'] = $description;
        }
        return $returnArray;
    }

    public function validate($data)
    {
        if(!isset($data['key']) || !isset($data['site_url'])) {
            return false;
        }
        return true;
    }

    public function setSite()
    {
        $sites = get_db()->getTable('Site')->findBy(array('url'=> $this->data['site_url']), 1);
        $errors = false;
        if(empty($sites)) {
            // $import->status['status'] = 'fail';
            // $import->status['messages'] = 'Invalid Site URL';
            $errors = true;
            _log("Site " . $this->data['site_url'] . " does not exist.");
        }

        $site = $sites[0];
        // $import->site_id = $site->id;

        if(is_null($site->added)) {
            // $import->status['status'] = 'fail';
            // $import->status['messages'] = 'Site not yet approved. Check back later';
            $errors = true;
            _log("Site " . $this->data['site_url'] . " not yet approved.");
        }

        //check that the keys match!
        if($this->data['key'] != $site->key) {
            _log('invalid key: ' . $this->data['site_url']);
            // $import->status['status'] = 'fail';
            // $import->status['messages'] = 'Invalid key';
            $errors = true;
            _log("Site " . $this->data['site_url'] . " has a bad key: " . $this->data['key']);
        }

        $this->site = $site;
        return $errors;
    }

    public function preprocessSiteCss($data)
    {
        $css = "h1 {color: " . $data['commons_title_color'] .  "; }";
        $data['css'] = $css;
        unset($data['commons_title_color']);
        return $data;
    }
}