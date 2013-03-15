<?php

class SiteToken extends Omeka_Record_AbstractRecord
{
    public $id;
    public $site_id;
    public $token;
    public $expiration;


    public function beforeSave()
    {
        $this->expiration = time() + 60*24*7;
    }

}