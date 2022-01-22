<?php

/**
 * Class Edit
 * The note controller. Here we create, read, update and delete (CRUD) example data.
 */
class Edit extends Controller
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
     * Sets up /edit/id page
     */
    public function id($id)
    {
    	$this->constructPage($id);
    }

    /**
     * Construct page and it's content
     */    
    public function constructPage($id) 
    {
    
        // make sure user is signin in
	    Auth::handleLogin();
	    
	    // create model
        $edit_model = $this->loadModel('Edit');
        		
        // check to see if there is an id passed through		
		if($id) {
		
			// get basic info on the phlo
			$phlo = $edit_model->getPhlo($id);
			$this->view->phlo = $phlo;
			$phlo_array = (array)$phlo[0];
    		$type = $phlo_array['type'];    	

	    	// create global objects
    	    $this->view->meta = $edit_model->setMeta();
        	$this->view->assets = $edit_model->setAssets();        
	        $this->view->crumbs = $edit_model->setBreadcrumbs();        
		
			// create phlo info object
			$this->view->child_obj = $edit_model->getChild($type,$id);
        	$this->view->parent_obj = $edit_model->getParent($type,$id);
	        $this->view->order = $edit_model->getOrder($type,$id);	       
	        
	        // create page
        	$this->view->render('edit/index');
	    
	    } else {
        
        	// redirect user to error page
            header('location: ' . URL . 'error/');
	    
	    }
        
    }

}
