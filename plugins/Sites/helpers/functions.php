<?php

function sites_link_to_site_for_item($item = null)
{

    $db = get_db();
    if(is_null($item)) {
        $item = get_current_item();
    }

    $site = $db->getTable('SiteItem')->findSiteForItem($item->id);

    return sites_link_to_site($site);
}

function sites_random_site() {

    $sites = get_db()->getTable('Site')->findBy(array('random'=>true));
    return $sites[0];
}

function sites_link_to_site($site = null, $text = null)
{
    if(!$text) {
        $text = $site->title;
    }
    $url = uri('/sites/display-case/show/id/' . $site->id);
    return "<a href='$url'>$text</a>";
}

function sites_link_to_original_site($site)
{
    return "<a href='{$site->url}'>Explore the full site</a>";
}

function sites_random_site_item($site)
{
    $params = array(
        'hasImage' => true,
        'random' => true,
        'limit' => 1
    );
    return get_db()->getTable('Site')->findItemsForSite($site, $params);
}

function sites_site_for_item($item)
{
    $db = get_db();
    return $db->getTable('SiteItem')->findSiteForItem($item->id);
}

function sites_site_css($site)
{
    return $site->css;
}

function sites_site_logo($site)
{
    return "<img id='sites-logo' src='" . $site->logo_url . "'/>";
}

