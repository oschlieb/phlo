<?php

/**
 * Class AJAX
 * Used for all AJAX requests from JQuery.
 */
class Ajax extends Controller
{
    /**
     * Construct this object by extending the basic Controller class
     */
    public function __construct()
    {
        parent::__construct();
        Auth::handleLogin();
    }

    /**
     * Sets up index page
     */
    public function index()
    {
    	$this->constructPage('');
    }

    /**
     * Delete profile
     */
    public function deleteprofile()
    {
    	$this->constructPage('delete');
    }

    /**
     * Construct page and it's content
     */    
    public function constructPage($action) 
    {
            
        // create model
        $ajax_model = $this->loadModel('Ajax');
        
        // action type
        if($action == 'delete') {
        	$ajax_model->deleteProfile();
        }
		        
    }

}
