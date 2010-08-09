<h2><?php echo lang('lang_forgot_pass'); ?></h2>

<?php if (isset($message)): ?>
	
	<p><?php echo $message; ?></p>
	
<?php else: ?>
	
	<?php if (validation_errors()) echo '<div class="errors">'.validation_errors().'</div>'; ?>
	
	<?php echo form_open('users/forgot'); ?>
		<table class="register" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td class="formleft"><?php echo lang('lang_email'); ?>:</td>
				<td class="formright"><input name="email" type="text" id="email" value="" /></td>
			</tr>
			<tr>
				<td colspan="2" valign="top">
					<div align="center">
						<input name="submit" type="submit" id="submit" value="<?php echo lang('lang_submit'); ?>" />
					</div>
				</td>
			</tr>
		</table>
	</form>
	
<?php endif; ?>