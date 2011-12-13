<?php

class CommonsLicenseInfoBlock extends Blocks_Block_Abstract
{
    const name = "Commons License Info";
    const description = "Shows the license info for current Item";
    const plugin = "Installaltions";
    
    public function render()
    {
        $db = get_db();
        $item = get_current_item();
        $instItem = $db->getTable('InstallationItem')->findByItemId($item->id);
        $html = "<div class='block'>";
        $html .= "<h2>License</h2>";
        $html .= "<p>The original site has made this available under a";
        $html .= $this->licenseLink($instItem->license);
        $html .= " license.</p>";
        $html .= "</div>";
        return $html;
        
    }
    
    private function licenseLink($license, $display = array('button'))
    {
        $licenseData = array(
            'cc-0' => array(
            	'button'=> WEB_ROOT . '/plugins/Installations/views/shared/images/cc-0.png',
            	'link'=>'http://creativecommons.org/licenses/cc-zero/3.0',
            	'short_label'=>'CC-0',
            	'long_label'=>'Public Domain Dedication'
            ),
        	
        	'by' => array(
            	'button'=> WEB_ROOT . '/plugins/Installations/views/shared/images/by.png',
            	'link'=>'http://creativecommons.org/licenses/by/3.0',
            	'short_label'=>'BY',
            	'long_label'=>'Attribution'
            ),
            'by-nd' => array(
            	'button'=> WEB_ROOT . '/plugins/Installations/views/shared/images/by-nd.png',
            	'link'=>'http://creativecommons.org/licenses/by-nd/3.0',
            	'short_label'=>'BY-ND',
            	'long_label'=>'Attribution-NoDerivs'
            ),
            'by-nc-sa' => array(
            	'button'=> WEB_ROOT . '/plugins/Installations/views/shared/images/by-nc-sa.png',
            	'link'=>'http://creativecommons.org/licenses/by-nc-sa/3.0',
            	'short_label'=>'BY-NC-SA',
            	'long_label'=>'Attribution-NonCommercial-ShareAlike'
            ),
            'by-sa' => array(
            	'button'=> WEB_ROOT . '/plugins/Installations/views/shared/images/by-sa.png',
            	'link'=>'http://creativecommons.org/licenses/by-sa/3.0',
            	'short_label'=>'BY-SA',
            	'long_label'=>'Attribution-ShareAlike'
            ),
            'by-nc' => array(
            	'button'=> WEB_ROOT . '/plugins/Installations/views/shared/images/by-nc.png',
            	'link'=>'http://creativecommons.org/licenses/by-nc/3.0',
            	'short_label'=>'BY-NC',
            	'long_label'=>'Attribution-NonCommercial'
            ),
            'by-nc-nd' => array(
            	'button'=> WEB_ROOT . '/plugins/Installations/views/shared/images/by-nc-nd.png',
            	'link'=>'http://creativecommons.org/licenses/by-nc-nd/3.0',
            	'short_label'=>'BY-NC-ND',
            	'long_label'=>'Attribution-NonCommercial-NoDerivs'
            ),
        );
        
        $html = "<a href='" . $licenseData[$license]['link'] . "'>";
        if(in_array('button', $display)) {
            $html .= "<img class='installations-license-block' src='" . $licenseData[$license]['button'] . "'/>";
        }
        if(in_array('short_label', $display )) {
            $html .= $licenseData[$license]['short_label'];
        }
        if(in_array('long_label', $display )) {
            $html .= $licenseData[$license]['long_label'];
        }
        $html .= "</a>";
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
       // $collections = $this->findInstallationCollections();

        $html = "<div class='block'>";
        $html .= "<h2>Original Site</h2>";
        $html .= "<p><a href='" . $installation->url . "'>" . $installation->title . "</a></p>";
        $html .= "<p>". $installation->description . "</p>";
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
        $installation = $db->getTable('InstallationItem')->findInstallationForItem($params['id']);
        return $installation;
    }
    
    private function findInstallationItem()
    {
        $params = $this->request->getParams();
        $this->installationItem = get_db()->getTable('InstallationItem')->findByItemId($params['id']);
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
            'object_record_type' => 'InstallationContext_Collection'
        );

        return $db->getTable('RecordRelationsRelation')->findObjectRecordsByParams($relParams);
        
    }
    
}

