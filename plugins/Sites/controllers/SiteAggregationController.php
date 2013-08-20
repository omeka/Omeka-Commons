<?php

class Sites_SiteAggregationController extends Omeka_Controller_AbstractActionController
{
    
    public function init()
    {
        $this->_helper->db->setDefaultModelName('SiteAggregation');
    }    
    public function browseAction() {}

    //@TODO: only allow permission to add and edit if the current user owns a site
    public function editAction()
    {
        $varName = $this->view->singularize($this->_helper->db->getDefaultModelName());
        
        $record = $this->_helper->db->findById();
                
        // Check if the form was submitted.
        if ($this->getRequest()->isPost()) {
            // Set the POST data to the record.
            $record->setPostData($_POST);
            // Save the record. Passing false prevents thrown exceptions.
            if ($record->save(false)) {
                $successMessage = $this->_getEditSuccessMessage($record);
                if ($successMessage) {
                    $this->_helper->flashMessenger($successMessage, 'success');
                }
                $errors = false;
                //dig up the sites that correspond to the keys passed and add or delete
                foreach($_POST['site_key'] as $key) {           
                    $site = $this->_helper->db->getTable('Site')->findByKey($key);
                    if($site) {
                        $site->site_aggregation_id = $record->id;
                        $site->save(false);                        
                    } else {
                        $errors = true;
                        $this->_helper->flashMessenger("Key $key is not valid.", 'error');
                    }
                }

                foreach($_POST['site_keys_delete'] as $siteKey) {
                    $site = $this->_helper->db->getTable('Site')->findByKey($key);
                    $site->site_aggregation_id = null;
                    $site->save(false);
                }                
                if(!$errors) {
                    $this->_redirectAfterEdit($record);
                }
                
            // Flash an error if the record does not validate.
            } else {
                $this->_helper->flashMessenger($record->getErrors());
            }
        }
        
        $this->view->$varName = $record;
        $this->view->form = $this->getForm($record);
        $this->view->sites = $record->getSites();
    }

    public function addAction()
    {
        $class = $this->_helper->db->getDefaultModelName();
        $varName = $this->view->singularize($class);
        
        $record = new $class();
        if ($this->getRequest()->isPost()) {
            $record->setPostData($_POST);
            if ($record->save(false)) {
                $successMessage = $this->_getAddSuccessMessage($record);
                if ($successMessage != '') {
                    $this->_helper->flashMessenger($successMessage, 'success');
                }
                
                //dig up the sites that correspond to the keys passed
                foreach($_POST['site_keys'] as $siteKey) {
                    $site = $this->_helper->db->getTable('Site')->findByKey($key);
                    if($site) {
                        $site->site_aggregation_id = $record->id;
                        $site->save(false);                        
                    } else {
                        $this->_helper->flashMessenger("Key $key is not valid.", 'error');
                    }
                }
                $this->_redirectAfterEdit($record);
            } else {
                $this->_helper->flashMessenger($record->getErrors());
            }
        }
        $this->view->$varName = $record;
        $this->view->form = $this->getForm($record);
    }    
    
    public function indexAction() {}
    
    public function showAction() 
    {
        parent::showAction();
        $this->view->sites = $this->view->site_aggregation->getSites();
    }

    private function getForm($siteAggregation)
    {
        
        //the form requires some direct intervention for the list of
        //site keys, so we can't use Omeka_Form_Admin
        $form = "<form method='post' action='' type=''>";
        $form .= "<div class='field'>";
        $form .= "<div class='two columns alpha'>";
        $form .= "<label for='title'>Name</label>";
        $form .= "</div>";
        $form .= "<div class='inputs five columns omega'>";
        $form .= "<div class='input-block'>";
        $form .= $this->view->formText('name', $siteAggregation->name);
        $form .= "</div></div></div>";
        $form .= "<div class='field'>";
        $form .= "<div class='two columns alpha'>";
        $form .= "<label for='description'>Description</label>";
        $form .= "</div>";
        $form .= "<div class='inputs five columns omega'>";
        $form .= "<div class='input-block'>";
        $form .= $this->view->formTextarea('description', $siteAggregation->description, array('rows'=>10));
        $form .= "</div></div></div>";
        
        $sites = $siteAggregation->getSites();
        $form .= "<div class='field'>";
        $form .= "<div class='two columns alpha'>";
        $form .= "<label for='site_keys'>Site keys for this group of sites</label>";
        $form .= "</div>";
        $form .= "<div class='inputs five columns omega'>";
        $form .= "<fieldset name='site_keys'>";
        foreach($sites as $site) {
            
            $form .= "<div class='input-block'>";
            $form .= "<label>{$site->title}</label>";
            $form .= $this->view->formText('site_key[]', $site->api_key, array('id'=>null, 'class'=>'site_keys'));
            $form .= "</div>";
        }            
        $form .= "<div class='input-block'>";
        $form .= "<label>New site key</label>";
        $form .= $this->view->formText('site_key[]');
        $form .= "</div>";
        $form .= "</fieldset>";
        $form .= "<p id='add_site_key'>Add another site key</p>";
        $form .= "<button>Submit</button>";
        $form .= "</div></div>";
        
        $form .= "</form>";
        return $form;
    }
    
}