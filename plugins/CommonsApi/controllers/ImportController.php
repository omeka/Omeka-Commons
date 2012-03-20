<?php

class CommonsApi_ImportController extends Omeka_Controller_Action
{
    public function indexAction()
    {
        $jobDispatcher = Zend_Registry::get('job_dispatcher');
        //need to create the import here, so I can pass the id back to the originating site so they can see the status
        $import = new CommonsApiImport();
        $import->time = time();
        $import->status = array();
        $import->save();
        $options = array(
            'data' => $_POST['data'],
            //'import' => $import
        );
        $jobDispatcher->send('CommonsApi_ImportJob', $options);
       // $response = array('importId'=>$import->id);
        //$this->_helper->json($response);
    }
}