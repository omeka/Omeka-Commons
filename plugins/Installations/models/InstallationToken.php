<?php

class InstallationToken extends Omeka_Record
{
    public $id;
    public $installation_id;
    public $token;
    public $expiration;
    
    
    public function beforeSave()
    {
        $this->expiration = time() + 60*60*7;
    }

}