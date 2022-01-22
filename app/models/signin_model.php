<?php

/**
 * SigninModel
 *
 * Handles the user's signin / logout / registration stuff
 */
use Gregwar\Captcha\CaptchaBuilder;

class SigninModel
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
    public function setMeta($page)
    {
    	if ($page == "requestpasswordreset") {
		    $meta = array('title' => 'Reset Password', 'description' =>'Reset your Werkphlo account password.');
		} else if ($page == "verifypasswordreset") {
		    $meta = array('title' => 'Verify Password Reset', 'description' =>'Reset your Werkphlo account password.');
		} else {
			$meta = array('title' => 'Sign in', 'description' =>'Sign into your Werkphlo account.');
		}
        return $meta;
    }
    
    /**
     * Set breadcrumbs
     */     
    public function setBreadCrumbs($page)
    {
    	if ($page == "requestpasswordreset") {
		    $crumbs = array('link' => 'Sign in', 'name' => 'Reset Password');
		} else if ($page == "verifypasswordreset") {
		    $crumbs = array('link' => 'Sign in', 'name' => 'Verify Password Reset');
		} else {
			$crumbs = array('name' => 'Sign in');
		}
        return $crumbs;
    }

    /**
     * signin process (for DEFAULT user accounts).
     * Users who signin with Facebook etc. are handled with loginWithFacebook()
     * @return bool success state
     */
    public function signin()
    {
        
        // we do negative-first checks here
        if (!isset($_POST['user_name']) OR empty($_POST['user_name'])) {
            $_SESSION["feedback_negative"][] = FEEDBACK_USERNAME_FIELD_EMPTY;
            return false;
        }
        if (!isset($_POST['user_password']) OR empty($_POST['user_password'])) {
            $_SESSION["feedback_negative"][] = FEEDBACK_PASSWORD_FIELD_EMPTY;
            return false;
        }

        // get user's data
        // (we check if the password fits the password_hash via password_verify() some lines below)
        $sth = $this->db->prepare("SELECT user_id,
                                          user_name,
                                          user_email,
                                          user_password_hash,
                                          user_active,
                                          user_account_type,
                                          user_failed_logins,
                                          user_last_failed_login
                                   FROM   signin
                                   WHERE  (user_name = :user_name OR user_email = :user_name)
                                          AND user_provider_type = :provider_type");
        // DEFAULT is the marker for "normal" accounts (that have a password etc.)
        // There are other types of accounts that don't have passwords etc. (FACEBOOK)
        $sth->execute(array(':user_name' => $_POST['user_name'], ':provider_type' => 'DEFAULT'));
        $count =  $sth->rowCount();
        // if there's NOT one result
        if ($count != 1) {
            // was FEEDBACK_USER_DOES_NOT_EXIST before, but has changed to FEEDBACK_LOGIN_FAILED
            // to prevent potential attackers showing if the user exists
            $_SESSION["feedback_negative"][] = FEEDBACK_LOGIN_FAILED;
            return false;
        }

        // fetch one row (we only have one result)
        $result = $sth->fetch();

        // block signin attempt if somebody has already failed 3 times and the last signin attempt is less than 30sec ago
        if (($result->user_failed_logins >= 3) AND ($result->user_last_failed_login > (time()-30))) {
            $_SESSION["feedback_negative"][] = FEEDBACK_PASSWORD_WRONG_3_TIMES;
            return false;
        }

        // check if hash of provided password matches the hash in the database
        if (password_verify($_POST['user_password'], $result->user_password_hash)) {

            if ($result->user_active != 1) {
                $_SESSION["feedback_negative"][] = FEEDBACK_ACCOUNT_NOT_ACTIVATED_YET;
                return false;
            }

            // signin process, write the user data into session
            Session::init();
            Session::set('user_logged_in', true);
            Session::set('user_id', $result->user_id);
            Session::set('user_name', $result->user_name);
            Session::set('user_email', $result->user_email);
            Session::set('user_account_type', $result->user_account_type);
            Session::set('user_provider_type', 'DEFAULT');
            /*
            // put native avatar path into session
            Session::set('user_avatar_file', $this->getUserAvatarFilePath());
            // put Gravatar URL into session
            $this->setGravatarImageUrl($result->user_email, AVATAR_SIZE);
			*/
            // reset the failed signin counter for that user (if necessary)
            if ($result->user_last_failed_login > 0) {
                $sql = "UPDATE signin SET user_failed_logins = 0, user_last_failed_login = NULL
                        WHERE user_id = :user_id AND user_failed_logins != 0";
                $sth = $this->db->prepare($sql);
                $sth->execute(array(':user_id' => $result->user_id));
            }

            // generate integer-timestamp for saving of last-login date
            $user_last_login_timestamp = time();
            // write timestamp of this signin into database (we only write "real" logins via signin form into the
            // database, not the session-login on every page request
            $sql = "UPDATE signin SET user_last_login_timestamp = :user_last_login_timestamp WHERE user_id = :user_id";
            $sth = $this->db->prepare($sql);
            $sth->execute(array(':user_id' => $result->user_id, ':user_last_login_timestamp' => $user_last_login_timestamp));

            // if user has checked the "remember me" checkbox, then write cookie
            if (isset($_POST['user_rememberme'])) {

                // generate 64 char random string
                $random_token_string = hash('sha256', mt_rand());

                // write that token into database
                $sql = "UPDATE signin SET user_rememberme_token = :user_rememberme_token WHERE user_id = :user_id";
                $sth = $this->db->prepare($sql);
                $sth->execute(array(':user_rememberme_token' => $random_token_string, ':user_id' => $result->user_id));

                // generate cookie string that consists of user id, random string and combined hash of both
                $cookie_string_first_part = $result->user_id . ':' . $random_token_string;
                $cookie_string_hash = hash('sha256', $cookie_string_first_part);
                $cookie_string = $cookie_string_first_part . ':' . $cookie_string_hash;

                // set cookie
                setcookie('rememberme', $cookie_string, time() + COOKIE_RUNTIME, "/", COOKIE_DOMAIN);
            }

            // return true to make clear the signin was successful
            return true;

        } else {
            // increment the failed signin counter for that user
            $sql = "UPDATE signin
                    SET user_failed_logins = user_failed_logins+1, user_last_failed_login = :user_last_failed_login
                    WHERE user_name = :user_name OR user_email = :user_name";
            $sth = $this->db->prepare($sql);
            $sth->execute(array(':user_name' => $_POST['user_name'], ':user_last_failed_login' => time() ));
            // feedback message
            $_SESSION["feedback_negative"][] = FEEDBACK_PASSWORD_WRONG;
            return false;
        }

        // default return
        return false;
    }

    /**
     * performs the signin via cookie (for DEFAULT user account, FACEBOOK-accounts are handled differently)
     * @return bool success state
     */
    public function loginWithCookie()
    {
        $cookie = isset($_COOKIE['rememberme']) ? $_COOKIE['rememberme'] : '';

        // do we have a cookie var ?
        if (!$cookie) {
            $_SESSION["feedback_negative"][] = FEEDBACK_COOKIE_INVALID;
            return false;
        }

        // check cookie's contents, check if cookie contents belong together
        list ($user_id, $token, $hash) = explode(':', $cookie);
        if ($hash !== hash('sha256', $user_id . ':' . $token)) {
            $_SESSION["feedback_negative"][] = FEEDBACK_COOKIE_INVALID;
            return false;
        }

        // do not log in when token is empty
        if (empty($token)) {
            $_SESSION["feedback_negative"][] = FEEDBACK_COOKIE_INVALID;
            return false;
        }

        // get real token from database (and all other data)
        $query = $this->db->prepare("SELECT user_id, user_name, user_email, user_password_hash, user_active,
                                          user_account_type,  user_has_avatar, user_failed_logins, user_last_failed_login
                                     FROM signin
                                     WHERE user_id = :user_id
                                       AND user_rememberme_token = :user_rememberme_token
                                       AND user_rememberme_token IS NOT NULL
                                       AND user_provider_type = :provider_type");
        $query->execute(array(':user_id' => $user_id, ':user_rememberme_token' => $token, ':provider_type' => 'DEFAULT'));
        $count =  $query->rowCount();
        if ($count == 1) {
            // fetch one row (we only have one result)
            $result = $query->fetch();
            // TODO: this block is same/similar to the one from login(), maybe we should put this in a method
            // write data into session
            Session::init();
            Session::set('user_logged_in', true);
            Session::set('user_id', $result->user_id);
            Session::set('user_name', $result->user_name);
            Session::set('user_email', $result->user_email);
            Session::set('user_account_type', $result->user_account_type);
            Session::set('user_provider_type', 'DEFAULT');
            Session::set('user_avatar_file', $this->getUserAvatarFilePath());
            // call the setGravatarImageUrl() method which writes gravatar urls into the session
            $this->setGravatarImageUrl($result->user_email, AVATAR_SIZE);

            // generate integer-timestamp for saving of last-login date
            $user_last_login_timestamp = time();
            // write timestamp of this signin into database (we only write "real" logins via signin form into the
            // database, not the session-login on every page request
            $sql = "UPDATE signin SET user_last_login_timestamp = :user_last_login_timestamp WHERE user_id = :user_id";
            $sth = $this->db->prepare($sql);
            $sth->execute(array(':user_id' => $user_id, ':user_last_login_timestamp' => $user_last_login_timestamp));

            // NOTE: we don't set another rememberme-cookie here as the current cookie should always
            // be invalid after a certain amount of time, so the user has to signin with username/password
            // again from time to time. This is good and safe ! ;)
            $_SESSION["feedback_positive"][] = FEEDBACK_COOKIE_LOGIN_SUCCESSFUL;
            return true;
        } else {
            $_SESSION["feedback_negative"][] = FEEDBACK_COOKIE_INVALID;
            return false;
        }
    }

    /**
     * Tries to log the user in via Facebook-authentication
     * @return bool
     */
    public function loginWithFacebook()
    {
        // instantiate the facebook object
        $facebook = new Facebook(array('appId' => FACEBOOK_LOGIN_APP_ID, 'secret' => FACEBOOK_LOGIN_APP_SECRET));

        // get "user", if the user object (array?) exists, the user has identified as a real facebook user
        $user = $facebook->getUser();
        if ($user) {
            try {
                // proceed knowing you have a logged in user who's authenticated.
                $facebook_user_data = $facebook->api('/me');

                // check database for data from exactly that user (identified via Facebook ID)
                $query = $this->db->prepare("SELECT user_id,
                                              user_name,
                                              user_email,
                                              user_account_type,
                                              user_provider_type
                                           FROM signin
                                           WHERE user_facebook_uid = :user_facebook_uid
                                             AND user_provider_type = :provider_type");
                $query->execute(array(':user_facebook_uid' => $facebook_user_data["id"], ':provider_type' => 'FACEBOOK'));
                $count =  $query->rowCount();
                if ($count != 1) {
                    $_SESSION["feedback_negative"][] = FEEDBACK_FACEBOOK_LOGIN_NOT_REGISTERED;
                    return false;
                }

                $result = $query->fetch();
                // put user data into session
                Session::init();
                Session::set('user_logged_in', true);
                Session::set('user_id', $result->user_id);
                Session::set('user_name', $result->user_name);
                Session::set('user_email', $result->user_email);
                Session::set('user_account_type', $result->user_account_type);
                Session::set('user_provider_type', 'FACEBOOK');
                Session::set('user_avatar_file', $this->getUserAvatarFilePath());

                // generate integer-timestamp for saving of last-login date
                $user_last_login_timestamp = time();
                // write timestamp of this signin into database (we only write "real" logins via signin form into the
                // database, not the session-login on every page request
                $sql = "UPDATE signin SET user_last_login_timestamp = :user_last_login_timestamp WHERE user_id = :user_id";
                $sth = $this->db->prepare($sql);
                $sth->execute(array(':user_id' => $result->user_id, ':user_last_login_timestamp' => $user_last_login_timestamp));

                return true;

            } catch (FacebookApiException $e) {
                // when facebook goes offline
                error_log($e);
                $user = null;
            }
        }
        // default return
        return false;
    }

    /**
     * Log out process, deletes cookie, deletes session
     */
    public function signout()
    {
        // set the remember-me-cookie to ten years ago (3600sec * 365 days * 10).
        // that's obviously the best practice to kill a cookie via php
        // @see http://stackoverflow.com/a/686166/1114320
        setcookie('rememberme', false, time() - (3600 * 3650), '/', COOKIE_DOMAIN);

        // delete the session
        Session::destroy();
    }

    /**
     * Deletes the (invalid) remember-cookie to prevent infinitive signin loops
     */
    public function deleteCookie()
    {
        // set the rememberme-cookie to ten years ago (3600sec * 365 days * 10).
        // that's obviously the best practice to kill a cookie via php
        // @see http://stackoverflow.com/a/686166/1114320
        setcookie('rememberme', false, time() - (3600 * 3650), '/', COOKIE_DOMAIN);
    }

    /**
     * Returns the current state of the user's login
     * @return bool user's signin status
     */
    public function isUserLoggedIn()
    {
        return Session::get('user_logged_in');
    }

    /**
     * Perform the necessary actions to send a password reset mail
     * @return bool success status
     */
    public function requestPasswordReset()
    {
        if (!isset($_POST['user_name']) OR empty($_POST['user_name'])) {
            $_SESSION["feedback_negative"][] = FEEDBACK_USERNAME_FIELD_EMPTY;
            return false;
        }

        // generate integer-timestamp (to see when exactly the user (or an attacker) requested the password reset mail)
        $temporary_timestamp = time();
        // generate random hash for email password reset verification (40 char string)
        $user_password_reset_hash = sha1(uniqid(mt_rand(), true));
        // clean user input
        $user_name = strip_tags($_POST['user_name']);

        // check if that username exists
        $query = $this->db->prepare("SELECT user_id, user_email FROM signin
                                     WHERE user_name = :user_name AND user_provider_type = :provider_type");
        $query->execute(array(':user_name' => $user_name, ':provider_type' => 'DEFAULT'));
        $count = $query->rowCount();
        if ($count != 1) {
            $_SESSION["feedback_negative"][] = FEEDBACK_USER_DOES_NOT_EXIST;
            return false;
        }

        // get result
        $result_user_row = $result = $query->fetch();
        $user_email = $result_user_row->user_email;

        // set token (= a random hash string and a timestamp) into database
        if ($this->setPasswordResetDatabaseToken($user_name, $user_password_reset_hash, $temporary_timestamp) == true) {
            // send a mail to the user, containing a link with username and token hash string
            if ($this->sendPasswordResetMail($user_name, $user_password_reset_hash, $user_email)) {
                return true;
            }
        }
        // default return
        return false;
    }

    /**
     * Set password reset token in database (for DEFAULT user accounts)
     * @param string $user_name username
     * @param string $user_password_reset_hash password reset hash
     * @param int $temporary_timestamp timestamp
     * @return bool success status
     */
    public function setPasswordResetDatabaseToken($user_name, $user_password_reset_hash, $temporary_timestamp)
    {
        $query_two = $this->db->prepare("UPDATE signin
                                            SET user_password_reset_hash = :user_password_reset_hash,
                                                user_password_reset_timestamp = :user_password_reset_timestamp
                                          WHERE user_name = :user_name AND user_provider_type = :provider_type");
        $query_two->execute(array(':user_password_reset_hash' => $user_password_reset_hash,
                                  ':user_password_reset_timestamp' => $temporary_timestamp,
                                  ':user_name' => $user_name,
                                  ':provider_type' => 'DEFAULT'));

        // check if exactly one row was successfully changed
        $count =  $query_two->rowCount();
        if ($count == 1) {
            return true;
        } else {
            $_SESSION["feedback_negative"][] = FEEDBACK_PASSWORD_RESET_TOKEN_FAIL;
            return false;
        }
    }

    /**
     * send the password reset mail
     * @param string $user_name username
     * @param string $user_password_reset_hash password reset hash
     * @param string $user_email user email
     * @return bool success status
     */
    public function sendPasswordResetMail($user_name, $user_password_reset_hash, $user_email)
    {
        // create PHPMailer object here. This is easily possible as we auto-load the according class(es) via composer
        $mail = new PHPMailer;

        // please look into the config/config.php for much more info on how to use this!
        if (EMAIL_USE_SMTP) {
            // Set mailer to use SMTP
            $mail->IsSMTP();
            //useful for debugging, shows full SMTP errors, config this in config/config.php
            $mail->SMTPDebug = PHPMAILER_DEBUG_MODE;
            // Enable SMTP authentication
            $mail->SMTPAuth = EMAIL_SMTP_AUTH;
            // Enable encryption, usually SSL/TLS
            if (defined('EMAIL_SMTP_ENCRYPTION')) {
                $mail->SMTPSecure = EMAIL_SMTP_ENCRYPTION;
            }
            // Specify host server
            $mail->Host = EMAIL_SMTP_HOST;
            $mail->Username = EMAIL_SMTP_USERNAME;
            $mail->Password = EMAIL_SMTP_PASSWORD;
            $mail->Port = EMAIL_SMTP_PORT;
        } else {
            $mail->IsMail();
        }

        // build the email
        $mail->From = EMAIL_PASSWORD_RESET_FROM_EMAIL;
        $mail->FromName = EMAIL_PASSWORD_RESET_FROM_NAME;
        $mail->AddAddress($user_email);
        $mail->Subject = EMAIL_PASSWORD_RESET_SUBJECT;
        $link = EMAIL_PASSWORD_RESET_URL . '/' . urlencode($user_name) . '/' . urlencode($user_password_reset_hash);
        $mail->Body = EMAIL_PASSWORD_RESET_CONTENT . ' ' . $link;

        // send the mail
        if($mail->Send()) {
            $_SESSION["feedback_positive"][] = FEEDBACK_PASSWORD_RESET_MAIL_SENDING_SUCCESSFUL;
            return true;
        } else {
            $_SESSION["feedback_negative"][] = FEEDBACK_PASSWORD_RESET_MAIL_SENDING_ERROR . $mail->ErrorInfo;
            return false;
        }
    }

    /**
     * Verifies the password reset request via the verification hash token (that's only valid for one hour)
     * @param string $user_name Username
     * @param string $verification_code Hash token
     * @return bool Success status
     */
    public function verifyPasswordReset($user_name, $verification_code)
    {
        // check if user-provided username + verification code combination exists
        $query = $this->db->prepare("SELECT user_id, user_password_reset_timestamp
                                       FROM signin
                                      WHERE user_name = :user_name
                                        AND user_password_reset_hash = :user_password_reset_hash
                                        AND user_provider_type = :user_provider_type");
        $query->execute(array(':user_password_reset_hash' => $verification_code,
                              ':user_name' => $user_name,
                              ':user_provider_type' => 'DEFAULT'));

        // if this user with exactly this verification hash code exists
        if ($query->rowCount() != 1) {
            $_SESSION["feedback_negative"][] = FEEDBACK_PASSWORD_RESET_COMBINATION_DOES_NOT_EXIST;
            return false;
        }

        // get result row (as an object)
        $result_user_row = $query->fetch();
        // 3600 seconds are 1 hour
        $timestamp_one_hour_ago = time() - 3600;
        // if password reset request was sent within the last hour (this timeout is for security reasons)
        if ($result_user_row->user_password_reset_timestamp > $timestamp_one_hour_ago) {
            // verification was successful
            $_SESSION["feedback_positive"][] = FEEDBACK_PASSWORD_RESET_LINK_VALID;
            return true;
        } else {
            $_SESSION["feedback_negative"][] = FEEDBACK_PASSWORD_RESET_LINK_EXPIRED;
            return false;
        }
    }

    /**
     * Set the new password (for DEFAULT user, FACEBOOK-users don't have a password)
     * Please note: At this point the user has already pre-verified via verifyPasswordReset() (within one hour),
     * so we don't need to check again for the 60min-limit here. In this method we authenticate
     * via username & password-reset-hash from (hidden) form fields.
     * @return bool success state of the password reset
     */
    public function setNewPassword()
    {
        // basic checks
        if (!isset($_POST['user_name']) OR empty($_POST['user_name'])) {
            $_SESSION["feedback_negative"][] = FEEDBACK_USERNAME_FIELD_EMPTY;
            return false;
        }
        if (!isset($_POST['user_password_reset_hash']) OR empty($_POST['user_password_reset_hash'])) {
            $_SESSION["feedback_negative"][] = FEEDBACK_PASSWORD_RESET_TOKEN_MISSING;
            return false;
        }
        if (!isset($_POST['user_password_new']) OR empty($_POST['user_password_new'])) {
            $_SESSION["feedback_negative"][] = FEEDBACK_PASSWORD_FIELD_EMPTY;
            return false;
        }
        if (!isset($_POST['user_password_repeat']) OR empty($_POST['user_password_repeat'])) {
            $_SESSION["feedback_negative"][] = FEEDBACK_PASSWORD_FIELD_EMPTY;
            return false;
        }
        // password does not match password repeat
        if ($_POST['user_password_new'] !== $_POST['user_password_repeat']) {
            $_SESSION["feedback_negative"][] = FEEDBACK_PASSWORD_REPEAT_WRONG;
            return false;
        }
        // password too short
        if (strlen($_POST['user_password_new']) < 6) {
            $_SESSION["feedback_negative"][] = FEEDBACK_PASSWORD_TOO_SHORT;
            return false;
        }

        // check if we have a constant HASH_COST_FACTOR defined
        // if so: put the value into $hash_cost_factor, if not, make $hash_cost_factor = null
        $hash_cost_factor = (defined('HASH_COST_FACTOR') ? HASH_COST_FACTOR : null);

        // crypt the user's password with the PHP 5.5's password_hash() function, results in a 60 character hash string
        // the PASSWORD_DEFAULT constant is defined by the PHP 5.5, or if you are using PHP 5.3/5.4, by the password hashing
        // compatibility library. the third parameter looks a little bit shitty, but that's how those PHP 5.5 functions
        // want the parameter: as an array with, currently only used with 'cost' => XX.
        $user_password_hash = password_hash($_POST['user_password_new'], PASSWORD_DEFAULT, array('cost' => $hash_cost_factor));

        // write users new password hash into database, reset user_password_reset_hash
        $query = $this->db->prepare("UPDATE signin
                                        SET user_password_hash = :user_password_hash,
                                            user_password_reset_hash = NULL,
                                            user_password_reset_timestamp = NULL
                                      WHERE user_name = :user_name
                                        AND user_password_reset_hash = :user_password_reset_hash
                                        AND user_provider_type = :user_provider_type");

        $query->execute(array(':user_password_hash' => $user_password_hash,
                              ':user_name' => $_POST['user_name'],
                              ':user_password_reset_hash' => $_POST['user_password_reset_hash'],
                              ':user_provider_type' => 'DEFAULT'));

        // check if exactly one row was successfully changed:
        if ($query->rowCount() == 1) {
            // successful password change!
            $_SESSION["feedback_positive"][] = FEEDBACK_PASSWORD_CHANGE_SUCCESSFUL;
            return true;
        }

        // default return
        $_SESSION["feedback_negative"][] = FEEDBACK_PASSWORD_CHANGE_FAILED;
        return false;
    }

    /**
     * Upgrades/downgrades the user's account (for DEFAULT and FACEBOOK users)
     * Currently it's just the field user_account_type in the database that
     * can be 1 or 2 (maybe "basic" or "premium"). In this basic method we
     * simply increase or decrease this value to emulate an account upgrade/downgrade.
     * Put some more complex stuff in here, maybe a pay-process or whatever you like.
     */
    public function changeAccountType()
    {
        if (isset($_POST["user_account_upgrade"]) AND !empty($_POST["user_account_upgrade"])) {

            // do whatever you want to upgrade the account here (pay-process etc)
            // ...
            // ... myPayProcess();
            // ...

            // upgrade account type
            $query = $this->db->prepare("UPDATE signin SET user_account_type = 2 WHERE user_id = :user_id");
            $query->execute(array(':user_id' => $_SESSION["user_id"]));

            if ($query->rowCount() == 1) {
                // set account type in session to 2
                Session::set('user_account_type', 2);
                $_SESSION["feedback_positive"][] = FEEDBACK_ACCOUNT_UPGRADE_SUCCESSFUL;
            } else {
                $_SESSION["feedback_negative"][] = FEEDBACK_ACCOUNT_UPGRADE_FAILED;
            }
        } elseif (isset($_POST["user_account_downgrade"]) AND !empty($_POST["user_account_downgrade"])) {

            // do whatever you want to downgrade the account here (pay-process etc)
            // ...
            // ... myWhateverProcess();
            // ...

            $query = $this->db->prepare("UPDATE signin SET user_account_type = 1 WHERE user_id = :user_id");
            $query->execute(array(':user_id' => $_SESSION["user_id"]));

            if ($query->rowCount() == 1) {
                // set account type in session to 1
                Session::set('user_account_type', 1);
                $_SESSION["feedback_positive"][] = FEEDBACK_ACCOUNT_DOWNGRADE_SUCCESSFUL;
            } else {
                $_SESSION["feedback_negative"][] = FEEDBACK_ACCOUNT_DOWNGRADE_FAILED;
            }
        }
    }

}
