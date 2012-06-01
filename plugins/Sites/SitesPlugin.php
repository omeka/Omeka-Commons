<?php


class SitesPlugin extends Omeka_Plugin_Abstract
{

    protected $_hooks = array(
        'install',
     //   'uninstall',
        'site_browse_sql',
        'public_theme_header',
        'define_routes'

    );
    protected $_filters = array(
        'admin_navigation_main'

    );
    protected $_options = null;

    public function hookPublicThemeHeader()
    {
        queue_css('sites');
    }

    public function filterAdminNavigationMain($tabs)
    {
        $tabs['Sites'] = uri('sites/index/browse');
        return $tabs;
    }

    public function hookInstall()
    {
        $db = get_db();
        //Site table
        $sql = "
        CREATE TABLE IF NOT EXISTS `$db->Site` (
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
          `css` text NULL,
          `branding` text NULL,
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

        blocks_register_blocks(array('CommonsOriginalInfoBlock', 'CommonsSiteInfoBlock' ));
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
                `$db->SiteToken` ;
        ";

        $db->query($sql);

        blocks_unregister_blocks(array('CommonsOriginalInfoBlock', 'CommonsSiteInfoBlock' ));
    }

    public function hookDefineRoutes($router)
    {
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

    public function hookSiteBrowseSql($select, $params)
    {
        if(isset($_GET['unapproved']) && $_GET['unapproved'] == true) {
            $select->where('added IS NULL');
        }
        return $select;
    }


}

