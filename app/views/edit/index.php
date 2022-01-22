<?php 
	$phlo_info = (array)$this->phlo[0]; 
	$parent = (array)$this->parent_obj[0]; 
?>
<?php 
	//print_r($parent); 
	//echo $parent['haschild'];
?>

<!-- title -->
<h1>Edit [<?php echo $phlo_info['name']; ?>]</h1>
<p>&nbsp;</p>

<!-- column left -->
<div id="col-left">

	<?php if(isset($parent['haschild'])) : ?>
	<a href="#">Add item</a>
	<?php endif; ?>
     
	<!-- form -->
	<form action="<?php echo URL; ?>edit/" method="post" id="phlo-form">
	<input type="hidden" name="phlo-order" id="phlo-order" value="" />
		<ol id="phlo" class="sort">		
	  		<?php $this->renderIncludes('_tools/' . $phlo_info['type']); ?>										
		</ol>	
		<?php if(isset($parent['haschild'])) : ?>
		<div class="save">
			<input type="submit" id="save" value="Save changes" class="btn submit" />
			<pre id="output"></pre>
		</div>
		<?php endif; ?>	
	</form>
  
</div>

<!-- column right -->
<div id="col-right">
	<?php $this->renderIncludes('_sidebars/information'); ?>
	<?php $this->renderIncludes('_sidebars/generate'); ?>		
</div>