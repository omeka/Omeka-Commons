<?php

class Sites_IndexController extends Omeka_Controller_AbstractActionController
{

    protected $_browseRecordsPerPage = 10;
    
    public function init()
    {
        $this->_helper->db->setDefaultModelName('Site');
    }
    
    public function approveAction()
    {
        $db = get_db();
        $id = $this->getParam('id');
        $site = $db->getTable('Site')->find($id);
        $site->date_approved = Zend_Date::now()->toString('yyyy-MM-dd HH:mm:ss');
        $site->save();
        try {
            $this->sendApprovalEmail($site);
        } catch (Exception $e) {

        }

        $responseArray = array('id' => $id, 'date_approved'=>$site->date_approved);
        $this->_helper->json(json_encode($responseArray));
    }

    private function sendApprovalEmail($site)
    {
        $to = $site->admin_email;
        $from = get_option('administrator_email');
        $subject = "Omeka Commons participation approved!";
        $body = "Thank you for participating in the Omeka Commons. blah blah blah
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

        $mail = new Zend_Mail();
        $mail->setBodyText($body);
        $mail->setFrom($from, "Omeka Commons");
        $mail->addTo($to, $site->title . " Administrator");
        $mail->setSubject($subject);
        $mail->addHeader('X-Mailer', 'PHP/' . phpversion());
        $mail->send();
    }
}