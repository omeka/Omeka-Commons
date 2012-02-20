<?php

class CommonsApi_ImportJob extends Omeka_JobAbstract
{

    public function perform()
    {

        $data = json_decode($_POST['data'], true);
        try {
            $import = new CommonsApiImport();
        } catch (Exception $e) {
            _log($e);
        }
        $installations = get_db()->getTable('Installation')->findBy(array('url'=> $data['installation_url']));
        $installation = $installations[0];
        $import->installation_id = $installation->id;
        $import->time = time();

        try {
            $importer = new CommonsApi_Importer($data);
        } catch (Exception $e) {
            _log($e);
        }

        if(isset($data['collections'])) {

            foreach($data['collections'] as $collectionData) {
                try {
                    $importer->processContext($collectionData, 'Collection');
                } catch (Exception $e) {
                    _log($e);
                }
            }

        }

        if(isset($data['exhibits'])) {
            foreach($data['exhibits'] as $index=>$exhibitData) {
                try {
                    $importer->processContext($data['exhibits'][$index]['exhibit'], 'Exhibit');
                    $importer->processContext($data['exhibits'][$index]['section'], 'ExhibitSection');
                    $importer->processContext($data['exhibits'][$index]['page'], 'ExhibitSectionPage');
                } catch (Exception $e) {
                    _log($e->getMessage());
                }
            }
        }

        try {
            if(!empty($data['items'])) {
                foreach($data['items'] as $item) {
                    $importer->processItem($item);
                }

            }
        } catch (Exception $e) {
            _log($e);
        }
        $import->status = serialize($importer->response);
        $import->save();

    }





}