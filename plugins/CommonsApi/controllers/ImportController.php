<?php

class CommonsApi_ImportController extends Omeka_Controller_Action
{
    /**
     *
     * Pull data from Omeka installations
     */
    public function importerAction()
    {
        
    }
    
    public function indexAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $data = json_decode($_POST['data'], true);
_log('start');
        try {
            $importer = new CommonsApi_Importer($data);
        } catch (Exception $e) {
            _log($e->getMessage());
        }
_log('before processItem');
        try {
            $importer->processItem($data['items'][0]);
        } catch (Exception $e) {
            _log($e->getMessage());
        }
        
        
    }
    
    
    
}