<?php

/**
 * get a link to the original site from which an item came
 *
 * @return string
 */

function sites_link_to_site_for_item($item = null)
{
    $db = get_db();
    if(is_null($item)) {
        $item = get_current_record('item');
    }
    $site = $db->getTable('SiteItem')->findSiteForItem($item->id);
    return "<a href='{$site->url}'>{$site->title}</a>";
}

/**
 * get a random site that has contributed to the commons
 *
 * @return Site
 */

function sites_random_site() 
{
    $sites = get_db()->getTable('Site')->findBy(array('random'=>true));
    return $sites[0];
}

/**
 * get a link back to the original site to push traffic back to it
 *
 * @return string link to original site
 */

function sites_link_to_original_site($site, $text = null)
{
    if(!$text) {
        $text = "Explore the full site";
    }
    return "<a href='{$site->url}'>$text</a>";
}

/**
 * get a random item from the site
 *
 * @return Item
 */

function sites_random_site_item($site)
{
    $params = array(
        'random' => true,
        'limit' => 1
    );
    $items = get_db()->getTable('Site')->findItemsForSite($site, $params);
    return isset($items[0]) ? $items[0] : false;
}

/**
 * get the site's logo from its branding info
 *
 * @return string the <img> to display
 */

function sites_site_logo($site)
{
    if(isset($site->commons_settings['logo'])) {
        return "<img id='sites-logo' src='" . $site->commons_settings['logo'] . "'/>";
    }
    return '';
}



