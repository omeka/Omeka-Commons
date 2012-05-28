<?php

define('SITES_PLUGIN_DIR', dirname(__FILE__));
require_once SITES_PLUGIN_DIR . '/helpers/functions.php';
require_once SITES_PLUGIN_DIR . '/helpers/ContextFunctions.php';
require_once PLUGIN_DIR . '/RecordRelations/includes/models/RelatableRecord.php';
require_once PLUGIN_DIR . '/Sites/SitesPlugin.php';
require_once PLUGIN_DIR . '/Sites/libraries/blocks/CommonsOriginalInfoBlock.php';
require_once PLUGIN_DIR . '/Sites/libraries/blocks/CommonsSiteInfoBlock.php';
$sitesPlugin = new SitesPlugin();
$sitesPlugin->setUp();