<div class="grid_16">
	<h2><?php echo lang('lang_delete_module'); ?></h2>
	<p><?php echo lang('lang_delete_module_txt'); ?></p>
	
	<?php if(isset($error)) {
		echo '<div class="error">'.$error,'</div>';
	} ?>
	<?php if(validation_errors()) {
		echo '<div class="error"><h3>'.lang('lang_errors_occured').'</h3> '.validation_errors().'</div>';
	}
	?>
	
	<?php
		$options = array(
			'db'    => 'Just the db tables',
			'all'  => 'Delete Everything',
		);
	?>
	<?php 
		echo form_open('admin/addons/delete'); 
		echo form_hidden('module_directory', $module_directory);
	?>
	<p class="row1">
		<?php echo form_label('Delete:', 'delete'); ?>
		<?php echo form_dropdown('delete', $options); ?>
	</p>
	<p class="row2">
	<?php
		echo '<p>'.form_submit('mysubmit', 'Submit');
		echo '&nbsp; &nbsp; or ';
		echo '<a href="'.site_url('admin/addons/').'">Get me out of here</a> </p>';
	?>
	</p>
	<?php echo form_close(); ?>
	
</div>