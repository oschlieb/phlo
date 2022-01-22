<!-- order -->
<div class="blue-box-title">
	<h2>Order<span class="help"><a href="<?php echo URL; ?>/help">Help</a></span></h2>
</div>
<div class="blue-box">
	<select id="orderby" name="orderby">
		<?php foreach ($this->order as $key => $value) : ?>
		<option value="<?php echo $key; ?>" <?php if($this->orderby==$key) { echo 'selected="selected"'; } ?>><?php echo ucfirst($key); ?></option>
		<?php endforeach; ?>
	</select>
</div>