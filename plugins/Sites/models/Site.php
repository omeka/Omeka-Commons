<?php

class Site extends Omeka_Record_AbstractRecord
{
    public $id;
    public $site_owner_id;
    public $owner_id;
    public $url;
    public $admin_email;
    public $title;
    public $description;
    public $api_key;
    public $import_url;
    public $added;
    public $last_import;
    public $copyright_info;
    public $author_info;
    public $css;
    public $branding;


    protected $_related = array('SiteOwner'=>'getSiteOwner');

    protected function _initializeMixins()
    {
        $this->_mixins[] = new Mixin_Owner($this);
    }    
    
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
        return isset($ownersArray[0]) ? $ownersArray[0] : false;                
    }
    
    public function getRecordUrl($action) {
        $url = url("/sites/display-case/$action/id/" . $this->id);
        return $url;
    }
    
}