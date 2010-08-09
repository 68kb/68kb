<h2>Please Register or Login</h2>

<?php 
if (validation_errors()) echo '<div class="errors">'.validation_errors().'</div>';
if($this->session->flashdata('msg')) {
	echo '<div class="error"><h3>'.lang('lang_errors_occured').'</h3><p>'. $this->session->flashdata('msg') .'</p></div>';
} elseif($this->session->flashdata('error')) {
	echo '<div class="error"><h3>'.lang('lang_errors_occured').'</h3><p>'. $this->session->flashdata('error') .'</p></div>';
} elseif (isset($error)) { 
	echo '<div class="error"><h3>'.lang('lang_errors_occured').'</h3><p>'. $error .'</p></div>';
} ?>

<table width="100%">
	<tr>
		<td width="50%" valign="top">
			<?php echo form_open('users/login'); ?>
				<fieldset>
					<legend>Existing User</legend>
						<table width="100%">
          					<tr>
            					<td class="formleft"><?php echo lang('lang_username'); ?>:</td>
            					<td class="formright"><input name="username" type="text" id="username" value="" /></td>
          					</tr>
          					<tr>
            					<td class="formleft"><?php echo lang('lang_password'); ?>:</td>
            					<td class="formright"><input name="password" type="password" id="password" value="" /></td>
          					</tr>
							<tr>
								<td colspan="2">
									<input type="checkbox" name="remember" value="y" />&nbsp;<?php echo lang('lang_remember_me'); ?>
								</td>
							</tr>
          					<tr>
      	  						<td colspan="2" valign="top">
									<div align="center">
	          							<input name="action" type="hidden" id="action" value="login" />
	          							<input name="submit" type="submit" id="submit" value="<?php echo lang('lang_submit'); ?>" />
        							</div>
        						</td>
	     					</tr>
	   					</table>
				</fieldset>
			</form>
		</td>
		<td width="50%" valign="top">

			<fieldset>
				<legend>New User</legend>
        			<table width="100%">
          				<tr>
            				<td><strong><a href="<?php echo site_url('users/register'); ?>" title="Create an account.">Create an account.</a></strong></td>
	     				</tr>
	      				<tr>
            				<td><a href="<?php echo site_url('users/forgot'); ?>" title="Forgot your password?">Forgot your password?</a></td>
	     				</tr>
	   				</table>
			</fieldset>
		</td>
	</tr>
</table>
