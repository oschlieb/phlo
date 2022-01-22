<!-- signin -->
<div id="col-2" class="pad">
	<div class="grey-box">
		<h2>Sign-in</h2>
       	<form action="<?php echo URL; ?>signin/signin" method="post">
        	<p><label class="block">Email</label><input type="text" name="user_name" autocomplete="off" required /></p>
            <p><label class="block">Password</label><input type="password" name="user_password" autocomplete="off" required /></p>
            <p><input type="checkbox" name="user_rememberme" class="remember-me-checkbox" /> <label>Stay signed in</label></p>
            <input type="submit" value="Sign into your account" class="btn submit" />                
        </form>        	
       	<a href="<?php echo URL; ?>signin/requestpasswordreset">Reset password</a>
        <?php if (FACEBOOK_LOGIN == true) : ?>
        	<h1>or</h1>
        	<a href="<?php echo $this->facebook_login_url; ?>" class="facebook-login-button">Log in with Facebook</a>
    	<?php endif; ?>
	</div>
</div>