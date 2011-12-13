<?php

class CommonsApi_ImportJob extends Omeka_JobAbstract
{
    
    public function perform()
    {

        $data = json_decode($_POST['data'], true);
        try {
            $import = new CommonsApiImport();
        } catch (Exception $e) {
            _log($e->getMessage());
        }
        $installations = get_db()->getTable('Installation')->findBy(array('url'=> $data['installation_url']));
        $installation = $installations[0];
        $import->installation_id = $installation->id;
        $import->time = time();

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
        $import->status = serialize($importer->response);
        $import->save();
_log($response->response);
        
    }
    
    
    
    
    
}