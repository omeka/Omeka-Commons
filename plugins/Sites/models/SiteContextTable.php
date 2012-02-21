<?php


class SiteContextTable extends Omeka_Db_Table
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


    public function findBySiteIdAndOrigId($siteId, $origId)
    {
        $select = $this->getSelectForFindBy(array('site_id'=>$siteId, 'orig_id'=>$origId));
        return $this->fetchObject($select);
    }
}