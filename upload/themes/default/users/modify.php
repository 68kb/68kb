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

<?php if(isset($error)) {
	echo '<div class="error">'.$error,'</div>';
} ?>
<?php if(validation_errors()) {
	echo '<div class="error"><h3>'.lang('lang_errors_occured').'</h3> '.validation_errors().'</div>';
}
?>

<?php echo form_open('users/account_modify/'); ?>
	<fieldset>
		<legend><?php echo lang('lang_modify_my_account'); ?></legend>
		<table class="register" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td class="formleft"><?php echo form_label(lang('lang_email'). ': <em>('.lang('lang_required').')</em>', 'user_email'); ?></td>
				<td class="formright"><?php echo form_input('user_email', set_value('user_email', $user['user_email']), 'class="inline_input"'); ?></td>
			</tr>
			<tr>
				<td class="formleft"><?php echo form_label(lang('lang_password'). ':', 'user_password'); ?></td>
				<td class="formright"><?php echo form_password('user_password', set_value('user_password'), 'class="inline_input"'); ?></td>
			</tr>
			<tr>
				<td class="formleft"><?php echo form_label(lang('lang_password_confirm'). ':', 'user_password_confirm'); ?></td>
				<td class="formright"><?php echo form_password('user_password_confirm', set_value('user_password_confirm'), 'class="inline_input"'); ?></td>
			</tr>
			
			<?php if ($fields) { ?>
				<?php echo get_fields($fields, $user); ?>
			<?php } ?>
			
		</table>
	</fieldset>
		
	<?php echo form_submit('submit', lang('lang_save'), 'class="save"'); ?>
</form>