<?php

define('SITES_PLUGIN_DIR', dirname(__FILE__));
require_once(SITES_PLUGIN_DIR . '/helpers/functions.php');
require_once(SITES_PLUGIN_DIR . '/helpers/ContextFunctions.php');

class SitesPlugin extends Omeka_Plugin_AbstractPlugin
{
    protected $_hooks = array(
        'install',
        'uninstall',
        'upgrade',
        'site_browse_sql',
        //'define_routes',
        'public_theme_header',
        'public_items_show',
        'admin_items_show_sidebar',
        'after_insert_user', // a little inappropriate since it isn't relevant to this plugin, but just a cheap shortcut since it shouldn't go in Groups as a general use plugin feature
        'embed_codes_browse_each'
    );
    
    protected $_filters = array(
        'admin_navigation_main'
    );
    
    public function setUp()
    {
        parent::setUp();
        require_once(SITES_PLUGIN_DIR . '/models/SiteContext/Table/Collection.php');
        require_once(SITES_PLUGIN_DIR . '/models/SiteContext/Table/Exhibit.php');
        //require_once(SITES_PLUGIN_DIR . '/models/SiteContext/Table/ExhibitSection.php');
        //require_once(SITES_PLUGIN_DIR . '/models/SiteContext/Table/ExhibitSectionPage.php');
    }
    
    /*
     * For Commons, each new user gets a new gruop
     */
    
    public function hookAfterInsertUser($args)
    {
        $user = $args['record'];
        $group = new Group();        
        $group->visibility = 'closed';
        $group->title = $user->name . "'s Group";
        $group->save();        
        $group->addMember($user, 0, 'is_owner');
    }
    
    public function hookPublicThemeHeader()
    {
        queue_css('sites');
    }

    public function hookPublicItemsShow($args)
    {
        $html = '';
        $html .= $this->_siteContextsHtml($args);
        $html .= $this->_siteInfoHtml($args);
        echo $html;
    }
    
    public function hookAdminItemsShowSidebar($args)
    {
        $contentHtml = $this->_siteContextsHtml($args, array('h-level'=>4));
        $contentHtml .= $this->_siteInfoHtml($args, array('h-level'=>4));
        if(!empty($contentHtml)) {
            $html = '<div class="panel">';
            $html .= '</div>';
            echo $html;            
        }
    }
    
    public function hookEmbedCodesBrowseEach($args)
    {
        $item = $args['item'];
        $site = $this->_db->getTable('SiteItem')->findSiteForItem($item);
        if($site) {
            $html = "<li>";
            $html .= "Site: " . $site->title;
            $html .= "</li>";
            echo $html;            
        }
    }
    
    public function filterAdminNavigationMain($navArray)
    {
        $navArray['Sites'] = array('label'=>'Sites', 'uri'=>url('sites/index') );
        return $navArray;
    }

