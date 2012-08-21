<?php

class SiteOwner extends Omeka_Record
{
    public $id;
    public $user_id;
    
    protected $_related = array('User'=>'getUser');
            
    public function getUser()
    {
        return $this->getTable('User')->find($this->user_id);

    }
}