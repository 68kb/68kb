
<div id="photos">
	<?php $i=0; foreach ($images as $row) { ?>
		<?php list($width, $height, $type, $attr) = getimagesize($row['full']); ?>
		<div class="photo">
			<div class="photo-wrap">
				<img src="<?php echo $row['src'] ?>" class="show" id="img_<?php echo $i; ?>" />
				
				<p>
					<?php echo $row['name'] ?>
					<div class="properties" id="prop_<?php echo $i; ?>">
						
						<?php echo form_open_multipart('admin/image_manager/edit/'); ?>
							
							<?php echo form_hidden('image', $row['src']); ?>
							<?php echo form_hidden('field', $field); ?>
							<?php echo form_submit('submit', lang('lang_edit'), 'class="button"'); ?>
							<input type="button" class="button" name="Insert" value="<?php echo lang('lang_insert'); ?>" onclick="send_text('<?php echo $row['full']; ?>', <?php echo $width ?>, <?php echo $height ?>);" />
						<?php echo form_close(); ?>
						
						
						
					</div>
				</p>
			</div>
		</div>
	<?php $i++; } ?>
</div>
<div class="clear"></div>