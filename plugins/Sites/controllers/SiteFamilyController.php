<?php

class Sites_SiteFamilyController extends Omeka_Controller_AbstractActionController
{
    
    public function init()
    {
        $this->_helper->db->setDefaultModelName('SiteFamily');
    }    
    public function browseAction() {}
    
    public function editAction() {}
    
    public function addAction() {}
    
    public function indexAction() {}
    
    public function showAction() 
    {
        parent::showAction();
        $this->view->sites = $this->view->site_family->getSites();
    }

        
}