<?php

class CommonsApi_ImportController extends Omeka_Controller_Action
{
    
    public function indexAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $jobDispatcher = Zend_Registry::get('job_dispatcher');
        $jobDispatcher->send('CommonsApi_ImportJob', array() );
        //$this->_helper->json($response);
    }
    
    public function statusAction()
    {
        //$this->_helper->viewRenderer->setNoRender();
        //$data = $_POST['data'];
        $data['installation_url'] = "http://localhost/omeka";
        $import = $this->getTable('CommonsApiImport')->findMostRecent($data['installation_url']);
        $status = unserialize($import->status);
        $this->_helper->json($status);
    }
    
}