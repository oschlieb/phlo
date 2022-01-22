<!-- import -->
<?php $import = 0; ?>
<?php if($import) : ?>
<div class="blue-box-title">
	<h2>Import<span class="help"><a href="<?php echo URL; ?>/help">Help</a></span></h2>
</div>
<div class="blue-box">	
	<form action="#" id="add">
	<!-- <p><label for="add" class="block">Select file:</label><input type="text" id="import" name="import" value="" /></p> -->
		<input type="submit" value="Select file" class="btn submit pad-top" />
	</form>
	<p style="margin-top:10px;">CSV files only, for full requirements read our <a href="#">import requirements</a>.</p>
</div>
<?php endif; ?>