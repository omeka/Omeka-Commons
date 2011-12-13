<?php

class CommonsApiImportTable extends Omeka_Db_Table
{
    protected $_alias = 'cai';
    
    public function findMostRecent($installationUrl)
    {
        $db = $this->getDb();
        $select = $this->getSelect();
        $select->join(array('i'=>$db->Installation), 'cai.installation_id = i.id', array() );
        $select->where('i.url = ?', $installationUrl);
        $select->order("cai.id DESC");
        return $this->fetchObject($select);
    }
    
    
    
    
}