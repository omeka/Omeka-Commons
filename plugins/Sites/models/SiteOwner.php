<?php

/**
 * SiteOwners allow aggregation of Sites under one controlling authority, for example when one institution
 * is contributing data from many different Omeka sites. In that case, the SiteOwner is the institution.
 * @TODO: figure out how that works, and what data goes with it
 * 
 * @author patrickmj
 *
 */

class SiteOwner extends Omeka_Record_AbstractRecord
{
    public $id;
    public $user_id;
    public $name;
    public $description;
    
    protected $_related = array('User'=>'getUser');
            
    public function getUser()
    {
        return $this->getTable('User')->find($this->user_id);
    }
}