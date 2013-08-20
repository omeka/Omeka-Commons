<?php

class Table_Site extends Omeka_Db_Table
{
    public function applySearchFilters($select, $params)
    {
        if(isset($params['random'])) {
            $select = $this->orderSelectByRandom($select);
        }
        if(isset($params['approved'])) {
            if($params['approved'] == 'true') {
                $select->where("`date_approved` IS NOT NULL");
            } else {
                $select->where("`date_approved` IS NULL");
            }
        }
        
        if(isset($params['title']) && $params['title'] != '') {
            $title = $params['title'];
            $select->where("title LIKE ?", '%' . $params['title'] . '%');
            unset($params['title']); //so the parent filters won't hit this
        }
        
        if(isset($params['admin_name']) && $params['admin_name'] != '') {
            $admin_name = $params['admin_name'];
            $select->where("admin_name LIKE ?", '%' . $params['admin_name'] . '%');
            unset($params['admin_name']); //so the parent filters won't hit this
        }
        
        if(isset($params['affiliation']) && $params['affiliation'] != '') {
            $select->where('affiliation LIKE ?', '%' . $params['affiliation'] . '%');
        }        
        //don't let search params go to the parent filter
        unset($params['affiliation']);
        unset($params['title']);
        unset($params['admin_name']);
        parent::applySearchFilters($select, $params);
        debug(print_r($params, true));
        return $select;
    }
    
    public function findByKey($key)
    {
        $select = $this->getSelectForFindBy(array('api_key'=>$key));
        debug($select);
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
        $record->commons_settings = unserialize($record->commons_settings);
        return $record;
    }
}