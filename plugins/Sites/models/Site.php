<?php

class Site extends Omeka_Record
{
    public $id;
    public $owner_id;
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
    public $css;
    public $branding;


    protected $_related = array('Owner'=>'getSiteOwner');
    
    public function beforeSave()
    {
        if(!is_array($this->branding)) {
            $this->branding = array();
        }
        $this->branding = serialize($this->branding);
    }

    public function getSiteOwner()
    {
        $ownersArray = $this->getTable('SiteOwner')->findBy(array('site_id'=>$this->id));
        return $ownersArray[0];                
    }
}