<?php

class CommonsApi_ImportJob extends Omeka_JobAbstract
{
    protected $data;
    protected $import;

    public function perform()
    {
        $data = json_decode($this->data, true);
        // $import = $this->import;
        $sites = get_db()->getTable('Site')->findBy(array('url'=> $data['site_url']), 1);
        $errors = false;
        if(empty($sites)) {
            // $import->status['status'] = 'fail';
            // $import->status['messages'] = 'Invalid Site URL';
            $errors = true;
            _log("Site " . $data['site_url'] . " does not exist.");
        }

        $site = $sites[0];
        // $import->site_id = $site->id;

        if(is_null($site->added)) {
            // $import->status['status'] = 'fail';
            // $import->status['messages'] = 'Site not yet approved. Check back later';
            $errors = true;
            _log("Site " . $data['site_url'] . " not yet approved.");
        }

        //check that the keys match!
        if($data['key'] != $site->key) {
            _log('invalid key: ' . $data['site_url']);
            // $import->status['status'] = 'fail';
            // $import->status['messages'] = 'Invalid key';
            $errors = true;
            _log("Site " . $data['site_url'] . " has a bad key: " . $data['key']);
        }
        if($errors) {
            // $import->save();
            return false;
        }

        $importer = new CommonsApi_Importer($data, $site);

        if(isset($data['deleteItem'])) {
            $params = array(
                'site_id' => $site->id,
                'orig_id' => $data['deleteItem'],

            );
            $items = get_db()->getTable('SiteItem')->findItemsBy($params);
            $item = $items[0];
            $item->public = false;
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
        //$import->status = $importer->response;
        //_log(print_r($import->status, true));
        //$import->save();

        return true;
    }

    protected function setImport($importArray)
    {
        //when the import arrives, it has been converted back to an array, so dig the real import record back up
       // $this->import = get_db()->getTable('CommonsApiImport')->find($importArray['id']);
    }

    protected function setData($data)
    {
        $this->data = $data;
    }
}