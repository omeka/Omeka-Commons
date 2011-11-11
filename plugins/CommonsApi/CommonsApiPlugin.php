<?php
define('COMMONSAPI_PLUGIN_DIR', dirname(__FILE__));
require_once PLUGIN_DIR . '/RecordRelations/includes/models/RelatableRecord.php';

    class CommonsApiPlugin extends Omeka_Plugin_Abstract
    {
        
        protected $_hooks = array('install', 'uninstall');
        protected $_filters = array();
        protected $_options = null;
        
        public function install()
        {
       
            
        }
        
        public function uninstall()
        {
       
        }
        
        
        
    }