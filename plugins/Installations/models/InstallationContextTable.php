<?php


class InstallationContextTable extends Omeka_Db_Table
{
    
    public function _applySearchFilters($select, $params)
    {
        foreach($params as $field=>$value)
        {
            $select->where($this->_alias . ".$field = ? ", $value);
        }
        return $select;
    }
    
    
    public function findByInstallationIdAndOrigId($instId, $origId)
    {
        $select = $this->getSelectForFindBy(array('installation_id'=>$instId, 'orig_id'=>$origId));
        return $this->fetchObject($select);
    }
}