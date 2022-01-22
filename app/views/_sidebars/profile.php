<!-- profile -->
<div class="blue-box-title">
	<h2>Profile<span class="help"><a href="<?php echo URL; ?>/help">Help</a></span></h2>
</div>
<div class="blue-box">
	<?php if($this->profile != '') : ?>
	<p><?php echo $this->profile; ?></p>
	<a href="/profile" class="btn anchor pad-top">Edit profile</a>
	<?php else: ?>
	<p>Add profile information.</p>		
	<a href="/profile" class="btn anchor">Add profile</a>
	<?php endif; ?>		
</div>
