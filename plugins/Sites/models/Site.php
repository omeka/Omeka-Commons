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
    public $css;
    public $branding;


    public function beforeSave()
    {
        $this->branding = serialize($this->branding);
    }

}