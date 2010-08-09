
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<th colspan="3">Welcome&nbsp;<?php echo $user['user_username']; ?></th>
	</tr>
	<tr>
		<td align="center">
			<div class="icon">
				<a href="<?php echo site_url('users/account_modify') ?>">
					<div class="iconimage">
						<img src="themes/default/images/icon_users.png" width="50" height="50" border="0" />
					</div>
					<?php echo lang('lang_modify_my_account'); ?>
				</a>
			</div>
		</td>
		<td align="center">
			<div class="icon">
				<a href="<?php echo site_url('users/listings') ?>">
					<div class="iconimage">
						<img src="themes/default/images/icon_edit.png" width="48" height="48" border="0" />
					</div>
					<?php echo lang('lang_my_listings'); ?></a>
			</div>
		</td>
		<td align="center">
			<div class="icon">
				<a href="<?php echo site_url('users/orders'); ?>">
					<div class="iconimage">
						<img src="themes/default/images/icon_add.png" width="48" height="48" border="0" />
					</div>
					<?php echo lang('lang_order_history'); ?></a>
			</div>
		</td>
	</tr>
</table>