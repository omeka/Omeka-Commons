<?php


class InstallationContextTable extends Omeka_Db_Table
{
    
    public function applySearchFilters($select, $params)
    {
        $columns = $this->getColumns();
        foreach($columns as $column) {
            if(array_key_exists($column, $params)) {
                $select->where($this->_alias . ".$column = ? ", $params[$column]);
            }
        }
        return $select;
    }
    
    
    public function findByInstallationIdAndOrigId($instId, $origId)
    {
        $select = $this->getSelectForFindBy(array('installation_id'=>$instId, 'orig_id'=>$origId));
        return $this->fetchObject($select);
    }
}