<script type="text/javascript" charset="utf-8"> 
$(document).ready(function() {
	$('input.date').datepicker({
			dateFormat: 'yy-mm-dd',
			yearRange: '1900:2020',
			changeMonth: true,
			changeYear: true
	});
});
</script>
<div class="grid_16">
	<h2><?php echo lang('lang_add_user'); ?></h2>
</div>

<?php if(isset($error)) {
	echo '<div class="error">'.$error,'</div>';
} ?>
<?php if(validation_errors()) {
	echo '<div class="error"><h3>'.lang('lang_errors_occured').'</h3> '.validation_errors().'</div>';
}
?>

	<?php echo form_open('admin/users/add/'); ?>
	<div id="form">
	<div class="grid_9">
		<div class="inline">
			
			<p class="top_info"><?php echo lang('lang_required_info'); ?></p>
			<p class="row1">
				<?php echo form_label(lang('lang_username'). ': <em>('.lang('lang_required').')</em>', 'user_username'); ?>
				<?php echo form_input('user_username', set_value('user_username'), 'class="inline_input"'); ?>
			</p>
			<p class="row1">
				<?php echo form_label(lang('lang_email'). ': <em>('.lang('lang_required').')</em>', 'user_email'); ?>
				<?php echo form_input('user_email', set_value('user_email'), 'class="inline_input"'); ?>
			</p>
			<p class="row1">
				<?php echo form_label(lang('lang_user_group'). ': <em>('.lang('lang_required').')</em>', 'user_group'); ?>
					<select name="user_group" id="user_group">
					<option value=""><?php echo lang('lang_please_select'); ?></option>
					<?php foreach ($groups AS $group) { ?>
						<option value="<?php echo $group['group_id']; ?>"><?php echo $group['group_name']; ?></option>
					<?php } ?>
				</select>
			</p>
			
			<p class="row1">
				<?php echo form_label(lang('lang_password'). ': <em>('.lang('lang_required').')</em>', 'user_password'); ?>
				<?php echo form_password('user_password', set_value('user_password'), 'class="inline_input"'); ?>
			</p>
			
			<p class="row1">
				<?php echo form_label(lang('lang_password_confirm'). ':', 'user_password_confirm'); ?>
				<?php echo form_password('user_password_confirm', set_value('user_password_confirm'), 'class="inline_input"'); ?>
			</p>
			
			<p class="row2">
				<?php echo form_submit('submit', lang('lang_save'), 'class="save"'); ?>
			</p>
			
		</div>
		
	</div>
	<div class="grid_7">
		<div class="inline">
			<?php if ($fields) { ?>
				<?php echo get_admin_fields($fields); ?>
			<?php } ?>
	</div>
	</div>