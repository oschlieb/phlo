<h1>Account</h1>

<!-- column left -->
<div id="col-left">
		
	<!-- list phlos -->	
	<div class="clear-box">
		<div class="manage-table">
			<div class="row">
				<?php echo $this->order_direction; ?>
				<div class="col"><a href="#">Manage</a></div>
			</div>
		</div>
	</div>
	
	<?php if($this->phlos) : ?>
	<?php for ($i = 0; $i < count($this->phlos); $i++) : ?>
	<?php $p_array = (array)$this->phlos[$i]; ?>
	<div class="grey-box">
		<div class="manage-table">
			<div class="row">
				<div class="col pad-right"><strong><?php echo $p_array['name']; ?></strong></div>
				<div class="col pad-right"><?php echo $p_array['type']; ?></div>
				<div class="col pad-right"><?php echo $p_array['style']; ?></div>
				<div class="col pad-right"><?php echo date("d-m-y", $p_array['created']); ?></div>
				<div class="col"><a href="<?php echo URL; ?>edit/id/<?php echo $p_array['phid']; ?>">Edit</a> &middot; <a href="<?php echo URL; ?>account/delete/<?php echo $p_array['phid']; ?>" class="delete">Delete</a></div>
			</div>
		</div>
	</div>
	<?php endfor; ?>
	<?php endif; ?>
	
</div>		
		
<!-- column right -->
<div id="col-right">
	<?php $this->renderIncludes('_sidebars/profile'); ?>
	<?php $this->renderIncludes('_sidebars/search'); ?>
	<?php $this->renderIncludes('_sidebars/add'); ?>
	<?php $this->renderIncludes('_sidebars/import'); ?>
</div>