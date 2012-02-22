<?php

define('SITES_PLUGIN_DIR', dirname(__FILE__));
require_once SITES_PLUGIN_DIR . '/helpers/functions.php';
require_once PLUGIN_DIR . '/RecordRelations/includes/models/RelatableRecord.php';
require_once PLUGIN_DIR . '/Sites/SitesPlugin.php';
require_once PLUGIN_DIR . '/Sites/libraries/blocks.php';
$sitesPlugin = new SitesPlugin();
$sitesPlugin->setUp();