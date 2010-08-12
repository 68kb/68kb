
<?php echo form_open('setup/install'); ?>

	<p>Welcome to the 68KB setup!</p>
	
	<div id="license"><?php echo auto_typography(lang('license'))?></div>
	
	<a href="<?php echo site_url('setup/install') ?>" class="button"><?php echo lang('agree') ?></a>

<?php echo form_close(); ?>