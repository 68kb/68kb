<div id="profile" class="<?php echo url_title($user['group_name']) ?>">

	<span class="small_frame right">
		<img src="{ice:users:gravatar email="<?php echo $user['user_email']; ?>"}" width="60" height="60" alt="<?php echo $user['user_username']; ?>" class="avatar" />
	</span>
	
	<h1><?php echo $user['user_username']; ?></h1>
	
	<table width="100%">
		<tr>
			<td class="formleft">User Group</td>
			<td class="formright"><?php echo $user['group_name'] ?></td>
		<tr>
			<td class="formleft">Member Since</td>
			<td class="formright"><?php echo date('M d, Y', $user['user_join_date']); ?> (<?php echo time_since($user['user_join_date']); ?>)</td>
		</tr>
		<tr>
			<td class="formleft">Last Visit</td>
			<td class="formright"><?php echo date('M d, Y', $user['user_last_login']); ?> (<?php echo time_since($user['user_last_login']); ?>)</td>
		</tr>
		<?php foreach ($extra as $field) { ?>
			<tr>
				<td class="formleft"><?php echo $field['name'] ?></td>
				<td class="formright"><?php echo $field['value']; ?></td>
			</tr>
		<?php } ?>
		
	</table>

</div>
