<?php


class CommonsOriginalInfoBlock extends Blocks_Block_Abstract
{

    const name = "Commons Original Info";
    const description = "Display the original context for an item";
    const plugin = "Sites";

    public $siteItem;

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
        $html .= "<h2>Original Site Info</h2>";
        $html .= "<p>". sites_link_to_original_site($site) . "</p>";
        $html .= "<p>". $site->description . "</p>";
        if(!empty($collections)) {
            $html .= "<h2>Collection(s)</h2>";
            foreach($collections as $collection) {
                $html .= "<p><a href='" . $collection->url . "'>" . $collection->title . "</a>: ";
                $html .= $collection->description . "</p>";
            }
        }
        if(!empty($exhibits)) {
            $html .= "<h2>Exhibit(s)</h2>";
            foreach($exhibits as $exhibit) {
                $html .= "<p><a href='" . $exhibit->url . "'>" . $exhibit->title . "</a>: ";
                $html .= $exhibit->description . "</p>";
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


/**
 * this should supercede methods below
 *
 */

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
            'object_record_type' => $objectContextType
        );

        return $db->getTable('RecordRelationsRelation')->findObjectRecordsByParams($relParams);


     }

}

