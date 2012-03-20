<?php


class CommonsApiImport extends Omeka_Record
{
    public $id;
    public $site_id;
    public $time;
    public $status;


    protected function beforeSave()
    {
        $this->status = serialize($this->status);
    }
}