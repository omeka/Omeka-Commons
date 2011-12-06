<?php

class InstallationTable extends Omeka_Db_Table
{
    
    protected $_alias = 'it';
    
    public function applySearchFilters($select, $params)
    {
        $validParams = array(
        	'id',
            'url',
            'admin_email',
            'title',
            'description',
            'key',
            'added',
            'last_import',
            'copyright_info',
            'author_info',
            'entity_id'
        );
        
        foreach($validParams as $field)
        {
            if(isset($params[$field])) {
                $select->where($this->_alias . ".$field = ? ", $params[$field]);
            }
        }
        return $select;
    }
    
    
}