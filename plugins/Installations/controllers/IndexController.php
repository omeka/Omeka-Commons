<?php

class Installations_IndexController extends Omeka_Controller_Action
{
    
    protected $_modelClass = 'Installation';
    
    public function indexAction()
    {
        $this->redirect->gotoSimple('browse');
        return;
    }
    
    public function approveAction()
    {
        $db = get_db();
        $id = $this->getRequest()->getParam('id');
        $installation = $db->getTable('Installation')->find($id);
        $installation->added = Zend_Date::now()->toString('yyyy-MM-dd HH:mm:ss');
        $installation->save();
       // $this->sendApprovalEmail($installation);
        $responseArray = array('id' => $id, 'added'=>$installation->added);
        $this->_helper->json(json_encode($responseArray));
    }
    
    private function sendApprovalEmail($installation)
    {
        $to = $installation->admin_email;
        
        $subject = "Omeka Commons participation approved!";
        $message = "Thank you for participating in the Omeka Commons. blah blah blah\n";
        $message .= "Your API key to let your Omeka installation is: \n";
        $message .= "something \n";
        $message .= "Copy and paste this key into the configuration information for the Commons Plugin";
        $message .= "in your Omeka site: " . $installation->url . "/admin/plugins/config?name=Commons";
        return mail($to, $subject, $message);
    }
    
}