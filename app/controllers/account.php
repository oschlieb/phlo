<?php

/**
 * Class Account
 * The note controller. Here we create, read, update and delete (CRUD) example data.
*/
class Account extends Controller
{
    /**
     * Construct this object by extending the basic Controller class
     */
    function __construct()
    {
        parent::__construct();
        Auth::handleLogin();
    }

    /**
     * Sets up index page
     */
    function index()
    {
   		$this->constructPage('index','','');
    }

    /**
     * Change Werkphlo order
     */
    function orderBy($orderby_name,$orderby_direction)
    {
    	$this->constructPage('orderby',$orderby_name,$orderby_direction);
    }
    
    /**
     * Search for a Werkphlo
     */
    function searchFor($search)
    {
    	$this->constructPage('search',$search,'');
    }    
    
    /**
     * Add new Werkphlo
     */
    function add()
    {
    	$this->constructPage('add','','');
    }

    /**
     * Delete Werkphlo
     */
    function delete($id)
    {
    	$this->constructPage('delete',$id,'');
    }

    /**
     * Import CSV file
     */
    function import()
    {
    }        

    /**
     * Construct model
     */    
    public function constructModel()
    {
        Auth::handleLogin();
        $account_model = $this->loadModel('Account');
        return $account_model;  
	}

    /**
     * Construct page and it's content
     */    
    public function constructPage($action,$value,$direction)
    {            		
    
    	// if order is blank
    	if($action=='orderby' && $value=='') {
    		$value = 'name';
    	}
    	
    	// if direction empty
    	if($direction=='') {
    		$direction = 'asc';
    	}
    	    	
    	// construct model
    	$account_model = $this->constructModel();
    	
    	// add or delete
    	if($action=='add') {
    		$this->view->add = $account_model->addPhlo();
    	} elseif($action=='delete') {
    		$this->view->delete = $account_model->deletePhlo($value);
    	}
    	
    	// create global objects
        $this->view->meta = $account_model->setMeta();
        $this->view->assets = $account_model->setAssets();        
        $this->view->crumbs = $account_model->setBreadcrumbs();
            	
    	// create class specific objects
        $this->view->orderby = $value;
        $this->view->direction = $direction;
        $this->view->order_direction = $account_model->orderByDirection($value,$direction);
        $this->view->phlos = $account_model->getAllPhlos($action,$value,$direction);
        $this->view->profile = $account_model->getProfile();
        $this->view->types = $account_model->getTypes();
        $this->view->render('account/index');       
         
    }
        
}
