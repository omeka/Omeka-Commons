<?php



class Sites_DisplayCaseController extends Omeka_Controller_Action
{

    protected $_modelClass = 'Site';


    public function showAction()
    {
        $db = $this->getDb();
        $id = $this->getRequest()->getParam('id');
        $site = $db->getTable('Site')->find($id);

        $items = $db->getTable('SiteItem')->findItemsBy(array('site_id' => $id));

        $collections = $db->getTable('SiteContext_Collection')->findBy(array('site_id'=>$id));
        $exhibits = $db->getTable('SiteContext_Exhibit')->findBy(array('site_id'=>$id));
        $entity = $site->getEntity();
        $tags = $db->getTable('Tag')->findBy(array(
            'entity' => $entity
        ));
        $this->view->site = $site;
        $this->view->items = $items;
        $this->view->collections = $collections;
        $this->view->exhibits = $exhibits;
        $this->view->tags = $tags;
    }



}