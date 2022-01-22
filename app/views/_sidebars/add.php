<!-- add -->
<div class="blue-box-title">
	<h2>Add<span class="help"><a href="<?php echo URL; ?>/help">Help</a></span></h2>
</div>
<div class="blue-box">	
	<form action="<?php echo URL; ?>account/add" method="post" id="add" class="clear-fix">
		<p><label for="add" class="block">Name:</label><input type="text" id="add" name="add_name" value="" required /></p>
		<p><label for="add" class="block">Type:</label>
			<select id="type" name="add_type" required>
				<option selected="selected" value="">Select type…</option>
				<?php for ($i = 0; $i < count($this->types); $i++) : ?>
					<?php $t_array = (array)$this->types[$i]; ?>
					<?php foreach ($t_array as $key => $value) : ?>
						<option value="<?php echo $value; ?>"><?php echo ucfirst($value); ?></option>
					<?php endforeach; ?>
				<?php endfor; ?>
			</select>
		</p>
		<input type="submit" value="Add new" class="btn submit pad-top" />
	</form>
</div>