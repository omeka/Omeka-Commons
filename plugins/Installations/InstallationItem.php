<?php

class InstallationItem extends Omeka_Record
{
    public $id;
    public $item_id;
    public $installation_id;
    public $orig_id;
    public $url;
    public $license;
    

    public function findItem()
    {
        $db = get_db();
        //Item::getSelect() builds in ACL filtering, so have to do things in...unanticipated...ways
        $select = "SELECT * from `$db->Item` AS i WHERE i.id = " . $this->item_id;
        return $db->getTable('Item')->fetchObject($select);
    }
}