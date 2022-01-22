<?php

/**
 * ProfileModel
 * This is basically a simple CRUD (Create/Read/Update/Delete) demonstration.
 */
class ProfileModel
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
    	$assets = array('js' => 'profile', 'css' => '');
        return $assets;
    }

    /**
     * Set meta data
     */     
    public function setMeta()
    {
    	$meta = array('title' => 'Profile', 'description' => 'Manage your Werkphlo profile.');
        return $meta;
    }
    
    /**
     * Set breadcrumbs
     */     
    public function setBreadcrumbs()
    {
    	$crumbs = array('link' => 'Account', 'name' => 'Profile');
        return $crumbs;
    }    
    
    /**
     * Getter for all profile
     * @return array an array with several objects (the results)
     */
    public function getProfile()
    {
        $sql = "SELECT * FROM profile WHERE user_id = :user_id";
        $query = $this->db->prepare($sql);
        $query->execute(array(':user_id' => $_SESSION['user_id']));
        return $query->fetchAll();
    }    
    
    /**
     * Set required fields for profile information
     * @return array an array with several objects (the results)
     */
    public function getReqFields()
    {
        $req_fields = array('first_name','last_name','phone','house','address','town','county','postcode','country');
        return $req_fields;
    }  
    
    /**
     * Update all Profile and Login information
     * @return array an array with several objects (the results)
     */
    public function editProfile()
    {
    	$count = 0;
    	$run_validation = explode(",", $_POST['validate']);
    	foreach($run_validation as $func) {
    		if($func == 'profile_name') {
    			$this->updateName();
    		} elseif($func == 'email') { 
    			$this->updateEmail();
    		} elseif($func == 'password_new') {
    			$this->updatePassword();
    		} else {
    			if($func!='' && $count<1) {
    				$this->updatePersonal();
    				$count++;
    			}
    		}
    	}
    } 
                
    /**
     * Edit the user's name, provided in the editing form
     * @return bool success status
     */
    public function updatePersonal()
    {
        $query = $this->db->prepare("
        					UPDATE profile SET 
        						title = :title, 
        						first_name = :first_name, 
        						last_name = :last_name, 
        						phone = :phone, 
        						house = :house, 
        						address = :address, 
        						address_continued = :address_continued, 
        						town = :town, 
        						county = :county, 
        						postcode = :postcode, 
        						country = :country, 
        						company = :company 
        					WHERE user_id = :user_id");
        $query->execute(array(':title' => $_POST['title'],
        					    ':first_name' => $_POST['first_name'],
        					  	':last_name' => $_POST['last_name'],
        					  	':phone' => $_POST['phone'],
        					  	':house' => $_POST['house'],
        					  	':address' => $_POST['address'],
        					  	':address_continued' => $_POST['address_continued'],
        					  	':town' => $_POST['town'],
        					  	':county' => $_POST['county'],
        					  	':postcode' => $_POST['postcode'],
        					  	':country' => $_POST['country'],
        					  	':company' => $_POST['company'],
        					  	':user_id' => $_SESSION['user_id']));
        $count =  $query->rowCount();
        if ($count == 1) {
            $_SESSION["feedback_positive"][] = FEEDBACK_PROFILE_UPDATED;
        } else {
            $_SESSION["feedback_negative"][] = FEEDBACK_UNKNOWN_ERROR;
        }
    	
	}

    /**
     * Edit the user's name, provided in the editing form
     * @return bool success status
     */
    public function updateName()
    {
    
        // new username provided ?
        if (!isset($_POST['profile_name']) OR empty($_POST['profile_name'])) {
            $_SESSION["feedback_negative"][] = FEEDBACK_USERNAME_FIELD_EMPTY;
        }

        // new username same as old one ?
        if ($_POST['profile_name'] == $_SESSION["user_name"]) {
            $_SESSION["feedback_negative"][] = FEEDBACK_USERNAME_SAME_AS_OLD_ONE;           
        }

        // username cannot be empty and must be azAZ09 and 2-64 characters
        if (!preg_match("/^(?=.{2,64}$)[a-zA-Z][a-zA-Z0-9]*(?: [a-zA-Z0-9]+)*$/", $_POST['profile_name'])) {
            $_SESSION["feedback_negative"][] = FEEDBACK_USERNAME_DOES_NOT_FIT_PATTERN;
        }

        // clean the input
        $user_name = substr(strip_tags($_POST['profile_name']), 0, 64);

        // check if new username already exists
        $query = $this->db->prepare("SELECT user_id FROM signin WHERE user_name = :user_name");
        $query->execute(array(':user_name' => $user_name));
        $count =  $query->rowCount();
        if ($count == 1) {
            $_SESSION["feedback_negative"][] = FEEDBACK_USERNAME_ALREADY_TAKEN;
        }

        $query = $this->db->prepare("UPDATE signin SET user_name = :user_name WHERE user_id = :user_id");
        $query->execute(array(':user_name' => $user_name, ':user_id' => $_SESSION['user_id']));
        $count =  $query->rowCount();
        if ($count == 1) {
            Session::set('user_name', $user_name);
            $_SESSION["feedback_positive"][] = FEEDBACK_USERNAME_CHANGE_SUCCESSFUL;
        } else {
            $_SESSION["feedback_negative"][] = FEEDBACK_UNKNOWN_ERROR;
        }
        
    }
    
    /**
     * Edit the user's email, provided in the editing form
     * @return bool success status
     */
    public function updateEmail()
    {

        // email provided ?
        if (!isset($_POST['email']) OR empty($_POST['email'])) {
            $_SESSION["feedback_negative"][] = FEEDBACK_EMAIL_FIELD_EMPTY;        
        }

        // check if new email is same like the old one
        if ($_POST['email'] == $_SESSION["user_email"]) {
            $_SESSION["feedback_negative"][] = FEEDBACK_EMAIL_SAME_AS_OLD_ONE;        
        }

        // user's email must be in valid email format
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION["feedback_negative"][] = FEEDBACK_EMAIL_DOES_NOT_FIT_PATTERN;        
        }

        // check if user's email already exists
        $query = $this->db->prepare("SELECT * FROM signin WHERE user_email = :user_email");
        $query->execute(array(':user_email' => $_POST['email']));
        $count =  $query->rowCount();
        if ($count == 1) {
            $_SESSION["feedback_negative"][] = FEEDBACK_USER_EMAIL_ALREADY_TAKEN;                
        }

        // cleaning and write new email to database
        $user_email = substr(strip_tags($_POST['email']), 0, 64);
        $query = $this->db->prepare("UPDATE signin SET user_email = :user_email WHERE user_id = :user_id");
        $query->execute(array(':user_email' => $user_email, ':user_id' => $_SESSION['user_id']));
        $count =  $query->rowCount();
        if ($count != 1) {
            $_SESSION["feedback_negative"][] = FEEDBACK_UNKNOWN_ERROR;                
        }

        Session::set('user_email', $user_email);
        $_SESSION["feedback_positive"][] = FEEDBACK_EMAIL_CHANGE_SUCCESSFUL;

    } 
    
	/**
     * Set the new password (for DEFAULT user, FACEBOOK-users don't have a password)
     * Please note: At this point the user has already pre-verified via verifyPasswordReset() (within one hour),
     * so we don't need to check again for the 60min-limit here. In this method we authenticate
     * via username & password-reset-hash from (hidden) form fields.
     * @return bool success state of the password reset
     */
    public function updatePassword()
    {
    
        // basic checks
        if (!isset($_POST['password_new']) OR empty($_POST['password_new'])) {
        	$_SESSION["feedback_negative"][] = FEEDBACK_PASSWORD_FIELD_EMPTY;        
        }
        if (!isset($_POST['password_repeat']) OR empty($_POST['password_repeat'])) {
        	$_SESSION["feedback_negative"][] = FEEDBACK_PASSWORD_FIELD_EMPTY;        
        }
        // password does not match password repeat
        if ($_POST['password_new'] !== $_POST['password_repeat']) {
        	$_SESSION["feedback_negative"][] = FEEDBACK_PASSWORD_REPEAT_WRONG;        
        }
        // password too short
        if (strlen($_POST['password_new']) < 6) {
        	$_SESSION["feedback_negative"][] = FEEDBACK_PASSWORD_TOO_SHORT;        
        }

        // check if we have a constant HASH_COST_FACTOR defined
        // if so: put the value into $hash_cost_factor, if not, make $hash_cost_factor = null
        $hash_cost_factor = (defined('HASH_COST_FACTOR') ? HASH_COST_FACTOR : null);

        // crypt the user's password with the PHP 5.5's password_hash() function, results in a 60 character hash string
        // the PASSWORD_DEFAULT constant is defined by the PHP 5.5, or if you are using PHP 5.3/5.4, by the password hashing
        // compatibility library. the third parameter looks a little bit shitty, but that's how those PHP 5.5 functions
        // want the parameter: as an array with, currently only used with 'cost' => XX.
        $user_password_hash = password_hash($_POST['password_new'], PASSWORD_DEFAULT, array('cost' => $hash_cost_factor));
        $user_updated_timestamp = time();

        // write users new password hash into database, reset user_password_reset_hash
        $query = $this->db->prepare("UPDATE signin 
        								SET user_password_hash = :user_password_hash, 
        									user_password_reset_timestamp = :user_password_reset_timestamp
        								WHERE user_name = :user_name");
        $query->execute(array(':user_password_hash' => $user_password_hash, ':user_password_reset_timestamp' => $user_updated_timestamp, ':user_name' => $_POST['profile_name']));

        // check if exactly one row was successfully changed:
        if ($query->rowCount() == 1) {
            // successful password change!
        	$_SESSION["feedback_positive"][] = FEEDBACK_PASSWORD_CHANGE_SUCCESSFUL;                    
        }

        // default return
        $_SESSION["feedback_negative"][] = FEEDBACK_PASSWORD_CHANGE_FAILED;                            
    }    
    
}
