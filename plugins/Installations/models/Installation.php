<?php

class Installation extends Omeka_Record
{
    public $id;
    public $url;
    public $admin_email;
    public $title;
    public $description;
    public $key;
    public $import_url;
    public $added;
    public $last_import;
    public $copyright_info;
    public $author_info;
    public $entity_id;
    
    public function getEntity()
    {
        return $this->_db->getTable('Entity')->find($this->entity_id);
    }
    
    
    
    public function save()
    {
        //first make sure that there is an Entity for the Installation
        if(is_null($this->entity_id)) {
            $installationEntity = new Entity();
            //calling the institution the name of the Omeka Installation
            $installationEntity->institution = $this->title;
            $installationEntity->save();
            $this->entity_id = $installationEntity->id;
        }
        
        parent::save();
    }
    
    
}