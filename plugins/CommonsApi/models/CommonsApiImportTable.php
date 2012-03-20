<?php

class CommonsApiImportTable extends Omeka_Db_Table
{
    protected $_alias = 'cai';

    public function findMostRecent($siteUrl)
    {
        $db = $this->getDb();
        $select = $this->getSelect();
        $select->join(array('i'=>$db->Site), 'cai.site_id = i.id', array() );
        $select->where('i.url = ?', $siteUrl);
        $select->order("cai.id DESC");
        return $this->fetchObject($select);
    }


    public function recordFromData($data)
    {
        $data['status'] = unserialize($data['status']);
        $class = $this->_target;
        $obj = new $class($this->_db);
        $obj->setArray($data);
        return $obj;
    }

}