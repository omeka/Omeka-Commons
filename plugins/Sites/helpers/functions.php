<?php

function sites_link_to_site_for_item($item = null)
{

    $db = get_db();
    if(is_null($item)) {
        $item = get_current_item();
    }

    $site = $sitesTable = $db->getTable('SiteItem')->findSiteForItem($item->id);

    return sites_link_to_site($site);
}

function sites_link_to_site($site = null)
{
    $url = "sites/display-case/show/id/" . $site->id;
    return "<a href='$url'>{$site->title}</a>";
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