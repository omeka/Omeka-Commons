<?php

class CommonsApi_SiteController extends Omeka_Controller_Action
{

    public function applyAction()
    {
        $data = $_POST['data'];
        $sites = get_db()->getTable('Site')->findBy(array('url'=>$data['site']['url']));
        if(empty($sites)) {
            $site = new Site();
        } else {
            $response = array('status'=>'EXISTS', 'message'=>'If you are having trouble sending data, please contacts us.');
            $this->_helper->json($response);
            die();

        }
        //thwart cheeky monkeys trying to send data that they have been approved/added to the Commons
        if(isset($data['site']['added'])) {
            unset($data['site']['added']);
        }
        //@TODO: add institution data/table
        foreach($data['site'] as $key=>$value) {
            $site->$key = $value;
        }
        $salt = substr(md5(mt_rand()), 0, 16);
        $site->key = sha1($salt . $site->url . microtime() );
        $site->save();
        $response = array('status'=>'OK', 'message'=>'Check your email for info about the next steps');
        $this->_helper->json($response);
    }
}