<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">

<head profile="http://gmpg.org/xfn/11">
	<title><?php echo lang('lang_notes'); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<script type="text/javascript" src="<?php echo base_url();?>js/jquery.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>js/tooltip.js"></script>
	<link  href="<?php echo $template;?>css/style.css" rel="stylesheet" type="text/css" />
	<?php echo $head_data; ?>  
	<?php $this->events->trigger('admin/header');?>
	<base href="<?php echo base_url(); ?>" />
</head>
<body style="background-image: none;">
	
	<div id="facebox" style="width: 90%;">
		
		<?php if (isset($success)) { ?>
			
			<h2><?php echo $success; ?></h2>
		
		<?php } else { ?>
		
			<?php if(isset($error)) {
				echo '<div class="error">'.$error,'</div>';
			} ?>
			<?php if(validation_errors()) {
				echo '<div class="error"><h3>'.lang('lang_errors_occured').'</h3> '.validation_errors().'</div>';
			}
			?>
	
			<?php if ($action == 'add') { ?>
				<h2><?php echo lang('lang_add_note'); ?></h2>
				<?php echo form_open('admin/users/add_note/'.$user_id, 'id="add_note"'); ?>
			<?php } else { ?>
				<h2><?php echo lang('lang_edit_note'); ?></h2>
				<?php echo form_open('admin/users/edit_note/'.$note_id, 'id="add_note"'); ?>
			<?php } ?>
				
			<?php echo '<div class="row1">'.form_textarea($textarea).'</div>'; ?>
		
			<div class="row2">
				<?php echo form_label(lang('lang_note_important'). ':', 'note_important'); ?>
				<?php $options = array('y' => lang('lang_yes'), 'n' => lang('lang_no')); ?>
				<?php echo form_dropdown('note_important', $options, set_value('note_important'), 'id="note_important"'); ?>
			</div>
			<p class="submit"><?php echo form_submit('mysubmit', lang('lang_save')); ?></p>
		
			<?php echo form_close(); ?>
		
		<?php } ?>
	</div>
</body>
</html>