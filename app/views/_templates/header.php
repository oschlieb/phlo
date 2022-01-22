<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php 
if(isset($this->meta)) { 
	$_name = $this->meta['title'];
	$_description = $this->meta['description']; 	
} else {
	$_name = 'My';
	$_description = 'Werkphlo is a new marketing, sales and customer service web application for small businesses.'; 
} 
?>

<title><?php echo $_name; ?> | WerkPhlo</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="<?php echo $_description; ?>">

<!-- scripts -->
<script type="text/javascript" src="//code.jquery.com/jquery-1.10.2.js"></script>
<script type="text/javascript" src="//code.jquery.com/ui/1.11.0/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo URL; ?>public/js/common.js"></script>

<!-- styles -->
<link rel="stylesheet" type="text/css" href="<?php echo URL; ?>public/css/style.css" />

<!-- page specific -->
<?php if(isset($this->assets)) : ?>
<?php if($this->assets['js']): ?>
<script type="text/javascript" src="<?php echo URL; ?>public/js/<?php echo $this->assets['js']; ?>.js"></script>
<?php endif; ?>
<?php if($this->assets['css']): ?>
<link rel="stylesheet" href="<?php echo URL; ?>public/css/<?php echo $this->assets['css']; ?>.css" />
<?php endif; ?>
<?php endif; ?>

</head>
<body>

<!-- header -->
<header id="header">

	<!-- title -->
	<div id="title">
		<div class="title-wrap">
			<div class="title"><a href="<?php echo URL; ?>">Werkphlo</a></div>
			<div class="account">
			<?php if (Session::get('user_logged_in') == true): ?>
				<a href="<?php echo URL; ?>signin/signout">Sign out</a>
			<?php else : ?>
				<a href="<?php echo URL; ?>signin">Sign in</a> &middot; <a href="<?php echo URL; ?>register">Register</a></div>			
			<?php endif; ?>			
		</div>
	</div>
	
	<!-- navigation -->
	<div id="nav">
		<div class="nav-wrap">		
			<ul id="menu">
	            <li><a href="http://www.werkphlo.com/products" style="padding-left:0px;">Products</a></li>
        		<li><a href="http://www.werkphlo.com/pricing">Pricing</a></li>
        		<li><a href="http://www.werkphlo.com/resources">Resources</a></li>
        		<li><a href="http://www.werkphlo.com/about">About</a></li>
        		<li><a href="http://www.werkphlo.com/contact">Contact</a></li>
    	    <?php if (Session::get('user_logged_in') == true): //for logged in users ?>
        	    <li <?php if ($this->checkForActiveController($filename, "account")) { echo ' class="active" '; } ?> >
            	    <a href="<?php echo URL; ?>account">Account</a>
	            </li>
	        <?php endif; ?>
        	<?php if (Session::get('user_logged_in') == false): //for not logged in users ?>        		
    	    <?php endif; ?>        
        	</ul>
        </div>	
	</div>

	<!-- breadcrumbs -->
	<div id="crumbs">
		<div class="crumbs-wrap"><a href="<?php echo URL; ?>">Home</a> 
		<?php if(isset($this->crumbs)) : ?>
			<?php foreach($this->crumbs as $key => $value) : ?>
				<?php if ($key == "link") : ?>
					&raquo; <a href="<?php echo URL; ?><?php echo strtolower(preg_replace('/\s+/', '', $value)); ?>"><?php echo $value; ?></a>
				<?php else : ?>
					&raquo; <?php echo $value; ?>
				<?php endif; ?>
			<?php endforeach; ?>
		<?php endif; ?>
		</div>	
	</div>

</header>

<!-- main -->
<div id="main" class="content">
	<div class="main-wrap">
	
	<?php $this->renderFeedbackMessages(); ?>