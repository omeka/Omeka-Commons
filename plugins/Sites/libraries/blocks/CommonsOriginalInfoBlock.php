<?php

/**
 * Display information about the Item from the original site info, like collections and exhibits
 *
 * @see Blocks/Blocks_Block_Abstract
 */

class CommonsOriginalInfoBlock extends Blocks_Block_Abstract
{

    const name = "Commons Original Info";
    const description = "Display the original context for an item";
    const plugin = "Sites";

    public $siteItem;

    public function isEmpty()
    {
        return false;
    }


    public function render()
    {
        $db = get_db();

        $site = $this->findSite();
        $siteItem = $this->findSiteItem();
        $has_container = $db->getTable('RecordRelationsProperty')->findByVocabAndPropertyName(SIOC, 'has_container');
        $collections = $this->findSiteContexts($has_container, 'SiteContext_Collection');
        $exhibits = $this->findSiteContexts($has_container, 'SiteContext_Exhibit');
        $exhibitSections = $this->findSiteContexts($has_container, 'SiteContext_ExhibitSection');
        $exhibitSectionPages = $this->findSiteContexts($has_container, 'SiteContext_ExhibitSectionPage');
        $html = "<div class='block'>";
        $html .= "<p>". sites_link_to_original_site($site) . "</p>";
        $html .= "<p>". $site->description . "</p>";
        if(!empty($collections)) {
            $html .= "<h3>Collection(s)</h3>";
            foreach($collections as $collection) {
                $html .= "<p><a href='" . $collection->url . "'>" . $collection->title . "</a>: ";
                $html .= snippet($collection->description, 0, 100) . "</p>";
            }
        }
        if(!empty($exhibits)) {
            $html .= "<h3>Exhibit(s)</h3>";
            foreach($exhibits as $exhibit) {
                $html .= "<p><a href='" . $exhibit->url . "'>" . $exhibit->title . "</a>: ";
                $html .= snippet($exhibit->description, 0, 100) . "</p>";
            }
        }
        $html .= "<p><a href='{$siteItem->url}'>View Original</a>";
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

    private function findSite()
    {
        $db = get_db();
        $params = $this->request->getParams();
        $site = $db->getTable('SiteItem')->findSiteForItem($params['id']);
        return $site;
    }

    private function findSiteItem()
    {
        $params = $this->request->getParams();
        $this->siteItem = get_db()->getTable('SiteItem')->findByItemId($params['id']);
        return $this->siteItem;
    }

    private function findSiteContexts($pred = null, $objectContextType)
    {
        $db = get_db();
        if(is_null($pred)) {
             $pred = $db->getTable('RecordRelationsProperty')->findByVocabAndPropertyName(SIOC, 'has_container');
        }

        $relParams = array(
            'subject_id' => $this->siteItem->id,
            'subject_record_type' => 'SiteItem',
            'property_id' => $pred->id,
            'object_record_type' => $objectContextType,
            'public' => true
            );

        return $db->getTable('RecordRelationsRelation')->findObjectRecordsByParams($relParams);
    }

}

