<?php



function sites_loop_sites()
{
    return loop_records('sites', sites_get_sites_for_loop(), 'sites_set_current_site');
}

function sites_get_sites_for_loop()
{
    return __v()->sites;
}

function sites_set_sites_for_loop($sites)
{
    __v()->sites = $sites;
}

function sites_get_current_site()
{
    return __v()->site;
}

function sites_set_current_site($site)
{
    __v()->site = $site;
}
/**
 * get a link to the original site from which an item came
 *
 * @return string
 */

function sites_link_to_site_for_item($item = null)
{

    $db = get_db();
    if(is_null($item)) {
        $item = get_current_item();
    }

    $site = $db->getTable('SiteItem')->findSiteForItem($item->id);
    return sites_link_to_site($site);
}

/**
 * get a random site that has contributed to the commons
 *
 * @return Site
 */

function sites_random_site() {

    $sites = get_db()->getTable('Site')->findBy(array('random'=>true));
    return $sites[0];
}

/**
 * get a link to a site display case in the commons
 *
 * @return string a link to the site display case in the commons
 */

function sites_link_to_site($site = null, $text = null)
{
    if(!$text) {
        $text = $site->title;
    }
    $url = uri('/sites/display-case/show/id/' . $site->id);
    return "<a href='$url'>$text</a>";
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
        //'hasImage' => true,
        'random' => true,
        'limit' => 1
    );
    return get_db()->getTable('Site')->findItemsForSite($site, $params);
}

/**
 * get the site from which an item came
 *
 * @return Site
 */

function sites_site_for_item($item)
{
    $db = get_db();
    return $db->getTable('SiteItem')->findSiteForItem($item->id);
}

/**
 * get the customized css for a site
 * experimental
 * @return string the customized css
 */


function sites_site_css($site)
{
    return $site->css;
}

/**
 * get the site's logo from its branding info
 *
 * @return string the <img> to display
 */

function sites_site_logo($site)
{
    return "<img id='sites-logo' src='" . $site->branding['logo'] . "'/>";
}

/**
 * get the site's banner image from its branding info
 *
 * @return string the <img> to display
 */

function sites_site_banner($site)
{
    return "<img id='sites-banner' src='" . $site->branding['banner'] . "'/>";
}

