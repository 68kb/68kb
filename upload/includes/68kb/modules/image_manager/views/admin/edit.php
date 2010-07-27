<script type="text/javascript">
jQuery(document).ready(function($) {				
	$('#width').keyup(function() {
		var width = parseInt($("#width").val());
		$("#height").val(change_value(width, "width"));
	});
});
var ratio_width = '<?php echo $height / $width; ?>';
var ratio_height = '<?php echo $width / $height; ?>';
function change_value(size, type)
{
	ratio = (type == "height") ? ratio_height : ratio_width;
	result = Math.floor(ratio * size);
	return result;
}
</script>
<div id="edit">
	<div class="photo">
		<div class="photo-wrap">
			<img src="<?php echo $thumb ?>" />
		</div>
	</div>
	
<?php echo form_open_multipart('admin/image_manager/edit/'); ?>
	<?php echo form_hidden('orig_image', $image); ?>
	<?php echo form_hidden('image', $thumb); ?>
	<?php echo form_hidden('field', $field); ?>
	
	<fieldset>
		<legend>Resize</legend>
		<label for="width">* <?php echo lang('lang_width'); ?>:</label><input type="text" name="width" id="width" value="<?php echo $width ?>" size="4" />
		<label for="height">* <?php echo lang('lang_height'); ?>:</label><input type="text" name="height" id="height" value="<?php echo $height ?>" size="4" />
		<?php echo form_submit('submit', lang('lang_edit'), 'class="button"'); ?>
	</fieldset>
	
	<input type="button" class="button" name="Insert" value="<?php echo lang('lang_insert'); ?>" onclick="send_text('<?php echo $image; ?>', <?php echo $width ?>, <?php echo $height ?>);" />
	
<?php echo form_close(); ?>

</div>
<div class="clear"></div>
