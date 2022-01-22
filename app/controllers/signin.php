<?php

/**
 * Login Controller
 * Controls the login processes
 */

class Signin extends Controller
{
    /**
     * Construct this object by extending the basic Controller class
     */
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Index, default action (shows the login form), when you do login/index
     */
    function index()
    {

        // create a login model to perform the getFacebookLoginUrl() method
        $signin_model = $this->loadModel('Signin');
        
        // if we use Facebook: this is necessary as we need the facebook_login_url in the login form (in the view)
        if (FACEBOOK_LOGIN == true) {
            $this->view->facebook_login_url = $signin_model->getFacebookLoginUrl();
        }
	
		// get globals	
        $this->view->meta = $signin_model->setMeta("signin");        
        $this->view->crumbs = $signin_model->setBreadcrumbs("signin");
		
        // show the view
        $this->view->render('signin/index');
    }
    
    /**
     * The login action, when you do login/login
     */
    function signin()
    {
        // run the login() method in the login-model, put the result in $login_successful (true or false)
        $signin_model = $this->loadModel('Signin');
        // perform the login method, put result (true or false) into $login_successful
        $login_successful = $signin_model->signin();
        
        // check login status
        if ($login_successful) {
            // if YES, then move user to dashboard/index (btw this is a browser-redirection, not a rendered view!)
            header('location: ' . URL . 'account/');
        } else {
            // if NO, then move user to login/index (login form) again
            header('location: ' . URL . 'signin/');
        }
    }

    /**
     * The login action, this is where the user is directed after being checked by the Facebook server by
     * clicking the facebook-login button
     */
    function loginWithFacebook()
    {
        // run the login() method in the login-model, put the result in $login_successful (true or false)
        $signin_model = $this->loadModel('Signin');
        $login_successful = $signin_model->loginWithFacebook();

        // check login status
        if ($login_successful) {
            // if YES, then move user to dashboard/index (this is a browser-redirection, not a rendered view)
            header('location: ' . URL . 'account/');
        } else {
            // if NO, then move user to login/index (login form) (this is a browser-redirection, not a rendered view)
            header('location: ' . URL . 'signin/');
        }
    }

    /**
     * The logout action, login/logout
     */
    function signout()
    {
        $signin_model = $this->loadModel('Signin');
        $signin_model->signout();
        // redirect user to base URL
        header('location: ' . URL);
    }

    /**
     * Login with cookie
     */
    function loginWithCookie()
    {
        // run the loginWithCookie() method in the login-model, put the result in $login_successful (true or false)
        $signin_model = $this->loadModel('Signin');
        $login_successful = $signin_model->loginWithCookie();

        if ($login_successful) {
            header('location: ' . URL . 'account/');
        } else {
            // delete the invalid cookie to prevent infinite login loops
            $signin_model->deleteCookie();
            // if NO, then move user to login/index (login form) (this is a browser-redirection, not a rendered view)
            header('location: ' . URL . 'signin/');
        }
    }

    /**
     * Request password reset page
     */
    function requestPasswordReset()
    {
    	// set model
    	$signin_model = $this->loadModel('Signin');
    
    	// get globals		
        $this->view->meta = $signin_model->setMeta("requestpasswordreset");        
        $this->view->crumbs = $signin_model->setBreadcrumbs("requestpasswordreset");
        
        // render page
        $this->view->render('signin/requestpasswordreset');
    }

    /**
     * Request password reset action (after form submit)
     */
    function requestPasswordReset_action()
    {
        $signin_model = $this->loadModel('Signin');    
        
		// get globals		
        $this->view->meta = $signin_model->setMeta("requestpasswordreset");        
        $this->view->crumbs = $signin_model->setBreadcrumbs("requestpasswordreset");
                
        $signin_model->requestPasswordReset();
        $this->view->render('signin/requestpasswordreset');
    }

    /**
     * Verify the verification token of that user (to show the user the password editing view or not)
     * @param string $user_name username
     * @param string $verification_code password reset verification token
     */
    function verifyPasswordReset($user_name, $verification_code)
    {
        $signin_model = $this->loadModel('Signin');
        if ($signin_model->verifyPasswordReset($user_name, $verification_code)) {
  
            // get globals		
        	$this->view->meta = $signin_model->setMeta("verifypasswordreset");        
        	$this->view->crumbs = $signin_model->setBreadcrumbs("verifypasswordreset");

            // get variables for the view
            $this->view->user_name = $user_name;
            $this->view->user_password_reset_hash = $verification_code;
            $this->view->render('signin/changepassword');
        } else {
            header('location: ' . URL . 'signin/');
        }
    }

    /**
     * Set the new password
     * Please note that this happens while the user is not logged in.
     * The user identifies via the data provided by the password reset link from the email.
     */
    function setNewPassword()
    {
        $signin_model = $this->loadModel('Signin');
        // try the password reset (user identified via hidden form inputs ($user_name, $verification_code)), see
        // verifyPasswordReset() for more
                
        $signin_model->setNewPassword();
        // regardless of result: go to index page (user will get success/error result via feedback message)
        header('location: ' . URL . 'signin/');
    }

}
