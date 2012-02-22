<?php

class SiteTable extends Omeka_Db_Table
{

    protected $_alias = 'st';


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

    public function findByUrlKey($url, $key)
    {
        $select = $this->getSelectForFindBy(array('url'=>$url, 'key'=>$key));
        return $this->fetchObject($select);
    }

    public function findItemsForSite($site, $params)
    {
        if(is_numeric($site)) {
            $siteId = $site;
        } else {
            $siteId = $site->id;
        }
        $itemTable = $this->getDb()->getTable('Item');
        $select = $itemTable->getSelectForFindBy($params);
        $select->join(array('sit'=>$this->_db->SiteItem), 'sit.item_id = i.id', array());
        $select->where("sit.site_id = ? ", $siteId);
        _log($select);
        return $itemTable->fetchObjects($select);
    }

}