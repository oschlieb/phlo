<?php

/**
 * Class Profile
 * The note controller. Here we create, read, update and delete (CRUD) example data.
 */
class Profile extends Controller
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
     * Edit profile information once the form has been submitted
     */
    public function edit()
    {
    	$this->constructPage('edit');
    }

    /**
     * Construct page and it's content
     */    
    public function constructPage($action) 
    {
    
	    // make sure user is signin in
	    Auth::handleLogin();
        
        // create model
        $profile_model = $this->loadModel('Profile');
        
        // action type
        if($action == 'edit') {
        	$profile_model->editProfile();
        }
        
    	// create global objects
        $this->view->meta = $profile_model->setMeta();
        $this->view->assets = $profile_model->setAssets();        
        $this->view->crumbs = $profile_model->setBreadcrumbs();        
		
		// create objects
        $this->view->profile = $profile_model->getProfile();
        $this->view->reqfields = $profile_model->getReqFields();		
        $this->view->render('profile/index');
        
    }

}
