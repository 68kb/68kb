
<?php echo form_open('setup/index'); ?>

	<p>Please enter your license key in order to continue.</p>
	
	<?php if(validation_errors()) {
		echo '<div class="error">'.validation_errors().'</div>';
	}
	?>
	
	<p><?php echo form_input('license_key', set_value('license_key'), 'class="key" size="50"'); ?></p>
	
	<input class="button" type="submit" name="submit" onClick="document.location = '<?php echo site_url('setup/install/'); ?>'" value="Proceed with Setup" />
	
<?php echo form_close(); ?>
