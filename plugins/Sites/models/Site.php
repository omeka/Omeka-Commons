<?php

class Site extends Omeka_Record_AbstractRecord
{
    public $id;
    public $site_aggregation_id;
    public $owner_id;
    public $url;
    public $super_email;
    public $admin_email;
    public $admin_name;
    public $content_summary;
    public $affiliation;
    public $join_reason;
    public $title;
    public $description;
    public $key;
    public $date_applied;
    public $date_approved;
    public $last_import;
    public $copyright_info;
    public $author_info;
    public $omeka_version;
    public $commons_settings;


    protected $_related = array('SiteOwner'=>'getSiteOwner');

    protected function _initializeMixins()
    {
        $this->_mixins[] = new Mixin_Owner($this);
    }    
    
    public function beforeSave()
    {
        if(!is_array($this->commons_settings)) {
            $this->commons_settings = array();
        }
        $this->commons_settings = serialize($this->commons_settings);
    }

    public function getSiteAggregation()
    {
        if($this->site_aggregation_id) {
            return  $this->getTable('SiteAggregation')->find($this->site_aggregation_id);
        }
        return false;
    }
    
    public function getRecordUrl($action) {
        $url = url("/sites/display-case/$action/id/" . $this->id);
        return $url;
    }
    
}