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
        $tags = null;
        $this->view->site = $site;
        $this->view->items = $items;
        $this->view->collections = $collections;
        $this->view->exhibits = $exhibits;
        $this->view->tags = $tags;
    }



}