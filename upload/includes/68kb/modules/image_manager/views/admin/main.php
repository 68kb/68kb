<?php echo form_open_multipart('admin/image_manager/upload/'.$field); ?>
	
	<?php echo form_label('Image:', 'user_file'); ?>
	<?php echo form_upload('userfile'); ?>
	<?php echo form_hidden('field', $field); ?>
	<?php echo form_submit('submit', 'Submit!'); ?>
	
<?php echo form_close(); ?>

<?php if (isset($error)) echo $error; ?>

<?php if (isset($thumb)): ?>

<div id="photos">
	<div class="photo">
		<div class="photo-wrap">
			<?php list($width, $height, $type, $attr) = getimagesize($full); ?>
			<img src="<?php echo $thumb ?>" onclick="send_text('<?php echo $full; ?>', <?php echo $width ?>, <?php echo $height ?>);" />
			
			<p>
				
			<?php echo form_open_multipart('admin/image_manager/edit/'); ?>
				
				<?php echo form_hidden('image', $thumb); ?>
				<?php echo form_hidden('field', $field); ?>
				<?php echo form_submit('submit', lang('lang_edit'), 'class="button"'); ?>
				<input class="button" type="button" name="Insert" value="<?php echo lang('lang_insert'); ?>" onclick="send_text('<?php echo $full; ?>', <?php echo $width ?>, <?php echo $height ?>);" />
			<?php echo form_close(); ?>
			
			</p>
		</div>
	</div>
</div>
<div class="clear"></div>

<?php endif; ?>