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

<?php 
if($notes)
{
	foreach ($notes AS $note)
	{
		if ($note['note_important'] == 'y')
		{
			?>
<div class="grid_16">
	<div class="yellownote">
		<table width="100%" cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td rowspan="2" width="20" valign="top" class="notenav">
					<a href='<?php echo site_url('admin/users/edit_note/'.$note['note_id']);?>' class="edit_note"><img src="<?php echo $template; ?>images/icons/small/edit.png" border="0" alt="Edit Note" /></a><br />
					<a href="javascript:void(0);" onclick="deleteSomething('<?php echo site_url('admin/users/delete_note/'.$note['note_id']); ?>')"><img src="<?php echo $template; ?>images/icons/small/delete.png" border="0" alt="<?php echo lang('lang_delete'); ?>" title="<?php echo lang('lang_delete'); ?>" /></a>					
				</td>
				<td class="notetitle">
					Added By: <a href="<?php echo site_url('users/edit/'.$note['user_id']); ?>"><?php echo $note['user_username'] ?></a> on <?php echo date('Y-m-d H:m:s', $note['note_date']); ?>
				</td>
			<tr>
				<td colspan="3" class="notefield"><?php echo nl2br($note['note']); ?></td>
			</tr>
		</table> 
	</div>
</div>
			<?php
			}
		}
	}
	?>

