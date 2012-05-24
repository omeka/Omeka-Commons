<?php

class SiteTable extends Omeka_Db_Table
{

    public function applySearchFilters($select, $params)
    {
        $columns = $this->getColumns();
        foreach($columns as $column) {
            if(array_key_exists($column, $params)) {
                $select->where("sites.$column = ? ", $params[$column]);
            }
        }
        if(isset($params['random'])) {
            $select = $this->orderSelectByRandom($select);
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
        $select->join(array('site_items'=>$this->_db->SiteItem), 'site_items.item_id = items.id', array());
        $select->where("site_id = ? ", $siteId);
        return $itemTable->fetchObjects($select);
    }

    public function findTagsForSite($site)
    {
        if(is_numeric($site)) {
            $siteId = $site;
        } else {
            $siteId = $site->id;
        }
    }

    public function orderSelectByRandom($select)
    {
        $select->order('RAND()');
    }

    protected function recordFromData($data)
    {
        $record = parent::recordFromData($data);
        $record->branding = unserialize($record->branding);
        return $record;
    }
}