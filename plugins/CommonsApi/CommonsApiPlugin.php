<?php
define('COMMONSAPI_PLUGIN_DIR', dirname(__FILE__));
require_once PLUGIN_DIR . '/RecordRelations/includes/models/RelatableRecord.php';

class CommonsApiPlugin extends Omeka_Plugin_Abstract
{

    protected $_hooks = array('install', 'uninstall');
    protected $_filters = array();
    protected $_options = null;

    public function hookInstall()
    {
        $db = get_db();
        $sql = "
            CREATE TABLE `$db->CommonsApiImport` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `site_id` INT UNSIGNED NOT NULL ,
            `time` INT UNSIGNED NOT NULL ,
            `status` TEXT NOT NULL ,
            INDEX ( `site_id` )
            ) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci;
        ";

        $db->exec($sql);
    }

    public function hookUninstall()
    {
        $db = get_db();
        $sql = "DROP TABLE IF EXISTS `$db->CommonsApiImport`";
        $db->exec($sql);
    }



}