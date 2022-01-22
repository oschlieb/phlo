<?php

/**
 * Class Add
 * The note controller. Here we create, read, update and delete (CRUD) example data.
 */
class Add extends Controller
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
        // make sure user is signin in
	    Auth::handleLogin();
	    
	    // create model
        $add_model = $this->loadModel('Add');
        
	    // create objects
	    $this->view->meta = $add_model->setMeta();
    	$this->view->assets = $add_model->setAssets();
        $this->view->render('add/index');
        
    }

}
