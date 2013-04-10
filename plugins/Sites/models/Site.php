<?php

class Site extends Omeka_Record_AbstractRecord
{
    public $id;
    public $site_family_id;
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
    public $version;
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

    public function getSiteFamily()
    {
        if($this->site_family_id) {
            $familiesArray = $this->getTable('SiteFamily')->findBy(array('site_id'=>$this->site_family_id));
            return isset($familiesArray[0]) ? $familiesArray[0] : false;            
        }
        return false;
    }
    
    public function getRecordUrl($action) {
        $url = url("/sites/display-case/$action/id/" . $this->id);
        return $url;
    }
    
}