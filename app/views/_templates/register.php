<!-- register -->
<?php $uid = uniqid(); ?>
<div id="col-2">
	<div class="grey-box">
		<h2>Register</h2>
	    <form method="post" action="<?php echo URL; ?>register/register_action" name="registerform">
	    	<input type="hidden" name="unique_id" value="_<?php echo $uid; ?>">
    		<p>
    			<label class="block">Profile name</label>
    			<input id="login_input_username" class="login_input" type="text" pattern="[a-zA-Z0-9]{2,64}" name="user_name" required />
    		</p>
        	<p>
        		<label class="block">Email</label>
        		<input id="login_input_email" class="login_input" type="email" name="user_email" required />
        	</p>
            <p>
            	<label class="block">Password</label>
            	<input id="login_input_password_new" class="login_input" type="password" name="user_password_new" pattern=".{6,}" required autocomplete="off" />
            </p>
	        <p>
	        	<label class="block">Repeat password</label>
	        	<input id="login_input_password_repeat" class="login_input" type="password" name="user_password_repeat" pattern=".{6,}" required autocomplete="off" />
	        </p>
    	    <p>
    	    	<label class="block">Captcha code</label>
    	    	<input type="text" name="captcha" autocomplete="off" required />
    	    </p>
    	    <p>
    	    	<img id="captcha" src="<?php echo URL; ?>register/showCaptcha" /><br/>
    	    	<a href="#" onclick="document.getElementById('captcha').src = '<?php echo URL; ?>register/showCaptcha?' + Math.random(); return false">Reload Captcha</a>
    	    </p>
	        <input type="submit" value="Register with Werkphlo" class="btn submit"/>
    	</form>
        <?php if (FACEBOOK_LOGIN == true) { ?>
            <h1>or</h1>
	        <a href="<?php echo $this->facebook_register_url; ?>" class="facebook-login-button">Register with Facebook</a>
    	<?php } ?>			
	</div>
</div>	