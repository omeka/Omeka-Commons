<?php


class CommonsOriginalInfoBlock extends Blocks_Block_Abstract
{
    
    const name = "Commons Original Info";
    const description = "Display the original context for an item";
    const plugin = "Installations";
    
    public $installationItem;
    
    public function render()
    {
        $installation = $this->findInstallation();
        $installationItem = $this->findInstallationItem();
        $collections = $this->findInstallationCollections();
        
        $html = "<div class='block'>";
        $html .= "<h2>Original Site</h2>";
        $html .= "<p><a href='" . $installation->url . "'>" . $installation->title . "</a></p>";
        
        if(!empty($collections)) {
            $html .= "<h2>Collection(s)</h2>";
            foreach($collections as $collection) {
                $html .= "<p><a href='" . $collection->url . "'>" . $collection->title . "</a>: ";
                $html .= $collection->description . "</p>";
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
        
        $installation = $db->getTable('InstallationItem')->fetchInstallationForItem($params['id']);
        return $installation;
    }
    
    private function findInstallationItem()
    {
        $params = $this->request->getParams();
        $this->installationItem = get_db()->getTable('InstallationItem')->fetchForItemId($params['id']);
        return $this->installationItem;
        
    }
    
    private function findInstallationCollections()
    {
        $db = get_db();
        $has_collection = $db->getTable('RecordRelationsProperty')->findByVocabAndPropertyName(SIOC, 'has_container');

        $relParams = array(
            'subject_id' => $this->installationItem->id,
            'subject_record_type' => 'InstallationItem',
            'property_id' => $has_collection->id,
            'object_record_type' => 'InstallationCollection'
        );

        return $db->getTable('RecordRelationsRelation')->findObjectRecordsByParams($relParams);
        
    }
    
}

