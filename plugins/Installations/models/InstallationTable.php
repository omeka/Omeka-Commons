<?php

class InstallationTable extends Omeka_Db_Table
{
    
    protected $_alias = 'it';
    
    
    public function applySearchFilters($select, $params)
    {
        $columns = $this->getColumns();
        foreach($columns as $column) {
            if(array_key_exists($column, $params)) {
                $select->where($this->_alias . ".$column = ? ", $params[$column]);
            }
        }
        _log($select);
        return $select;
    }
    
    public function findByUrlKey($url, $key)
    {
        $select = $this->getSelectForFindBy(array('url'=>$url, 'key'=>$key));
        return $this->fetchObject($select);
    }
    
}