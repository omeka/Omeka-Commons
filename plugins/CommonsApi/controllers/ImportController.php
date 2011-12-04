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
                    $importer->processContext($collectionData, 'Collection');
                } catch (Exception $e) {
                    _log($e->getMessage());
                }
            }

        }
_log('done collections');

        if(isset($data['exhibits'])) {
            foreach($data['exhibits'] as $index=>$exhibitData) {
                try {
                    $importer->processContext($data['exhibits'][$index]['exhibit'], 'Exhibit');
                } catch (Exception $e) {
                    _log($e->getMessage());
                }
            }
        }
_log('done exhibits');
        if(isset($data['exhibits'])) {
            foreach($data['exhibits'] as $index=>$exhibitData) {
                try {
                    $importer->processContext($data['exhibits'][$index]['section'], 'ExhibitSection');
                } catch (Exception $e) {
                    _log($e->getMessage());
                }
            }
        }
_log('done exhibit sections');
    
        if(isset($data['exhibits'])) {
            foreach($data['exhibits'] as $index=>$exhibitData) {
                try {
                    $importer->processContext($data['exhibits'][$index]['page'], 'ExhibitSectionPage');
                } catch (Exception $e) {
                    _log($e->getMessage());
                }
            }
        }
_log('done exhibit section pages');
// */
_log('before items');
        try {
            if(!empty($data['items'])) {
                $importer->processItem($data['items'][0]);
            }
        } catch (Exception $e) {
            _log($e->getMessage());
        }
        $this->_helper->json($importer->response);
    }
}