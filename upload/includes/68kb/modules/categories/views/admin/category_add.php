<?php 
if ($this->events->active_hook('category/form')) {
	$this->events->trigger('category/form');
} else {
	echo '<script src="js/js-quicktags/js_quicktags.js" type="text/javascript"></script>';
} 
?>
<div class="grid_16">
	<h2><?php echo (isset($action) && $action == 'add') ? lang('lang_add_category') : lang('lang_duplicate'); ?></h2>
</div>

<?php if(isset($error)) {
	echo '<div class="error">'.$error,'</div>';
} ?>
<?php if(validation_errors()) {
	echo '<div class="error"><h3>'.lang('lang_errors_occured').'</h3> '.validation_errors().'</div>';
}
?>

<?php echo form_open_multipart('admin/categories/add'); ?>
<div id="form">
	<div class="grid_9">
		
		<div class="row1">
			<?php echo form_label(lang('lang_title'). ': <em>('.lang('lang_required').')</em>', 'cat_name'); ?>
			<?php echo form_input('cat_name', set_value('cat_name', $cat['cat_name']), 'class="inputtext"'); ?>
		</div>
		<div class="row2">
			<?php echo form_label(lang('lang_uri') . ': '. tooltip(lang('lang_uri_desc')), 'cat_uri'); ?>
			<?php echo form_input('cat_uri', set_value('cat_uri', $cat['cat_uri']), 'class="inputtext"'); ?>
			
		</div>
		
		<div class="row1">
			<?php echo form_label(lang('lang_description'). ':', 'cat_description'); ?>
			<div class="toolbar"><script type="text/javascript">if(typeof edToolbar=='function') edToolbar('cat_description');</script></div>
			<?php echo form_textarea('cat_description', set_value('cat_description', $cat['cat_description']), 'id="cat_description" class="inputtext"'); ?>
		</div>
		<div class="row2">
			<?php echo form_label(lang('lang_promo'). ': '. tooltip(lang('lang_promo_desc')), 'cat_promo'); ?>
			<div class="toolbar"><script type="text/javascript">if(typeof edToolbar=='function') edToolbar('cat_description');</script></div>
			<?php echo form_textarea('cat_promo', set_value('cat_promo', $cat['cat_promo']), 'id="cat_promo" class="inputtext"'); ?>
		</div>		
		<div class="submit">
			<?php echo form_submit('submit', lang('lang_save'), 'class="save"'); ?>
		</div>
	</div>
	<div class="grid_7 inline">
		<div class="row1">
			<?php echo form_label(lang('lang_display'). ':', 'cat_display'); ?>
			<?php $options = array('yes' => lang('lang_yes'), 'no' => lang('lang_no')); ?>
			<?php echo form_dropdown('cat_display', $options, set_value('cat_display', $cat['cat_display'])); ?>
		</div>
		<div class="row2">
			<?php echo form_label(lang('lang_allow_ads'). ':', 'cat_allowads'); ?>
			<?php $options = array('yes' => lang('lang_yes'), 'no' => lang('lang_no')); ?>
			<?php echo form_dropdown('cat_allowads', $options, set_value('cat_allowads', $cat['cat_allowads'])); ?>
		</div>
		<div class="row1">
			<?php echo form_label(lang('lang_parent_cat'). ':', 'cat_parent'); ?>
			<?php 
			$cat_options['0'] = lang('lang_no_parent');
			foreach($tree as $row)
			{
				$indent = ($row['cat_parent'] != 0) ? repeater('&nbsp;&raquo;&nbsp;', $row['depth']) : '';
				$cat_options[$row['cat_id']] = $indent.$row['cat_name']; 
			}	
			echo form_dropdown('cat_parent', $cat_options, $cat['cat_parent'], 'id="cat_parent"');
			?>
		</div>
		<div class="row2">
			<?php echo form_label(lang('lang_site_meta_keywords'). ':', 'cat_keywords'); ?>
			<?php echo form_input('cat_keywords', set_value('cat_keywords', $cat['cat_keywords']), 'size="25"'); ?>
			<?php echo tooltip(lang('lang_keywords_desc')); ?>
		</div>
		
		<div class="row1">
			<?php echo form_label(lang('lang_weight'). ':', 'cat_order'); ?>
			<?php echo form_input('cat_order', set_value('cat_order', $cat['cat_order']), 'size="25"'); ?>
			<?php echo tooltip(lang('lang_weight_desc')); ?>
		</div>
		
		<div class="row2">
			<?php echo form_label(lang('lang_cat_image'). ':', 'userfile'); ?>
			<?php echo form_upload('userfile', '', 'id="userfile"'); ?>
		</div>
		
		
		
	</div>
</div>
</form>