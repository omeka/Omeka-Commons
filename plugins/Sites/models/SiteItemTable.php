<?php

class SiteItemTable extends Omeka_Db_Table
{
    protected $_alias = 'sit';

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


    public function findBySiteIdAndOrigId($instId, $origId)
    {
        $select = $this->getSelectForFindBy(array('site_id'=>$instId, 'orig_id'=>$origId));
        return $this->fetchObject($select);
    }

    public function findByItemId($itemId)
    {
        $params = array('item_id'=>$itemId);
        $select = $this->getSelectForFindBy($params);
        return $this->fetchObject($select);
    }

    public function findItemsBy($params)
    {
        $db = get_db();
        $itemTable = $db->getTable('Item');
        $select = $itemTable->getSelect();
        foreach($params as $field=>$value) {
            $select->where("sit.$field = ?", $value);
        }
        $select->join(array('sit'=>$db->SiteItem), 'sit.item_id = i.id', array());
        return $itemTable->fetchObjects($select);
    }

    public function findSiteForItem($item_id)
    {
        $sitesTable = $this->getTable('Site');
        $select = $sitesTable->getSelect();
        $select->join(array('sit'=>$this->_db->SiteItem), 'sit.site_id = st.id', array());
        $select->where("sit.item_id = ?", $item_id);
        return $this->fetchObject($select);
    }

    public function findItemForId($id)
    {
        $db = get_db();
        $itemTable = $db->getTable('Item');
        $select = $itemTable->getSelect();
        $select->where('sit.id = ?', $id);
        $select->join(array('sit'=>$db->SiteItems), 'sit.item_id = i.id', array());
        return $this->getTable('Item')->fetchObject($select);

    }


}