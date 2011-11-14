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

        try {
            $importer = new CommonsApi_Importer($data);
_log('ok1');
        } catch (Exception $e) {
            _log($e->getMessage());
        }
_log('ok2');
        try {
            $importer->processItem($data['items'][0]);
_log('ok3');
        } catch (Exception $e) {
            _log($e->getMessage());
        }
        
    }
}