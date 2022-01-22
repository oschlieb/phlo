<!-- information -->
<?php $phlo_info = (array)$this->phlo[0]; ?>
<div class="blue-box-title">
	<h2>Information<span class="help"><a href="/help">Help</a></span></h2>
</div>
<div class="blue-box">
	<p><strong>Name:</strong> <?php echo $phlo_info['name']; ?><br/><strong>Type:</strong> <?php echo $phlo_info['type']; ?><br/><strong>Style:</strong> <?php echo $phlo_info['style']; ?></p>
	<a href="/edit/update/info" class="btn anchor">Edit information</a>	
	<!-- 
	<form action="<?php //echo URL; ?>edit/update/info" method="post" id="add" class="clear-fix">
		<p>
			<label for="name" class="block">Name:</label>
			<input type="text" id="name" name="name" value="<?php //echo $phlo_info['name']; ?>" required />
		</p>
		<p>
			<label for="type" class="block">Type:</label>
			<input type="text" id="type" name="type" value="<?php //echo $phlo_info['type']; ?>" required />
		</p>
		<p>
			<label for="style" class="block">Style:</label>
			<input type="text" id="style" name="style" value="<?php //echo $phlo_info['style']; ?>" required />
		<p>
	</form>
	-->		
</div>