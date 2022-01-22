<?php

/**
 * AjaxModel
 * Used for all AJAX requests from JQuery.
 */
class AjaxModel
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
     * Delete `profile` and `signin` table rows
     * @return true or false on delete
     */
    public function deleteProfile()
    {	
		$sql = "DELETE profile, signin FROM profile, signin WHERE profile.user_id = signin.user_id AND signin.user_id = " . $_SESSION['user_id'];
	    $query = $this->db->prepare($sql);    	
	    $query->execute();
    	if ($query->execute() === true) { /* note: feedback notifications should be logged */
    		$phlos_removed = $this->deletePhlos();    	
    		if($phlos_removed) {
    			setcookie('rememberme', false, time() - (3600 * 3650), '/', COOKIE_DOMAIN);
				Session::destroy();    		
    		} else {
    			$_SESSION["feedback_negative"][] = FEEDBACK_WERKPHLO_DELETE_PROFILE_DATA_FAILED;
    		}
    		
		} else {
			$_SESSION["feedback_negative"][] = FEEDBACK_WERKPHLO_DELETE_PROFILE_FAILED;
		}
    }

    /**
     * Delete `ph` and all associated table rows
     * @return true or false on delete
     */
    public function deletePhlos()
    {
    	$sql = "DELETE FROM `ph` WHERE customer = :user_id";
	    $query = $this->db->prepare($sql);
	    /* note: foreign key constraints needs to be set to all child, patent and order tables */
    	if ($query->execute(array(':user_id' => $_SESSION['user_id'])) === true) {
			return true;	
		} else {
    		return false;
		}
    }
    
    
}
