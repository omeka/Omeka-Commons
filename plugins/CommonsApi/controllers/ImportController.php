<?php

class CommonsApi_ImportController extends Omeka_Controller_Action
{
    
    public function indexAction()
    {
        
        
        
        //$this->_helper->viewRenderer->setNoRender();
        $data = json_decode($_POST['data'], true);

        try {
            $importer = new CommonsApi_Importer($data);
        } catch (Exception $e) {
            _log($e->getMessage());
        }
        
        if(isset($data['collections'])) {
            
            foreach($data['collections'] as $collectionData) {
                try {
                    $importer->processCollection($collectionData);
                } catch (Exception $e) {
                    _log($e->getMessage());
                }
            }

        }
        
        try {
            $importer->processItem($data['items'][0]);
        } catch (Exception $e) {
            _log($e->getMessage());
        }
        $this->_helper->json($importer->response);
    }
}