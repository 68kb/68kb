<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">

<head profile="http://gmpg.org/xfn/11">
	<title><?php echo lang('lang_image_manager'); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<script type="text/javascript" src="<?php echo base_url();?>js/jquery.js"></script>
	<base href="<?php echo base_url(); ?>" />
	<link  href="<?php echo $template;?>css/image_manager.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript">
	function send_text(the_value, the_width, the_height) { var image = '<img src="'+the_value+'" width="'+the_width+'" height="'+the_height+'" />'; parent.edInsertContent('<?php echo $field; ?>', image); parent.$.fancybox.close(); }
	</script>
</head>

<body class="image_manager">
	<div id="facebox" style="">
		<h2><?php echo lang('lang_image_manager'); ?></h2>
		<div id="tabs">
			<ul>
				<li><a href="<?php echo site_url('admin/image_manager/index/'.$field) ?>" <?php if ($nav == 'upload') echo 'class="current"'; ?>><?php echo lang('lang_upload_image'); ?></a></li>
				<li><a href="<?php echo site_url('admin/image_manager/browse/'.$field) ?>" <?php if ($nav == 'browse') echo 'class="current"'; ?>><?php echo lang('lang_browse_library'); ?></a></li>
			</ul>
		</div>
		<div id="image_manager" class="clear">
			<?php echo $body; ?>
		</div>
	</div>
</body>
</html>