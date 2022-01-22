<h1>Profile</h1>
    
<div id="col-left">
    
    <?php 
    	$profile_array = '';
    	if($this->profile) {
	    	if (!$_POST) { 
    			$profile_array = (array)$this->profile[0]; 
    		} else { 
	    		$profile_array = $_POST; 
		    }
		}
   ?>   
                
    <form action="<?php echo URL; ?>profile/edit" method="post">    
	<?php if ($profile_array) : ?>	
		<input type="hidden" id="validate" name="validate" value="" />
		
		<!-- email and name -->
		<div class="grey-box">
			<p>
				<label for="email" class="block">Email</label>
				<input type="text" id="email" name="email" class="onchange" value="<?php echo Session::get('user_email'); ?>" required>
			</p>	
			<p>
				<label for="profile_name" class="block">Profile name</label>
				<input type="text" id="profile_name" name="profile_name" class="onchange" value="<?php echo Session::get('user_name'); ?>" required>
			</p>
	    </div>
	    
	    <!-- password -->
	    <div class="grey-box">
	    	<p>
	    		<label for="password_new" class="block">Password</label>
	    		<input type="password" id="password_new" name="password_new" value="**********" pattern=".{6,}" required autocomplete="off" />
	    	</p>
	        <p>
	        	<label for="password_repeat" class="block">Repeat password</label>
	        	<input type="password" id="password_repeat" name="password_repeat" value="**********" class="onchange" pattern=".{6,}" required autocomplete="off" />
	        </p>
		</div>
		
		<!-- personal -->
		<div class="grey-box">
    	<?php foreach($profile_array as $key => $value) : ?>
    		<?php if($key=='id' || $key=='user_id' || $key=='validate' || $key=='email' || $key=='profile_name' || $key=='password_new' || $key=='password_repeat') : ?>
    			<?php // do nothing ?>
    		<?php else: ?>
    			<p>
    				<label for="<?php echo $key; ?>" class="block"><?php echo ucfirst(str_replace("_"," ",$key)); ?></label>
    				<input type="text" name="<?php echo $key; ?>" value="<?php echo $value; ?>" class="onchange" <?php if(in_array($key, $this->reqfields)) : ?>_required<?php endif; ?> />
    			</p>
			<?php endif; ?>                                
    	<?php endforeach; ?>          
    	</div>
    	
    <?php endif; ?>    
		<input type="submit" value="Update profile" class="btn submit" />		
    </form>

</div>

<!-- column right -->
<div id="col-right">
	<?php $this->renderIncludes('_sidebars/deleteaccount'); ?>		
</div>

<?php 
	/* // upgrade… echo Session::get('user_account_type'); */
?>