<?php


class Table_SiteContext extends Omeka_Db_Table
{
    public function findBySiteIdAndOrigId($siteId, $origId)
    {
        $select = $this->getSelectForFindBy(array('site_id'=>$siteId, 'orig_id'=>$origId));
        return $this->fetchObject($select);
    }
}