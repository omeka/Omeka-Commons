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
        
        $this->sendApprovalEmail($installation);
        $responseArray = array('id' => $id, 'added'=>$installation->added);
        $this->_helper->json(json_encode($responseArray));
    }
    
    public function keyAction()
    {
        $token = $this->getRequest()->getParam('token');
        if(!$token) {
            exit;
        }
        $instTokens = $this->getDb()->getTable('InstallationToken')->findBy(array('token'=>$token), 1);
        
        $instToken = $instTokens[0];
        if($token != $instToken->token ) {
            exit;
        }
        
        if (time() > $instToken->expiration) {
            exit;
        } else {
            $this->view->assign('debug', array(time(), $instToken->expiration));
            $installation = $this->getDb()->getTable('Installation')->find($instToken->installation_id);
            $this->view->assign('installation', $installation);
        }

    }
    
    private function sendApprovalEmail($installation)
    {
        
        $tokenUrl = $this->createTokenUrl($installation);
        $to = $installation->admin_email;
        $subject = "Omeka Commons participation approved!";
        $message = "Thank you for participating in the Omeka Commons. blah blah blah
        You will need to enter your Omeka Commons API key into the configuration form
        of the Commons plugin you installed on your Omeka site.
        
        You can obtain your API key anytime in the next seven days by following
        this link: $tokenUrl
        
        Copy and paste the API key into the API key input on the form and save the configuration.
        You will then be able to send individual items and entire collections to be preserved in the Commons.
        When you do so, some basic information about your items, collections, and exhibits will be
        available in the commons to help others discover your material and incorporate it into their research
        and interests.
        
        ";
        
        //return mail($to, $subject, $message);
    }
    
    private function createTokenUrl($installation)
    {
        $token = sha1("tOkenS@1t" . microtime());
        require_once HELPERS;
        $tokenUrl = uri('installations/key') . "?token=" . $token;

        $instToken = new InstallationToken();

        $instToken->installation_id = $installation->id;
        $instToken->token = $token;
        $instToken->save();
        return $tokenUrl;
    
    }
}