<?php

class Table_Site extends Omeka_Db_Table
{
    public function applySearchFilters($select, $params)
    {
        if(isset($params['random'])) {
            $select = $this->orderSelectByRandom($select);
        }
        if(isset($params['unapproved'])) {
            $select->where("`added` IS NULL");
        }
        if(isset($params['approved'])) {
            $select->where("`added` IS NOT NULL");
        }        
        parent::applySearchFilters($select, $params);
        return $select;
    }
    
    public function findByKey($key)
    {
        $select = $this->getSelectForFindBy(array('api_key'=>$key));
        return $this->fetchObject($select);        
    }

    public function findByUrlKey($url, $key)
    {
        $select = $this->getSelectForFindBy(array('url'=>$url, 'api_key'=>$key));
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