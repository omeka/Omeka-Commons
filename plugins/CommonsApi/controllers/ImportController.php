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
        $data = $_GET['data'];
        
       // $data['installation_url'] = "http://localhost/omeka";
      //  $data['key'] = '1a5b8c864eca82f4c8869ca5642d3299240c5494';
        $installation = get_db()->getTable('Installation')->findByUrlKey($data['url'], $data['key']);
        if(!$installation) {
            die();
        }
        $import = $this->getTable('CommonsApiImport')->findMostRecent($data['installation_url']);
        $status = unserialize($import->status);
        $this->_helper->json($status);
    }
    
}