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



        $sites = get_db()->getTable('Site')->findBy(array('url'=> $data['site_url']));
        $site = $sites[0];

        //check that the keys match!
        if($data['key'] != $site->key) {
            _log('invalid key: ' . $data['site_url']);
            return;
        }

        $import->site_id = $site->id;
        $import->time = time();



        try {
            $importer = new CommonsApi_Importer($data);
        } catch (Exception $e) {
            _log($e);
        }

        if(isset($data['deleteItem'])) {
_log('delete');
            $params = array(
                'site_id' => $site->id,
                'orig_id' => $data['deleteItem'],

            );
            $items = get_db()->getTable('SiteItem')->findItemsBy($params);
            $item = $items[0];
            $item->public = false;
_log(print_r($item, true));
_log('end delete');
            $item->save();
        }

        $importer->processSite($data['site']);

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
                    _log($e);
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