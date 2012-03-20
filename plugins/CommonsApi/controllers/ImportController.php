<?php

class CommonsApi_ImportController extends Omeka_Controller_Action
{
    public $importer;

    public function init()
    {
        $this->importer = new CommonsApi_Importer($_POST['data']);
        //handle errors
    }

    public function deleteItemAction()
    {
        if(isset($this->data['deleteItem'])) {
            $params = array(
                'site_id' => $this->importer->site->id,
                'orig_id' => $this->importer->data['deleteItem'],

            );
            $items = get_db()->getTable('SiteItem')->findItemsBy($params);
            $item = $items[0];
            $item->public = false;
            $item->save();
        }
    }

    public function indexAction()
    {
        $data = $_POST['data'];
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

        if(!empty($data['items'])) {
            foreach($data['items'] as $item) {
                try {
                    $importer->processItem($item);
                } catch (Exception $e) {
                    _log($e);
                }
            }
        }

        //$jobDispatcher->send('CommonsApi_ImportJob', $options);
       // $response = array('importId'=>$import->id);
        //$this->_helper->json($response);
    }
}