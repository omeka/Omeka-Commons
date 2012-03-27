<?php

class CommonsApi_ImportController extends Omeka_Controller_Action
{
    public $importer;

    public function init()
    {
        $this->importer = new CommonsApi_Importer($_POST['data']);

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
        $data = json_decode($_POST['data'], true);

        if(isset($data['configSite'])) {
            $this->importer->processSite();
        }

        if(isset($data['collections'])) {
            foreach($data['collections'] as $collectionData) {
                try {
                    $this->importer->processContext($collectionData, 'Collection');
                } catch (Exception $e) {
                    _log($e);
                }
            }
        }

        if(isset($data['exhibits'])) {
            foreach($data['exhibits'] as $index=>$exhibitData) {
                try {
                    $this->importer->processContext($data['exhibits'][$index]['exhibit'], 'Exhibit');
                    $this->importer->processContext($data['exhibits'][$index]['section'], 'ExhibitSection');
                    $this->importer->processContext($data['exhibits'][$index]['page'], 'ExhibitSectionPage');
                } catch (Exception $e) {
                    _log($e);
                }
            }
        }

        if(!empty($data['items'])) {
            foreach($data['items'] as $item) {
                try {
                    $this->importer->processItem($item);
                } catch (Exception $e) {
                    _log($e);
                }
            }
        }

        $responseArray = $this->importer->status;
        $response = json_encode($responseArray);
        $this->_helper->json($response);
    }
}