<?php


class CommonsOriginalInfoBlock extends Blocks_Block_Abstract
{

    const name = "Commons Original Info";
    const description = "Display the original context for an item";
    const plugin = "Installations";

    public $installationItem;

    public function render()
    {
        $db = get_db();
        $installation = $this->findInstallation();
        $installationItem = $this->findInstallationItem();
        $has_container = $db->getTable('RecordRelationsProperty')->findByVocabAndPropertyName(SIOC, 'has_container');
        $collections = $this->findInstallationContexts($has_container, 'InstallationContext_Collection');
        $exhibits = $this->findInstallationContexts($has_container, 'InstallationContext_Exhibit');
        $exhibitSections = $this->findInstallationContexts($has_container, 'InstallationContext_ExhibitSection');
        $exhibitSectionPages = $this->findInstallationContexts($has_container, 'InstallationContext_ExhibitSectionPage');
        $html = "<div class='block'>";
        $html .= "<h2>Original Site Info</h2>";
        $html .= "<p><a href='" . $installation->url . "'>" . $installation->title . "</a></p>";
        $html .= "<p>". $installation->description . "</p>";
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

        $html .= "<p><a href='{$installationItem->url}'>View Original</a>";

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

    private function findInstallation()
    {
        $db = get_db();
        $params = $this->request->getParams();
        $installation = $db->getTable('InstallationItem')->findInstallationForItem($params['id']);
        return $installation;
    }

    private function findInstallationItem()
    {
        $params = $this->request->getParams();
        $this->installationItem = get_db()->getTable('InstallationItem')->findByItemId($params['id']);
        return $this->installationItem;

    }


/**
 * this should supercede methods below
 *
 */

     private function findInstallationContexts($pred = null, $objectContextType)
     {
         $db = get_db();
         if(is_null($pred)) {
             $pred = $db->getTable('RecordRelationsProperty')->findByVocabAndPropertyName(SIOC, 'has_container');
         }

        $relParams = array(
            'subject_id' => $this->installationItem->id,
            'subject_record_type' => 'InstallationItem',
            'property_id' => $pred->id,
            'object_record_type' => $objectContextType
        );

        return $db->getTable('RecordRelationsRelation')->findObjectRecordsByParams($relParams);


     }

}

