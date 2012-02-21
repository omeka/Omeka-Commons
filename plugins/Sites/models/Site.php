<?php

class Site extends Omeka_Record
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
        //first make sure that there is an Entity for the Site
        //@TODO: remove for latest versions of Omeka
        if(is_null($this->entity_id)) {
            $siteEntity = new Entity();
            //calling the institution the name of the Omeka Site
            $siteEntity->institution = $this->title;
            $siteEntity->save();
            $this->entity_id = $siteEntity->id;
        }

        parent::save();
    }


}