<?php if(isset($error)) {
	echo '<div class="error">'.$error,'</div>';
} ?>
<?php if(validation_errors()) {
	echo '<div class="error"><h3>'.lang('lang_errors_occured').'</h3> '.validation_errors().'</div>';
}
?>

	<?php echo form_open('admin/users/edit/'.$row['user_id']); ?>
	<div class="grid_9" id="form">
		<h2 class="underline"><?php echo lang('lang_edit_user'); ?> <a class="addnew iframe" href="<?php echo site_url('admin/users/add_note/'.$row['user_id']);?>"><?php echo lang('lang_add_note'); ?></a></h2>
		<div class="inline">
			
			<p class="top_info"><?php echo lang('lang_required_info'); ?></p>
			<p class="row1">
				<label for="user_username"><?php echo lang('lang_username'); ?>: <em>(<?php echo lang('lang_required'); ?>)</em></label>
				<input tabindex="1" type="text" class="inline_input" name="user_username" id="user_username" value="<?php echo set_value('user_username', $row['user_username']); ?>" />
			</p>
			<p class="row1">
				<label for="user_email"><?php echo lang('lang_email'); ?>: <em>(<?php echo lang('lang_required'); ?>)</em></label>
				<input tabindex="2" type="text" class="inline_input" name="user_email" id="user_email" value="<?php echo set_value('user_email', $row['user_email']); ?>" />
			</p>
			<p class="row1">
				<label for="user_group"><?php echo lang('lang_user_group'); ?>: <em>(<?php echo lang('lang_required'); ?>)</em></label>
					<select tabindex="3" name="user_group" id="user_group">
					<option value=""><?php echo lang('lang_please_select'); ?></option>
					<?php foreach ($groups AS $group) { ?>
						<option value="<?php echo $group['group_id']; ?>"<?php if($group['group_id'] == $row['user_group']) echo 'selected="selected"'; ?>><?php echo $group['group_name']; ?></option>
					<?php } ?>
				</select>
				<?php if ($row['user_group'] == 4): ?>
					<?php echo anchor('admin/users/delete_user_items/'.$row['user_id'], lang('lang_delete_all_content')); ?></a>
				<?php endif; ?>
			</p>
			
			<p class="top_info"><?php echo lang('lang_change_password'); ?></p>
			
			<p class="row1">
				<?php echo form_label(lang('lang_password'). ':', 'user_password'); ?>
				<?php echo form_password('user_password', set_value('user_password'), 'class="inline_input"'); ?>
			</p>
			
			<p class="row1">
				<?php echo form_label(lang('lang_password_confirm'). ':', 'user_password_confirm'); ?>
				<?php echo form_password('user_password_confirm', set_value('user_password_confirm'), 'class="inline_input"'); ?>
			</p>
			
			<?php if ($fields) { ?>
				<p class="top_info"><?php echo lang('lang_extra_fields'); ?></p>
				<?php echo get_admin_fields($fields, $row); ?>
			<?php } ?>
			
			<p class="submit"><input type="submit" name="submit" class="save" value="<?php echo lang('lang_save'); ?>" /></p>
			
			<input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>" />
		</div>
		
	</div>
	<div class="grid_7">
		<fieldset>
			<legend>Original Version</legend>
			<div class="user_box">
			<img width="48" height="48" src="<?php echo gravatar($row['user_email'], 'PG', 48); ?>" class="user_gravatar" />
			<h3><?php echo $row['user_username']; ?></h3>
			<a href="mailto:<?php echo $row['user_email']; ?>"><?php echo $row['user_email']; ?></a><br />
			<span class="date"><?php echo lang('lang_join_date'); ?>: <?php echo format_date($row['user_join_date']); ?></span>
			<span class="date"><?php echo lang('lang_last_login'); ?>: <?php echo format_date($row['user_last_login']); ?></span><br />
			</div>
		</fieldset>
		<fieldset>
			<legend><?php echo lang('lang_stats'); ?></legend>
			<ul id="stats">
				<li>Total Spent: <?php echo format_money($total_order_amount); ?></li>
				<li>Total Orders: <?php echo $total_orders; ?></li>
				<li>Listings Placed: <?php echo $row['user_listings']; ?></li>
				<li>Active Listings: <?php echo $active_listings; ?></li>
			</ul>
		</fieldset>
		<fieldset>
			<legend><?php echo lang('lang_xml_rpc_api'); ?></legend>
			<p class="row1"><?php echo lang('lang_api_key'); ?>: <span class="api_key"><tt><?php echo $row['user_api_key']; ?></tt></span> &nbsp; 
			<a href="<?php echo site_url('admin/users/reset_api/'.$row['user_id']); ?>"><?php echo lang('lang_regenerate'); ?></a></p>
		</fieldset>
	</div>
	


	<?php 
	if($notes)
	{
		foreach ($notes AS $note)
		{
			if ($note['note_important'] != 'y')
			{
				?>
	<div class="grid_16">
		<fieldset><legend><?php echo lang('lang_notes'); ?></legend>
		<div class="notebox">
			<table width="100%" cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td rowspan="2" width="20" valign="top" class="notenav">
						<a href='<?php echo site_url('admin/users/edit_note/'.$note['note_id']);?>' class="edit_note"><img src="<?php echo $template; ?>images/icons/small/edit.png" border="0" alt="Edit Note" /></a><br />
						<a href="javascript:void(0);" onclick="deleteSomething('<?php echo site_url('admin/users/delete_note/'.$note['note_id']); ?>')"><img src="<?php echo $template; ?>images/icons/small/delete.png" border="0" alt="<?php echo lang('lang_delete'); ?>" title="<?php echo lang('lang_delete'); ?>" /></a>					
					</td>
					<td class="notetitle">
						Added By: <a href="<?php echo site_url('users/edit/'.$note['user_id']); ?>"><?php echo $note['user_username'] ?></a> on <?php echo date('Y-m-d H:m:s', $note['note_date']); ?>
					</td>
				<tr>
					<td colspan="3" class="notefield"><?php echo nl2br($note['note']); ?></td>
				</tr>
			</table> 
		</div>
		</fieldset>
	</div>
				<?php
				}
			}
		}
	?>
	
	
<script type="text/javascript" charset="utf-8"> 
$(document).ready(function() {
	$(".addnew").fancybox({
		'titleShow'	: false,
		'type'		: 'iframe',
		'onClosed' 	: function() {
			location.reload();
		}
	});
	$(".edit_note").fancybox({
		'titleShow'	: false,
		'type'		: 'iframe',
		'onClosed' 	: function() {
			location.reload();
		}
	});
});
</script>