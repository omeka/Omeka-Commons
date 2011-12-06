<?php
define('INSTALLATIONS_PLUGIN_DIR', dirname(__FILE__));
require_once PLUGIN_DIR . '/RecordRelations/includes/models/RelatableRecord.php';

if (class_exists('Omeka_Plugin_Abstract')) {
    
    class InstallationsPlugin extends Omeka_Plugin_Abstract
    {
        
        protected $_hooks = array('install', 'uninstall', 'installation_browse_sql');
        protected $_filters = array();
        protected $_options = null;
        
        public function hookInstall()
        {
            $db = get_db();
            //Installation table
            $sql = "
            CREATE TABLE IF NOT EXISTS `$db->Installation` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `entity_id` int(10) unsigned NULL,
              `url` text NULL,
              `admin_email` text NULL,
              `title` text NULL,
              `description` text NULL,
              `key` text NULL,
              `import_url` text NULL,
              `last_import` timestamp NULL DEFAULT NULL,
              `added` timestamp NULL DEFAULT NULL,
              `copyright_info` text,
              `author_info` text NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci
            
            ";
            $db->query($sql);
            //InstallationCollection table
            $sql = "
            CREATE TABLE IF NOT EXISTS `$db->InstallationContextCollection` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `installation_id` int(10) unsigned NOT NULL,
              `orig_id` int(10) unsigned NOT NULL,
              `url` text NULL,
              `title` text NULL,
              `description` text NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci
            ";
                    
            $db->query($sql);
            
            //InstallationExhibit table
            $sql = "
            CREATE TABLE IF NOT EXISTS `$db->InstallationContextExhibit` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `installation_id` int(10) unsigned NOT NULL,
              `orig_id` int(10) unsigned NOT NULL,
              `url` text NULL,
              `title` text NULL,
              `description` text NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci
            ";
            
            $db->query($sql);
            
            $sql = "
            CREATE TABLE IF NOT EXISTS `$db->InstallationContextExhibitSection` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `installation_id` int(10) unsigned NOT NULL,
              `installation_exhibit_id` int(10) unsigned NOT NULL,
              `orig_id` int(10) unsigned NOT NULL,
              `url` text NULL,
              `title` text NULL,
              `description` text NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci
            ";

            $db->query($sql);
            
            $sql = "
            CREATE TABLE IF NOT EXISTS `$db->InstallationContextExhibitSectionPage` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `installation_id` int(10) unsigned NOT NULL,
              `installation_section_id` int(10) unsigned NOT NULL,
              `orig_id` int(10) unsigned NOT NULL,
              `url` text NULL,
              `title` text NULL,
              `description` text NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci
            ";
            
            
            $db->query($sql);
            //InstallationItem table
            $sql = "
            CREATE TABLE IF NOT EXISTS `$db->InstallationItem` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `item_id` int(10) unsigned NOT NULL,
              `installation_id` int(10) unsigned NOT NULL,
              `orig_id` int(10) unsigned NOT NULL,
              `url` text NULL,
              `license` text NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci
            ";
            $db->query($sql);
      
                    
            $prop = get_db()->getTable('RecordRelationsProperty')->findByVocabAndPropertyName(SIOC, 'has_container');
            if(empty($prop)) {
                $propData = array(
                    'namespace_prefix' => 'sioc',
                    'namespace_uri' => SIOC,
                    'properties' => array(
                        array(
                            'local_part' => 'has_container',
                            'label' => 'Has container',
                            'description' => 'The Container to which this Item belongs.'
                        )
                    )
                );
                record_relations_install_properties(array($propData));
            }
            
            //@TODO: remove after development
            //set up a fake installation to work with
            $inst = new Installation();
            $inst->url = "http://example.com";
            $inst->copyright_info = "CC-BY";
            $inst->save();
            
            
            $blocks = unserialize(get_option('blocks'));
            $blocks[] = 'CommonsOriginalInfoBlock';
            set_option('blocks', serialize($blocks));
            
        }
        
        public function hookUninstall()
        {

            $db = get_db();

            $sql = "DROP TABLE IF EXISTS `$db->Installation`,
            		`$db->InstallationContextExhibit`,
            		`$db->InstallationContextExhibitSection`,
            		`$db->InstallationContextExhibitPage`,
            		`$db->InstallationContextCollection`,
            		`$db->InstallationItem` ;
            ";
          
            $db->exec($sql);
        }
        
        public function hookInstallationBrowseSql($select, $params)
        {
            if(isset($_GET['unapproved']) && $_GET['unapproved'] == true) {
                $select->where('added IS NULL');
            }
_log($select);
            return $select;
        }
  
        
    }
} else {
    
    class InstallationsPlugin
    {
        
        protected $_hooks = array('install', 'uninstall');
        protected $_filters = array();
        protected $_options = null;
        
        public function install()
        {
            $db = get_db();
            //Installation table
            $sql = "
            CREATE TABLE IF NOT EXISTS `$db->Installation` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `url` text NULL,
              `admin_email` text NULL,
              `title` text NULL,
              `description` text NULL,
              `key` text NULL,
              `import_url` text NULL,
              `last_import` timestamp NULL DEFAULT NULL,
              `added` timestamp NULL DEFAULT NULL,
              `copyright_info` text,
              `author_info` text NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM ;
            
            ";
            $db->query($sql);
            //InstallationCollection table
            $sql = "
            CREATE TABLE IF NOT EXISTS `$db->InstallationCollection` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `installation_id` int(10) unsigned NOT NULL,
              `orig_id` int(10) unsigned NOT NULL,
              `url` text NULL,
              `title` text NULL,
              `description` text NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM ;
            ";
                    
            $db->query($sql);
            
            //InstallationExhibit table
            $sql = "
            CREATE TABLE IF NOT EXISTS `$db->InstallationExhibit` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `installation_id` int(10) unsigned NOT NULL,
              `orig_id` int(10) unsigned NOT NULL,
              `url` text NULL,
              `title` text NULL,
              `description` text NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM ;
            ";
            
            $db->query($sql);
            //InstallationItem table
            $sql = "
            CREATE TABLE IF NOT EXISTS `$db->InstallationItem` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `item_id` int(10) unsigned NOT NULL,
              `installation_id` int(10) unsigned NOT NULL,
              `orig_id` int(10) unsigned NOT NULL,
              `url` text NULL,
              `license` text NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM ;
            ";
            $db->query($sql);
      
            
            $prop = get_db()->getTable('RecordRelationsProperty')->findByVocabAndPropertyName(SIOC, 'has_container');
            if(empty($prop)) {
                $propData = array(
                    'namespace_prefix' => 'sioc',
                    'namespace_uri' => SIOC,
                    'properties' => array(
                        array(
                            'local_part' => 'has_container',
                            'label' => 'Has container',
                            'description' => 'The Container to which this Item belongs.'
                        )
                    )
                );
                record_relations_install_properties(array($propData));
            }
                    
            
            //@TODO: remove after development
            //set up a fake installation to work with
            $inst = new Installation();
            $inst->url = "http://example.com";
            $inst->copyright_info = "CC-BY";
            $inst->save();
            
        }
        
        public function uninstall()
        {
            $db = get_db();
            $sql = "DROP TABLE IF EXISTS `$db->Installation`,
            		DROP TABLE IF EXISTS `$db->InstallationExhibit`,
            		DROP TABLE IF EXISTS `$db->InstallationCollection`,
            		DROP TABLE IF EXISTS `$db->InstallationItem`,
            ";
        }
        
       public function __construct()
    {
        $this->_db = Omeka_Context::getInstance()->getDb();
        $this->_addHooks();
        $this->_addFilters();
    }
    
    /**
     * Set options with default values.
     *
     * Plugin authors may want to use this convenience method in their install
     * hook callback.
     */
    protected function _installOptions()
    {
        $options = $this->_options;
        if (!is_array($options)) {
            return;
        }
        foreach ($options as $name => $value) {
            // Don't set options without default values.
            if (!is_string($name)) {
                continue;
            }
            set_option($name, $value);
        }
    }
    
    /**
     * Delete all options.
     *
     * Plugin authors may want to use this convenience method in their uninstall
     * hook callback.
     */
    protected function _uninstallOptions()
    {
        $options = self::$_options;
        if (!is_array($options)) {
            return;
        }
        foreach ($options as $name => $value) {
            delete_option($name);
        }
    }
    
    /**
     * Validate and add hooks.
     */
    private function _addHooks()
    {
        $hookNames = $this->_hooks;
        if (!is_array($hookNames)) {
            return;
        }
        foreach ($hookNames as $hookName) {
            $functionName = Inflector::variablize($hookName);
            if (!is_callable(array($this, $functionName))) {
                throw new Omeka_Plugin_Exception('Hook callback "' . $functionName . '" does not exist.');
            }
            add_plugin_hook($hookName, array($this, $functionName));
        }
    }
    
    /**
     * Validate and add filters.
     */
    private function _addFilters()
    {
        $filterNames = $this->_filters;
        if (!is_array($filterNames)) {
            return;
        }
        foreach ($filterNames as $filterName) {
            $functionName = Inflector::variablize($filterName);
            if (!is_callable(array($this, $functionName))) {
                throw new Omeka_Plugin_Exception('Filter callback "' . $functionName . '" does not exist.');
            }
            add_filter($filterName, array($this, $functionName));
        }
    }
        
        
    }
    
    
}

