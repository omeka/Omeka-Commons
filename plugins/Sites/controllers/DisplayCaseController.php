<?php

class Sites_DisplayCaseController extends Omeka_Controller_AbstractActionController
{

    public function init()
    {
        $this->_helper->db->setDefaultModelName('Site');
    }

    public function showAction()
    {
        $db = $this->_helper->db;
        $id = $this->getRequest()->getParam('id');
        $site = $this->_helper->db->getTable('Site')->find($id);
        $items = $this->_helper->db->getTable('SiteItem')->findItemsBy(array('site_id' => $id), 3);
        $collections = $this->_helper->db->getTable('SiteContext_Collection')->findBy(array('site_id'=>$id));
        $exhibits = $this->_helper->db->getTable('SiteContext_Exhibit')->findBy(array('site_id'=>$id));

        $params = array(
            'subject_record_type'=>'Site',
            'subject_id' => $site->id,
            'object_record_type' => 'Tag'
        );

        $tags = $this->_helper->db->getTable('RecordRelationsRelation')->findObjectRecordsByParams($params);
        $this->view->site = $site;
        $this->view->items = $items;
        $this->view->site_context_collections = $collections;
        $this->view->site_context_exhibits = $exhibits;
        $this->view->tags = $tags;
    }
}