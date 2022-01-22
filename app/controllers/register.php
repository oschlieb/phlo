<?php

/**
 * Register Controller
 * Controls the login processes
 */

class Register extends Controller
{
    /**
     * Construct this object by extending the basic Controller class
     */
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Register page
     * Show the register form (with the register-with-facebook button). We need the facebook-register-URL for that.
     */
    function index()
    {
        $register_model = $this->loadModel('Register');
        
    	// create global objects
        $this->view->meta = $register_model->setMeta();   
        $this->view->crumbs = $register_model->setBreadcrumbs();        

        // if we use Facebook: this is necessary as we need the facebook_register_url in the login form (in the view)
        if (FACEBOOK_LOGIN == true) {
            $this->view->facebook_register_url = $register_model->getFacebookRegisterUrl();
        }
        $this->view->render('register/index');
    }

    /**
     * Register page
     */
    function success()
    {
        $register_model = $this->loadModel('Register');
        
    	// create global objects
        $this->view->meta = $register_model->setMeta();   
        $this->view->crumbs = $register_model->setBreadcrumbs();        
        
        $this->view->render('register/success');
    }
    
    /**
     * Register page action (after form submit)
     */
    function register_action()
    {
        $register_model = $this->loadModel('Register');
        $registration_successful = $register_model->registerNewUser();

        if ($registration_successful == true) {
            header('location: ' . URL . 'register/success');
        } else {
            header('location: ' . URL . 'register/');
        }
    }

    /**
     * Register a user via Facebook-authentication
     */
    function registerWithFacebook()
    {
        $register_model = $this->loadModel('Register');
        // perform the register method, put result (true or false) into $registration_successful
        $registration_successful = $register_model->registerWithFacebook();

        // check registration status
        if ($registration_successful) {
            // if YES, then move user to login/index (this is a browser-redirection, not a rendered view)
            header('location: ' . URL . 'account/');
        } else {
            // if NO, then move user to login/register (this is a browser-redirection, not a rendered view)
            header('location: ' . URL . 'register/');
        }
    }

    /**
     * Verify user after activation mail link opened
     * @param int $user_id user's id
     * @param string $user_activation_verification_code sser's verification token
     */
    function verify($user_id, $user_activation_verification_code)
    {
        if (isset($user_id) && isset($user_activation_verification_code)) {
            $register_model = $this->loadModel('Register');
        
	    	// create global objects
    	    $this->view->meta = $register_model->setMeta();   
        	$this->view->crumbs = $register_model->setBreadcrumbs();        
            
            $register_model->verifyNewUser($user_id, $user_activation_verification_code);
            $register_model->addUserProfile($user_id);            
            $this->view->render('register/verify');
        } else {
            header('location: ' . URL . 'register/');
        }
    }

    /**
     * Generate a captcha, write the characters into $_SESSION['captcha'] and returns a real image which will be used
     * like this: <img src="......./login/showCaptcha" />
     * IMPORTANT: As this action is called via <img ...> AFTER the real application has finished executing (!), the
     * SESSION["captcha"] has no content when the application is loaded. The SESSION["captcha"] gets filled at the
     * moment the end-user requests the <img .. >
     * If you don't know what this means: Don't worry, simply leave everything like it is ;)
     */
    function showCaptcha()
    {
        $register_model = $this->loadModel('Register');
        $register_model->generateCaptcha();
    }
}
