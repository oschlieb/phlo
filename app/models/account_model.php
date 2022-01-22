<?php

/**
 * AccountModel
 */
class AccountModel
{
    /**
     * Constructor, expects a Database connection
     * @param Database $db The Database object
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }
    
    /**
     * Set required asset files
     */     
    public function setAssets()
    {
    	$assets = array('js' => 'account', 'css' => '');
        return $assets;
    }

    /**
     * Set meta data
     */     
    public function setMeta()
    {
    	$meta = array('title' => 'Account', 'description' =>'Manage your Werkphlo account.');
        return $meta;
    }
    
    /**
     * Set breadcrumbs
     */     
    public function setBreadcrumbs()
    {
    	$crumbs = array('name' => 'Account');
        return $crumbs;
    }

    /**
     * Results order table headers
     */
	public function orderByDirection($value,$direction) 
	{
		$table_order = '';		
		$table_headers = array('name', 'type', 'style', 'created');
		foreach($table_headers as $name) {		
			if ($name == $value) {
				if (strtolower($direction) == 'asc') {
					$dir = 'desc';
					$arrow = ' &#8593;';
				} else {
					$dir = 'asc';
					$arrow = ' &#8595;';
				}
				$link = URL .'account/orderby/' . lcfirst($name) . '/' . $dir;
			} else {
				$arrow = '';
				$link = URL .'account/orderby/' . lcfirst($name) . '/asc';
			}
			$table_order .= '<div class="col pad-right"><a href="' . $link . '">' . ucfirst($name) . $arrow . '</a></div>';
		}
		return $table_order;		
	}

    /**
     * Display for all phlos
     * @return content displayed
     */
    public function displayProfile($profile_array)
    {
    	$output = "";
		if ($profile_array) {
			$array = (array)$profile_array[0];
			$end = count($profile_array);			
			$count = 1;
		    foreach($array as $key =>$value) {
		    	if($key=="first_name") {
	    			$output = $value;
	    		} elseif($key=="last_name") {
		    		$output .= " " . $value . "<br/>";
		    	} elseif($key=="phone") {
	    			$output .= $value . "<br/>";
	    		} else {
		    		if(isset($value)) {
			    		if($count!==$end) {
			    			$output .= $value . ", ";
		    			} else {
		    				$output .= $value . "<br/>";		    			
		    			}
			    	}
		    	}
	    		$count++;
		    }
		    $output .= Session::get('user_email');
		}
		return $output;
    }

    /**
     * Check to make sure the new Werkphlo is valid for adding or deleting
     */
	public function validatePhlo($item,$value)
	{
		$sql = "SELECT * FROM `ph` WHERE " . $item . " = '" . $value . "' AND customer = :user_id";
		$query = $this->db->prepare($sql);
        $query->execute(array(':user_id' => $_SESSION['user_id']));
        $count = $query->rowCount();
        if ($count == 1) {        	
        	return true;
        } else {
        	return false;
        }
	}

    /**
     * Add new Werkphlo
     */
	public function addPhlo()
	{

		// check if name and type are empty
		if (empty($_POST['add_name'])) {
			$_SESSION["feedback_negative"][] = FEEDBACK_WERKPHLO_ADD_NAME_MISSING;
            return false;
		}
		if (empty($_POST['add_type'])) {
			$_SESSION["feedback_negative"][] = FEEDBACK_WERKPHLO_ADD_TYPE_MISSING;
            return false;
		}
		
		// check if duplicate name
		$duplicate_name = $this->validatePhlo('name',$_POST['add_name']);
		
		// clean the input
        $phlo_name = strip_tags($_POST['add_name']);
        $phlo_type = strip_tags($_POST['add_type']);
        $timestamp = time();
        
		// add to database
        if(!$duplicate_name) {	     
			$sql = "INSERT INTO `ph` (name, type, customer, created) VALUES (:name, :type, :customer, :created)";
    	    $query = $this->db->prepare($sql);
        	$query->execute(array(':name' => $phlo_name, ':type' => $phlo_type, ':customer' => $_SESSION["user_id"], ':created' => $timestamp));
	        $count = $query->rowCount(); 
    	    if ($count == 1) {            
        	    $_SESSION["feedback_positive"][] = FEEDBACK_WERKPHLO_ADD_SUCCESS;
            	return true;
	        } else {
    	        $_SESSION["feedback_negative"][] = FEEDBACK_WERKPHLO_ADD_FAILURE;
        	    return false;
	        }	        
		} else {
			$_SESSION["feedback_negative"][] = FEEDBACK_WERKPHLO_ADD_DUPLCIATE_NAME;
			return false;
		}
        
	}

    /**
     * Delete Werkphlo
     */
	public function deletePhlo($id)
	{
		$correct_phlo = $this->validatePhlo('phid',$id);				
		if(!$correct_phlo) {
			$_SESSION["feedback_negative"][] = FEEDBACK_WERKPHLO_DELETE_FAILURE;
			return false;
		} else {
    		$sql = "DELETE FROM `ph` WHERE phid = :phid AND customer = :user_id";
	        $query = $this->db->prepare($sql);
    	    $query->execute(array(':phid' => $id, ':user_id' => $_SESSION['user_id']));			
    	    $_SESSION["feedback_positive"][] = FEEDBACK_WERKPHLO_DELETE_SUCCESS;    	    
    	    return true;
		}		
	}

    /**
     * Getter for all profile
     * @return array an array with several objects (the results)
     */
    public function getProfile()
    {
        $sql = "SELECT * FROM `profile` WHERE user_id = :user_id";
        $query = $this->db->prepare($sql);
        $query->execute(array(':user_id' => $_SESSION['user_id']));
        return $this->displayProfile($query->fetchAll());
    }

    /**
     * Get Werkphlo type
     * @return array an array with several objects (the results)
     */
    public function getTypes()
    {
        $sql = "SELECT type FROM `ph_type`";
        $query = $this->db->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }
    
    /**
     * Get all werkphlos from database
     */	
    public function getAllPhlos($action,$value,$direction)
    {
    	$sql = "SELECT phid, name, type, style, customer, created FROM `ph` WHERE customer = :user_id";
    	if($action=='search') {
    	    $sql .= " AND `name` LIKE '%" . htmlspecialchars($value, ENT_QUOTES) . "%';";
    	} elseif ($action=='orderby') {
    		$act = htmlspecialchars($value, ENT_QUOTES);
			$orderby_query = " ORDER BY " . $act . " " . strtoupper($direction);
			$sql .= $orderby_query;
        }
        $count = 0;
        $query = $this->db->prepare($sql);
        $query->execute(array(':user_id' => $_SESSION['user_id']));
       	$count = $query->rowCount(); 
    	if ($count > 0) {			
            return $query->fetchAll();
	    } else {
	    	if($action=='search') {
			    $_SESSION["feedback_negative"][] = FEEDBACK_WERKPHLO_SEARCH_FAILURE;
				return false;
			} else {
			    $_SESSION["feedback_warning"][] = FEEDBACK_WERKPHLO_NOT_CREATED;
				return false;			
			}
		}
    }
    	    
}