    public function hookInstall()
    {
        $db = get_db();
        //Site table
        $sql = "
        CREATE TABLE IF NOT EXISTS `$db->Site` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `site_aggregation_id` int(10) unsigned NULL,
          `owner_id` int(10) unsigned NULL,
          `url` text NULL,
          `super_email` text NOT NULL,
          `admin_email` text NOT NULL,
          `admin_name` text NOT NULL,
          `content_summary` text NOT NULL,
          `affiliation` text NOT NULL,
          `join_reason` text NOT NULL,
          `title` text NULL,
          `description` text NULL,
          `key` text NULL,
          `last_import` timestamp NULL DEFAULT NULL,
          `date_approved` timestamp NULL DEFAULT NULL,
          `date_applied` timestamp NULL DEFAULT NULL,
          `copyright_info` text,
          `author_info` text NULL,
          `commons_settings` text NULL,
          `omeka_version` text NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

        ";
        $db->query($sql);

        $sql = "
        CREATE TABLE IF NOT EXISTS `$db->SiteAggregation` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `name` text NULL,
        `description` text NULL,
        PRIMARY KEY (`id`)
        ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci
        ";
        
        $db->query($sql);        
        
        //SiteCollection table
        $sql = "
        CREATE TABLE IF NOT EXISTS `$db->SiteContextCollection` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `site_id` int(10) unsigned NOT NULL,
          `orig_id` int(10) unsigned NOT NULL,
          `url` text NULL,
          `title` text NULL,
          `description` text NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci
        ";

        $db->query($sql);

        //SiteExhibit table
        $sql = "
        CREATE TABLE IF NOT EXISTS `$db->SiteContextExhibit` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `site_id` int(10) unsigned NOT NULL,
          `orig_id` int(10) unsigned NOT NULL,
          `url` text NULL,
          `title` text NULL,
          `description` text NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci
        ";

        $db->query($sql);

        $sql = "
        CREATE TABLE IF NOT EXISTS `$db->SiteContextExhibitSection` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `site_id` int(10) unsigned NOT NULL,
          `site_exhibit_id` int(10) unsigned NOT NULL,
          `orig_id` int(10) unsigned NOT NULL,
          `url` text NULL,
          `title` text NULL,
          `description` text NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci
        ";

        $db->query($sql);

        $sql = "
        CREATE TABLE IF NOT EXISTS `$db->SiteContextExhibitSectionPage` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `site_id` int(10) unsigned NOT NULL,
          `site_section_id` int(10) unsigned NOT NULL,
          `orig_id` int(10) unsigned NOT NULL,
          `url` text NULL,
          `title` text NULL,
          `description` text NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci
        ";


        $db->query($sql);
        //SiteItem table
        $sql = "
        CREATE TABLE IF NOT EXISTS `$db->SiteItem` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `item_id` int(10) unsigned NOT NULL,
          `site_id` int(10) unsigned NOT NULL,
          `orig_id` int(10) unsigned NOT NULL,
          `url` text NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci
        ";
        $db->query($sql);

        $sql = "
        CREATE TABLE IF NOT EXISTS `$db->SiteToken` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `token` text COLLATE utf8_unicode_ci NOT NULL,
          `site_id` int(10) unsigned NOT NULL,
          `expiration` int(13) NOT NULL,
          PRIMARY KEY (`id`),
          UNIQUE KEY `site_ids` (`site_id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ";

        $db->query($sql);

        $sql = "
        CREATE TABLE IF NOT EXISTS `$db->SiteAggregation` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `name` text NULL,
          `description` text NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;        
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

        //put in Europeana properties
        $europeanaProps = array(
              array(
                    'name' => 'Europeana',
                    'description' => 'Europeana relations',
                    'namespace_prefix' => 'europeana',
                    'namespace_uri' => EUROPEANA,
                    'properties' => array(
                        array(
                            'local_part' => 'isShownBy',
                            'label' => 'is shown by',
                            'description' => ''
                        ),
                        array(
                            'local_part' => 'isDisplayedBy',
                            'label' => 'is displayed by',
                            'description' => ''
                        ),
                        array(
                            'local_part' => 'provider',
                            'label' => 'provider',
                            'description' => ''
                        ),
                        array(
                            'local_part' => 'dataProvider',
                            'label' => 'data provider',
                            'description' => ''
                        ),
                    )
                )

          );
        record_relations_install_properties($europeanaProps);

        $commonsProps = array(
              array(
                    'name' => 'Commons',
                    'description' => 'Commons relations',
                    'namespace_prefix' => 'commons',
                    'namespace_uri' => 'http://ns.omeka-commons.org/',
                    'properties' => array(
                        array(
                            'local_part' => 'usesTag',
                            'label' => 'Uses Tag',
                            'description' => 'The subject Site uses the object Tag'
                        ),                    )
                )

          );

        record_relations_install_properties($commonsProps);

        //blocks_register_blocks(array('CommonsOriginalInfoBlock', 'CommonsSiteInfoBlock' ));
    }

    public function hookUninstall()
    {

        $db = get_db();

        $sql = "DROP TABLE IF EXISTS `$db->Site`,
                `$db->SiteContextExhibit`,
                `$db->SiteContextExhibitSection`,
                `$db->SiteContextExhibitPage`,
                `$db->SiteContextCollection`,
                `$db->SiteItem`,
                `$db->SiteFamily`,
                `$db->SiteToken` ;
        ";

        $db->query($sql);

        //blocks_unregister_blocks(array('CommonsOriginalInfoBlock', 'CommonsSiteInfoBlock' ));
    }
    
    public function hookUpgrade($args)
    {
        $old = $args['old_version'];
        $new = $args['new_version'];
        $db = get_db();
        if($new == '1.1') {
            $sql = "
            CREATE TABLE IF NOT EXISTS `$db->SiteFamily` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `name` text NULL,
            `description` text NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
            ";
            
            $db->query($sql);       

            $sql = "
            ALTER TABLE `omeka_sites` CHANGE `site_owner_id` `site_family_id` INT( 10 ) UNSIGNED NULL DEFAULT NULL,
            ALTER TABLE `omeka_sites` ADD `version` TINYTEXT NOT NULL AFTER `author_info`  
            ";
            
            $db->query($sql);
        }
    }

    public function hookDefineRoutes($args)
    {
        $router = $args['router'];
        $router->addRoute(
            'sites-site-route',
            new Zend_Controller_Router_Route(
                'sites/:action/:id',
                array(
                    'module'        => 'sites',
                    'controller'    => 'index',
                    'action'        => 'browse'
                    )
            )
        );
    }

    public function hookSiteBrowseSql($args)
    {
        $select = $args['select'];
        $params = $args['params'];
        if(isset($_GET['unapproved']) && $_GET['unapproved'] == true) {
            $select->where('added IS NULL');
        }
        return $select;
    }

    private function _findSiteContexts($pred, $objectContextType, $siteItemId)
    {
        $db = get_db();
    
        $relParams = array(
                'subject_id' => $siteItemId,
                'subject_record_type' => 'SiteItem',
                'property_id' => $pred->id,
                'object_record_type' => $objectContextType,
                'public' => true
        );
    
        return $db->getTable('RecordRelationsRelation')->findObjectRecordsByParams($relParams);
    }    

    private function _siteContextsHtml($args, $options=array())
    {
        if(isset($options['h-level'])) {
            $hlevel = $options['h-level'];
        } else {
            $hlevel = 2;
        }
                
        
        $item = $args['item'];
        $db = get_db();
        
        $site = $db->getTable('SiteItem')->findSiteForItem($item->id);
        $siteItem = get_db()->getTable('SiteItem')->findByItemId($item->id);
        if(!$siteItem) {
            return;
        }
        $has_container = $db->getTable('RecordRelationsProperty')->findByVocabAndPropertyName(SIOC, 'has_container');
        $collections = $this->_findSiteContexts($has_container, 'SiteContext_Collection', $siteItem->id);
        $exhibits = $this->_findSiteContexts($has_container, 'SiteContext_Exhibit', $siteItem->id);
        $exhibitSections = $this->_findSiteContexts($has_container, 'SiteContext_ExhibitSection', $siteItem->id);
        $exhibitSectionPages = $this->_findSiteContexts($has_container, 'SiteContext_ExhibitSectionPage', $siteItem->id);
        $html = "<div id='site-contexts'>";
        $html .= "<h$hlevel>Original Context</h$hlevel>";
        $html .= "<p><a href='{$site->url}'>". $site->title . "</a></p>";
        $html .= "<p>". $site->description . "</p>";
        $nextHlevel = $hlevel +1;
        if(!empty($collections)) {
            $html .= "<h$nextHlevel>Collection(s)</h$nextHlevel>";
            foreach($collections as $collection) {
                $html .= "<p><a href='" . $collection->url . "'>" . $collection->title . "</a>: ";
                $html .= snippet($collection->description, 0, 100) . "</p>";
            }
        }
        if(!empty($exhibits)) {
            $html .= "<h$nextHlevel>Exhibit(s)</h$nextHlevel>";
            foreach($exhibits as $exhibit) {
                $html .= "<p><a href='" . $exhibit->url . "'>" . $exhibit->title . "</a>: ";
                $html .= metadata($exhibit, array('Dublin Core', 'Description'), array('snippet'=>100)) . "</p>";
            }
        }
        $html .= "<p><a href='{$siteItem->url}'>View Original</a>";
        $html .= "</div>";
        return $html;        
    }
    
    private function _siteInfoHtml($args, $options=array())
    {
        if(isset($options['h-level'])) {
            $hlevel = $options['h-level'];
        } else {
            $hlevel = 2;
        }
        
        $item = $args['item'];
        $db = get_db();
        $site = $db->getTable('SiteItem')->findSiteForItem($item->id);    
        if(!$site) {
            return;
        }    
        $html = "<div id='site-info'>";
        $html .= "<h$hlevel>Explore in Omeka Commons</h$hlevel>";
        $html .= "<p>From " . link_to($site, 'show', $site->title) . "</p>";
        $html .= sites_site_logo($site);
        $html .= "</div>";
        return $html;
    }
}

