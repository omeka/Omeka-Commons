<?php

class Table_SiteItem extends Omeka_Db_Table
{

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

    public function findItemsBy($params, $limit = null, $page = null)
    {
        $db = get_db();
        $itemTable = $db->getTable('Item');
        $select = $itemTable->getSelectForFindBy($params);
        foreach($params as $field=>$value) {
            $select->where("site_items.$field = ?", $value);
        }
        $select->join(array('site_items'=>$db->SiteItem), 'site_items.item_id = items.id', array());

        if ($limit) {
            $this->applyPagination($select, $limit, $page);
        }
        return $itemTable->fetchObjects($select);
    }

    public function findSiteForItem($item)
    {
        if(is_numeric($item)) {
            $item_id = $item;
        } else {
            $item_id = $item->id;
        }

        $sitesTable = $this->getTable('Site');
        $select = $sitesTable->getSelect();
        $select->join(array('site_items'=>$this->_db->SiteItem), 'site_items.site_id = sites.id', array());
        $select->where("site_items.item_id = ?", $item_id);
        return $sitesTable->fetchObject($select);
    }

    public function findItemForId($id)
    {
        $db = get_db();
        $itemTable = $db->getTable('Item');
        $select = $itemTable->getSelect();
        $select->where('site_items.id = ?', $id);
        $select->join(array('site_items'=>$db->SiteItems), 'site_items.item_id = items.id', array());
        return $this->getTable('Item')->fetchObject($select);
    }
}