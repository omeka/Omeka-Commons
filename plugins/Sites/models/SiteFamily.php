<?php

/**
 * SiteFamilies allow aggregation of Sites under one controlling authority, for example when one institution
 * is contributing data from many different Omeka sites. In that case, the SiteOwner is the institution.
 * @TODO: figure out how that works, and what data goes with it
 * 
 * @author patrickmj
 *
 */

class SiteFamily extends Omeka_Record_AbstractRecord
{
    public $id;
    public $name;
    public $description;
    
    public function getSites()
    {
        return $this->getTable('Site')->findBy(array('site_family_id'=>$this->id));
    }
    
    public function getRecordUrl($action = 'show')
    {
        return url("sites/site-family/$action/id/{$this->id}");
    }
}