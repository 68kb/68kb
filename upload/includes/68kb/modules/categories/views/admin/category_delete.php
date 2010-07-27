<div class="grid_16">
	<h2><?php echo lang('lang_editcat'); ?></h2>
</div>

<?php if(isset($error)) {
	echo '<div class="error">'.$error,'</div>';
} ?>
<?php if(validation_errors()) {
	echo '<div class="error"><h3>'.lang('lang_errors_occured').'</h3> '.validation_errors().'</div>';
}
?>

<?php echo form_open('admin/categories/delete/'.$id); ?>
<div id="form">
	<div class="grid_16">
		<p class="top_info"><?php printf(lang('lang_move_cat'), $total); ?></p>
		<p class="row1">
			<?php echo form_label(lang('lang_move_to'). ':', 'cat_parent'); ?>
			<?php 
			$cat_options['0'] = lang('lang_no_parent');
			foreach($tree as $row)
			{
				$indent = ($row['cat_parent'] != 0) ? repeater('&nbsp;&raquo;&nbsp;', $row['depth']) : '';
				$cat_options[$row['cat_id']] = $indent.$row['cat_name']; 
			}	
			echo form_dropdown('new_cat', $cat_options, 'id="cat_parent"');
			?>
		</p>
		<p><input type="submit" name="submit" class="save" value="<?php echo lang('lang_save'); ?>" /></p>

		<input type="hidden" name="cat_id" value="<?php echo $id; ?>" />
	</div>
</div>
