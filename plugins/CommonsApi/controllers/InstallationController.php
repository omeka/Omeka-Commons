<?php

class CommonsApi_SiteController extends Omeka_Controller_Action
{


    public function applyAction()
    {
        $data = json_decode($_POST['data'], true);
        $installations = get_db()->getTable('Installation')->findBy(array('url'=>$data['installation']['url']));
        if(empty($installations)) {
            $installation = new Installation();
        } else {
            $response = array('status'=>'OK', 'message'=>'Already exists');
            $this->_helper->json($response);
            die();

        }
        //thwart cheeky monkeys trying to send data that they have been approved/added to the Commons
        if(isset($data['installation']['added'])) {
            unset($data['installation']['added']);
        }
        //@TODO: add institution data/table
        foreach($data['installation'] as $key=>$value) {
            $installation->$key = $value;
        }
        $salt = substr(md5(mt_rand()), 0, 16);
        $installation->key = sha1($salt . $installation->url . microtime() );

        $installation->entity_id = $installationEntity->id;
        $installation->save();

        $this->_helper->json($response);
    }




}