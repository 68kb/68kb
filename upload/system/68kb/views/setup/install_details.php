<?php echo form_open('setup/install_final'); ?>
			
			<table width="90%" align="center" cellpadding="5" cellspacing="0" class="modules">
				<tr>
					<th colspan="2">Site Settings:</th>
				</tr>
				<?php if(validation_errors()) {
					echo '<tr><td colspan="2"><div class="fail">'.validation_errors().'</div></td></tr>';
				}
				?>
				<tr>
					<td width="50%" class="row1">Site Address</td>
					<td width="50%" class="row1">
						<?php echo form_input('url', set_value('url', $guess_url)); ?>
					</td>
				</tr>
				<tr>
					<td width="50%" class="row1">Admin Username</td>
					<td width="50%" class="row1">
						<?php echo form_input('username', set_value('username')); ?>
					</td>
				</tr>
				<tr>
					<td class="row2">Admin Password</td>
					<td class="row2">
						<?php echo form_input('password', set_value('password')); ?>
					</td>
				</tr>
				<tr>
					<td width="50%" class="row1">Admin Email Address</td>
					<td width="50%" class="row1">
						<?php echo form_input('email', set_value('email')); ?>
					</td>
				</tr>
				<tr>
					<td width="50%" class="row1">Site Title</td>
					<td width="50%" class="row1">
						<?php echo form_input('site_name', set_value('site_name')); ?>
					</td>
				</tr>
				<tr>
					<td width="50%" class="row2">Install Sample Data:</td>
					<td width="50%" class="row2">
						<select name="sample">
							<option value="">No</option>
							<option value="categories">Example</option>
						</select>
					</td>
				</tr>
			</table>
			
			<p align="right">
				<?php
					if (isset($error) && $error==TRUE) {
						echo "<p style='text-align: center;'><strong>Please fix the above errors and refresh this page.</strong></p>";
					} else {
				?>
				<input type="submit" name="submit" class="button" value="Next Step" />
				<?php } ?>
			</p>
		</form>
</div>