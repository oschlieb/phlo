<?php

/**
 * RegisterModel
 *
 * Handles the user's login / logout / registration stuff
 */
use Gregwar\Captcha\CaptchaBuilder;

class RegisterModel
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
     * Set meta data
     */     
    public function setMeta()
    {
    	$meta = array('title' => 'Register', 'description' =>'Create your Werkphlo account.');
        return $meta;
    }
    
    /**
     * Set breadcrumbs
     */     
    public function setBreadcrumbs()
    {
    	$crumbs = array('name' => 'Register');
        return $crumbs;
    }
    
    
    /**
     * handles the entire registration process for DEFAULT users (not for people who register with
     * 3rd party services, like facebook) and creates a new user in the database if everything is fine
     * @return boolean Gives back the success status of the registration
     */
    public function registerNewUser()
    {
    
    	$uid = $_POST['unique_id'];
    
        // perform all necessary form checks
        if (!$this->checkCaptcha()) {
            $_SESSION["feedback_negative"][] = FEEDBACK_CAPTCHA_WRONG;
        } elseif (empty($_POST['user_name'])) {
            $_SESSION["feedback_negative"][] = FEEDBACK_USERNAME_FIELD_EMPTY;
        } elseif (empty($_POST['user_password_new']) OR empty($_POST['user_password_repeat'])) {
            $_SESSION["feedback_negative"][] = FEEDBACK_PASSWORD_FIELD_EMPTY;
        } elseif ($_POST['user_password_new'] !== $_POST['user_password_repeat']) {
            $_SESSION["feedback_negative"][] = FEEDBACK_PASSWORD_REPEAT_WRONG;
        } elseif (strlen($_POST['user_password_new']) < 6) {
            $_SESSION["feedback_negative"][] = FEEDBACK_PASSWORD_TOO_SHORT;
        } elseif (strlen($_POST['user_name']) > 64 OR strlen($_POST['user_name']) < 2) {
            $_SESSION["feedback_negative"][] = FEEDBACK_USERNAME_TOO_SHORT_OR_TOO_LONG;
        } elseif (!preg_match('/^[a-z\d]{2,64}$/i', $_POST['user_name'])) {
            $_SESSION["feedback_negative"][] = FEEDBACK_USERNAME_DOES_NOT_FIT_PATTERN;
        } elseif (empty($_POST['user_email'])) {
            $_SESSION["feedback_negative"][] = FEEDBACK_EMAIL_FIELD_EMPTY;
        } elseif (strlen($_POST['user_email']) > 64) {
            $_SESSION["feedback_negative"][] = FEEDBACK_EMAIL_TOO_LONG;
        } elseif (!filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION["feedback_negative"][] = FEEDBACK_EMAIL_DOES_NOT_FIT_PATTERN;
        } elseif (!empty($_POST['user_name'])
            AND strlen($_POST['user_name']) <= 64
            AND strlen($_POST['user_name']) >= 2
            AND preg_match('/^[a-z\d]{2,64}$/i', $_POST['user_name'])
            AND !empty($_POST['user_email'])
            AND strlen($_POST['user_email']) <= 64
            AND filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)
            AND !empty($_POST['user_password_new'])
            AND !empty($_POST['user_password_repeat'])
            AND ($_POST['user_password_new'] === $_POST['user_password_repeat'])) {

            // clean the input
            $user_name = strip_tags($_POST['user_name']);
            $user_email = strip_tags($_POST['user_email']);

            // crypt the user's password with the PHP 5.5's password_hash() function, results in a 60 character
            // hash string. the PASSWORD_DEFAULT constant is defined by the PHP 5.5, or if you are using PHP 5.3/5.4,
            // by the password hashing compatibility library. the third parameter looks a little bit shitty, but that's
            // how those PHP 5.5 functions want the parameter: as an array with, currently only used with 'cost' => XX
            $hash_cost_factor = (defined('HASH_COST_FACTOR') ? HASH_COST_FACTOR : null);
            $user_password_hash = password_hash($_POST['user_password_new'], PASSWORD_DEFAULT, array('cost' => $hash_cost_factor));

            // check if username already exists
            $query = $this->db->prepare("SELECT * FROM signin WHERE user_name = :user_name");
            $query->execute(array(':user_name' => $user_name));
            $count =  $query->rowCount();
            if ($count == 1) {
                $_SESSION["feedback_negative"][] = FEEDBACK_USERNAME_ALREADY_TAKEN;
                return false;
            }

            // check if email already exists
            $query = $this->db->prepare("SELECT user_id FROM signin WHERE user_email = :user_email");
            $query->execute(array(':user_email' => $user_email));
            $count =  $query->rowCount();
            if ($count == 1) {
                $_SESSION["feedback_negative"][] = FEEDBACK_USER_EMAIL_ALREADY_TAKEN;
                return false;
            }

            // generate random hash for email verification (40 char string)
            $user_activation_hash = sha1(uniqid(mt_rand(), true));
            // generate integer-timestamp for saving of account-creating date
            $user_creation_timestamp = time();

            // write new users data into database
            $sql = "INSERT INTO signin (user_name, user_password_hash, user_email, user_creation_timestamp, user_activation_hash, user_provider_type)
                    VALUES (:user_name, :user_password_hash, :user_email, :user_creation_timestamp, :user_activation_hash, :user_provider_type)";
            $query = $this->db->prepare($sql);
            $query->execute(array(':user_name' => $user_name,
                                  ':user_password_hash' => $user_password_hash,
                                  ':user_email' => $user_email,
                                  ':user_creation_timestamp' => $user_creation_timestamp,
                                  ':user_activation_hash' => $user_activation_hash,
                                  ':user_provider_type' => 'DEFAULT'));
            $count =  $query->rowCount();
            if ($count != 1) {
                $_SESSION["feedback_negative"][] = FEEDBACK_ACCOUNT_CREATION_FAILED;
                return false;
            }

            // get user_id of the user that has been created, to keep things clean we DON'T use lastInsertId() here
            $query = $this->db->prepare("SELECT user_id FROM signin WHERE user_name = :user_name");
            $query->execute(array(':user_name' => $user_name));
            if ($query->rowCount() != 1) {
                $_SESSION["feedback_negative"][] = FEEDBACK_UNKNOWN_ERROR;
                return false;
            }
            $result_user_row = $query->fetch();
            $user_id = $result_user_row->user_id;

            // send verification email, if verification email sending failed: instantly delete the user
            if ($this->sendVerificationEmail($user_id, $user_email, $user_activation_hash)) {
                //$_SESSION["feedback_positive"][] = FEEDBACK_ACCOUNT_SUCCESSFULLY_CREATED;
                return true;
            } else {
                $query = $this->db->prepare("DELETE FROM signin WHERE user_id = :last_inserted_id");
                $query->execute(array(':last_inserted_id' => $user_id));
                $_SESSION["feedback_negative"][] = FEEDBACK_VERIFICATION_MAIL_SENDING_FAILED;
                return false;
            }
        } else {
            $_SESSION["feedback_negative"][] = FEEDBACK_UNKNOWN_ERROR;
        }
        // default return, returns only true of really successful (see above)
        return false;
    }

    /**
     * sends an email to the provided email address
     * @param int $user_id user's id
     * @param string $user_email user's email
     * @param string $user_activation_hash user's mail verification hash string
     * @return boolean gives back true if mail has been sent, gives back false if no mail could been sent
     */
    private function sendVerificationEmail($user_id, $user_email, $user_activation_hash)
    {
        // create PHPMailer object (this is easily possible as we auto-load the according class(es) via composer)
        $mail = new PHPMailer;

        // please look into the config/config.php for much more info on how to use this!
        if (EMAIL_USE_SMTP) {
            // set PHPMailer to use SMTP
            $mail->IsSMTP();
            // useful for debugging, shows full SMTP errors, config this in config/config.php
            $mail->SMTPDebug = PHPMAILER_DEBUG_MODE;
            // enable SMTP authentication
            $mail->SMTPAuth = EMAIL_SMTP_AUTH;
            // enable encryption, usually SSL/TLS
            if (defined('EMAIL_SMTP_ENCRYPTION')) {
                $mail->SMTPSecure = EMAIL_SMTP_ENCRYPTION;
            }
            // set SMTP provider's credentials
            $mail->Host = EMAIL_SMTP_HOST;
            $mail->Username = EMAIL_SMTP_USERNAME;
            $mail->Password = EMAIL_SMTP_PASSWORD;
            $mail->Port = EMAIL_SMTP_PORT;
        } else {
            $mail->IsMail();
        }

        // fill mail with data
        $mail->From = EMAIL_VERIFICATION_FROM_EMAIL;
        $mail->FromName = EMAIL_VERIFICATION_FROM_NAME;
        $mail->AddAddress($user_email);
        $mail->Subject = EMAIL_VERIFICATION_SUBJECT;
        $mail->Body = EMAIL_VERIFICATION_CONTENT . EMAIL_VERIFICATION_URL . '/' . urlencode($user_id) . '/' . urlencode($user_activation_hash);

        // final sending and check
        if($mail->Send()) {
            $_SESSION["feedback_positive"][] = FEEDBACK_VERIFICATION_MAIL_SENDING_SUCCESSFUL;
            return true;
        } else {
            $_SESSION["feedback_negative"][] = FEEDBACK_VERIFICATION_MAIL_SENDING_ERROR . $mail->ErrorInfo;
            return false;
        }
    }

    /**
     * checks the email/verification code combination and set the user's activation status to true in the database
     * @param int $user_id user id
     * @param string $user_activation_verification_code verification token
     * @return bool success status
     */
    public function verifyNewUser($user_id, $user_activation_verification_code)
    {
        $sth = $this->db->prepare("UPDATE signin
                                   SET user_active = 1, user_activation_hash = NULL
                                   WHERE user_id = :user_id AND user_activation_hash = :user_activation_hash");
        $sth->execute(array(':user_id' => $user_id, ':user_activation_hash' => $user_activation_verification_code));
        if ($sth->rowCount() == 1) {
            $_SESSION["feedback_positive"][] = FEEDBACK_ACCOUNT_ACTIVATION_SUCCESSFUL;
            return true;
        } else {
            $_SESSION["feedback_negative"][] = FEEDBACK_ACCOUNT_ACTIVATION_FAILED;
            return false;
        }
    }
    
    /**
     * creates table entry for profile information once users has been verified
     * @return bool success status
     */
    public function addUserProfile($user_id)
    {
        $query = $this->db->prepare("INSERT INTO profile (user_id) VALUES ($user_id)");
        $query->execute(array(':user_id' => $user_id));
        $count =  $query->rowCount();
        if ($count != 1) {
           $_SESSION["feedback_negative"][] = FEEDBACK_PROFILE_CREATION_FAILED;
           return false;
        }
    }

    /**
     * Generates the captcha, "returns" a real image,
     * this is why there is header('Content-type: image/jpeg')
     * Note: This is a very special method, as this is echoes out binary data.
     * Eventually this is something to refactor
     */
    public function generateCaptcha()
    {
        // create a captcha with the CaptchaBuilder lib
        $builder = new CaptchaBuilder;
        $builder->build();

        // write the captcha character into session
        $_SESSION['captcha'] = $builder->getPhrase();

        // render an image showing the characters (=the captcha)
        header('Content-type: image/jpeg');
        $builder->output();
    }

    /**
     * Checks if the entered captcha is the same like the one from the rendered image which has been saved in session
     * @return bool success of captcha check
     */
    private function checkCaptcha()
    {
        if (isset($_POST["captcha"]) AND ($_POST["captcha"] == $_SESSION['captcha'])) {
            return true;
        }
        // default return
        return false;
    }

    /**
     * Gets the URL where the "Login with Facebook"-button redirects the user to
     * @return string The URL
     */
    public function getFacebookLoginUrl()
    {
        // Create Facebook object (official Facebook SDK, loaded via Composer: facebook/php-sdk), this is the official
        // way to login via Facebook with PHP. Constants come from config/config.php.
        $facebook = new Facebook(array('appId'  => FACEBOOK_LOGIN_APP_ID, 'secret' => FACEBOOK_LOGIN_APP_SECRET));

        // get the "login"-URL: This is the URL the user will be redirected to after being sent to the Facebook Auth
        // server by clicking the "login via facebook"-button. Don't touch this until you know exactly what you do.
        $facebook_login_url = $facebook->getLoginUrl(array('redirect_uri' => URL . FACEBOOK_LOGIN_PATH));

        return $facebook_login_url;
    }

    /**
     * Gets the URL where the "Register with Facebook"-button redirects the user to
     * @return string The URL
     */
    public function getFacebookRegisterUrl()
    {
        // create our Application instance (necessary to request Facebook data)
        $facebook = new Facebook(array('appId'  => FACEBOOK_LOGIN_APP_ID, 'secret' => FACEBOOK_LOGIN_APP_SECRET));

        // build the URL where the user will be redirected to after being authenticated on the Facebook server
        // Note: Facebook needs to know that URL, that's why we pass this
        $redirect_url_after_facebook_auth = URL . FACEBOOK_REGISTER_PATH;

        // hard to explain, read the Facebook PHP SDK for more information!
        // basically, when the user clicks the Facebook register button, the following arguments will be passed
        // to Facebook: In this case a request for getting the email (not shown by default btw) and the URL
        // when facebook will send the user after he/she has authenticated
        // "scope" => 'email' means that we need read-access to the user's "public" data plus his/her email address
        // (not public by default)
        $facebook_register_url = $facebook->getLoginUrl(array(
            'scope' => 'email',
            'redirect_uri' => $redirect_url_after_facebook_auth
        ));

        return $facebook_register_url;
    }

    /**
     * This is the main method to handle the full facebook registration process
     * @return bool The entire facebook registration's success status
     */
    public function registerWithFacebook()
    {
        // instantiate the facebook object
        $facebook = new Facebook(array('appId'  => FACEBOOK_LOGIN_APP_ID, 'secret' => FACEBOOK_LOGIN_APP_SECRET));

        // get user id (string)
        $user = $facebook->getUser();

        // if the user object (array?) exists, the user has identified as a real facebook user
        if ($user) {
            try {
                // Proceed knowing you have a logged in user who's authenticated
                $facebook_user_data = $facebook->api('/me');
            } catch (FacebookApiException $e) {
                // when facebook goes offline or armageddon comes or some shit like that
                error_log($e);
                $user = null;
                $_SESSION["feedback_negative"][] = FEEDBACK_FACEBOOK_OFFLINE;
                return false;
            }
        }

        // if we don't have the facebook-user array variable, leave the method
        if (!$facebook_user_data) {
            $_SESSION["feedback_negative"][] = FEEDBACK_FACEBOOK_UID_ALREADY_EXISTS;
            return false;
        }

        // check if user provides mail address (registration will only work when user agrees to provide email address)
        if (!$this->facebookUserHasEmail($facebook_user_data)) {
            $_SESSION["feedback_negative"][] = FEEDBACK_FACEBOOK_EMAIL_NEEDED;
            return false;
        }

        // check if a user with that facebook user id (UID) has already registered
        if ($this->facebookUserIdExistsAlreadyInDatabase($facebook_user_data)) {
            $_SESSION["feedback_negative"][] = FEEDBACK_FACEBOOK_UID_ALREADY_EXISTS;
            return false;
        }

        // check if a user with that username already exists in our database
        // note: Facebook's internal username is usually the person's full name plus a number (and dots between)
        if ($this->facebookUserNameExistsAlreadyInDatabase($facebook_user_data)) {
        	$facebook_user_data["username"] = $this->generateUniqueUserNameFromExistingUserName($facebook_user_data["username"]);
         if ($this->facebookUserNameExistsAlreadyInDatabase($facebook_user_data)) {
        	//shouldn't get here if we managed to generate a unique name!
        	$_SESSION["feedback_negative"][] = FEEDBACK_FACEBOOK_USERNAME_ALREADY_EXISTS;
          return false;
         }
        }

        // check if that email address already exists in our database
        if ($this->facebookUserEmailExistsAlreadyInDatabase($facebook_user_data)) {
            $_SESSION["feedback_negative"][] = FEEDBACK_FACEBOOK_EMAIL_ALREADY_EXISTS;
            return false;
        }

        // all necessary things have been checked, so let's create that user
        if ($this->registerNewUserWithFacebook($facebook_user_data)) {
            $_SESSION["feedback_positive"][] = FEEDBACK_FACEBOOK_REGISTER_SUCCESSFUL;
            return true;
        } else {
            $_SESSION["feedback_negative"][] = FEEDBACK_UNKNOWN_ERROR;
            return false;
        }

        // default return
        return false;
    }

    /**
     * Register user with data from the "facebook object"
     * @param array $facebook_user_data stuff from the facebook class
     * @return bool success state
     */
    public function registerNewUserWithFacebook($facebook_user_data)
    {
        // delete dots from facebook-username (it's the common way to do this like that)
        $clean_user_name_from_facebook = str_replace(".", "", $facebook_user_data["username"]);
        // generate integer-timestamp for saving of account-creating date
        $user_creation_timestamp = time();

        $sql = "INSERT INTO signin (user_name, user_email, user_creation_timestamp, user_active, user_provider_type, user_facebook_uid)
                VALUES (:user_name, :user_email, :user_creation_timestamp, :user_active, :user_provider_type, :user_facebook_uid)";
        $query = $this->db->prepare($sql);
        $query->execute(array(':user_name' => $clean_user_name_from_facebook,
                              ':user_email' => $facebook_user_data["email"],
                              ':user_creation_timestamp' => $user_creation_timestamp,
                              ':user_active' => 1,
                              ':user_provider_type' => 'FACEBOOK',
                              ':user_facebook_uid' => $facebook_user_data["id"]));

        $count = $query->rowCount();
        if ($count == 1) {
            $query = $this->db->prepare("SELECT user_id, user_name, user_email, user_account_type, user_provider_type
                                         FROM   signin
                                         WHERE  user_name = :user_name AND user_provider_type = :provider_type");
            $query->execute(array(':user_name' => $clean_user_name_from_facebook, ':provider_type' => 'FACEBOOK'));
            $count_from_select_statement = $query->rowCount();
            if ($count_from_select_statement == 1) {
                // registration successful
                return true;
            }
        }
        // default return
        return false;
    }

    /**
     * Checks if the facebook-user data array has an email. It's possible that users block this, so we don't have
     * an email and therefore cannot register this person (registration without email is impossible).
     * @param array $facebook_user_data stuff from the facebook class
     * @return bool user has email yes/no
     */
    public function facebookUserHasEmail($facebook_user_data)
    {
        if (isset($facebook_user_data["email"]) && !empty($facebook_user_data["email"])) {
            return true;
        }
        // default return
        return false;
    }

    /**
     * Check if the facebook-user's UID (unique facebook ID) already exists in our database
     * @param array $facebook_user_data stuff from the facebook class
     * @return bool success state
     */
    public function facebookUserIdExistsAlreadyInDatabase($facebook_user_data)
    {
        $query = $this->db->prepare("SELECT user_id FROM signin WHERE user_facebook_uid = :user_facebook_uid");
        $query->execute(array(':user_facebook_uid' => $facebook_user_data["id"]));

        if ($query->rowCount() == 1) {
            return true;
        }
        // default return
        return false;
    }

    /**
     * Checks if the facebook-user's username is already in our database
     * Note: facebook's user-names have dots, so we remove all dots.
     * @param array $facebook_user_data stuff from the facebook class
     * @return bool success state
     */
    public function facebookUserNameExistsAlreadyInDatabase($facebook_user_data)
    {
        // delete dots from facebook's username (it's the common way to do this like that)
        $clean_user_name_from_facebook = str_replace(".", "", $facebook_user_data["username"]);

        $query = $this->db->prepare("SELECT user_id FROM signin WHERE user_name = :clean_user_name_from_facebook");
        $query->execute(array(':clean_user_name_from_facebook' => $clean_user_name_from_facebook));

        if ($query->rowCount() == 1) {
            return true;
        }
        // default return
        return false;
    }

    /**
     * Checks if the facebook-user's email address is already in our database
     * @param array $facebook_user_data stuff from the facebook class
     * @return bool success state
     */
    public function facebookUserEmailExistsAlreadyInDatabase($facebook_user_data)
    {
        $query = $this->db->prepare("SELECT user_id FROM signin WHERE user_email = :facebook_email");
        $query->execute(array(':facebook_email' => $facebook_user_data["email"]));

        if ($query->rowCount() == 1) {
            return true;
        }
        // default return
        return false;
    }

    /**
     * Generate unique user_name from facebook-user's username appended with a number
     * @param string $existing_name $facebook_user_data stuff from the facebook class
     * @return string unique user_name not in database yet
     */
    public function generateUniqueUserNameFromExistingUserName($existing_name)
    {
    	//strip any dots, trailing numbers and white spaces
        $existing_name = str_replace(".", "", $existing_name);
        $existing_name = preg_replace('/\s*\d+$/', '', $existing_name);

        // loop until we have a new username, adding an increasing number to the given string every time
    	$n = 0;
    	do {
            $n = $n+1;
            $new_username = $existing_name . $n;
            $query = $this->db->prepare("SELECT user_id FROM signin WHERE user_name = :name_with_number");
            $query->execute(array(':name_with_number' => $new_username));
    	 	 
    	 } while ($query->rowCount() == 1);

    	return $new_username;
    }

}
