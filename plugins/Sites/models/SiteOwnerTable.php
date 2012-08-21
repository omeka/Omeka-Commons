<?php

class SiteOwnerTable extends Omeka_Db_Table
{
    public function applySearchFilters($select, $params)
    {
        $columns = $this->getColumns();
        foreach($columns as $column) {
            if(array_key_exists($column, $params)) {
                $select->where("$column = ? ", $params[$column]);
            }
        }
        return $select;
    }    
}