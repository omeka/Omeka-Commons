<?php

/**
 * display info about the original site for an Item
 */

class CommonsSiteInfoBlock extends Blocks_Block_Abstract
{

    const name = "Commons Site Info";
    const description = "Display info and link to display case";
    const plugin = "Sites";

    public function isEmpty()
    {
        return false;
    }

    public function render()
    {
        $item = __v()->item;
        $site = sites_site_for_item($item);
        $html = "<div id='site-info'>";
        $html .= "<p>From " . sites_link_to_site($site) . "</p>";
        $html .= sites_site_logo($site);
        $html .= "</div>";

        return $html;
    }

    static function prepareConfigOptions($formData)
    {
        return false;
    }

    static function formElementConfigData()
    {
        return false;
    }
}
