<?php

class Sites_DisplayCaseController extends Omeka_Controller_Action
{

    protected $_modelClass = 'Site';

    public function init()
    {
        $this->_helper->db->setDefaultModelName('Site');
    }

    public function showAction()
    {
        $db = $this->getDb();
        $id = $this->getRequest()->getParam('id');
        $site = $db->getTable('Site')->find($id);

        $items = $db->getTable('SiteItem')->findItemsBy(array('site_id' => $id), 3);
        $collections = $db->getTable('SiteContext_Collection')->findBy(array('site_id'=>$id));
        $exhibits = $db->getTable('SiteContext_Exhibit')->findBy(array('site_id'=>$id));

        $params = array(
            'subject_record_type'=>'Site',
            'subject_record_id' => $site->id,
            'object_record_type' => 'Tag'
        );

        $tags = $db->getTable('RecordRelationsRelation')->findObjectRecordsByParams($params);
        $this->view->site = $site;
        $this->view->items = $items;
        $this->view->SiteContext_Collections = $collections;
        $this->view->SiteContext_Exhibits = $exhibits;
        $this->view->tags = $tags;
    }

}