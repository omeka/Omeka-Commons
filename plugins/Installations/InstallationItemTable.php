<?php

class InstallationItemTable extends Omeka_Db_Table
{
    

    protected $_alias = 'iit';
    
    public function applySearchFilters($select, $params)
    {
        foreach($params as $field=>$value)
        {
            $select->where($this->_alias . ".$field = ? ", $value);
        }
        return $select;
    }
    
    public function findItemBy($params)
    {
        $db = get_db();
        $itemTable = $db->getTable('Item');
        $select = $itemTable->getSelect();
        foreach($params as $field=>$value) {
            $select->where("iit.$field = ?", $value);
            
        }
        $select->join(array('iit'=>$db->InstallationItems), 'iit.item_id = i.id', array());
        return $this->getTable('Item')->fetchObject($select);
        
        
    }
    
    public function findItemForOriginalId($orig_id)
    {
        
    }
    
    public function findItemForId($id)
    {
        $db = get_db();
        $itemTable = $db->getTable('Item');
        $select = $itemTable->getSelect();
        $select->where('iit.id = ?', $id);
        $select->join(array('iit'=>$db->InstallationItems), 'iit.item_id = i.id', array());
        return $this->getTable('Item')->fetchObject($select);
        
    }
}