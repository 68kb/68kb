<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">

<head profile="http://gmpg.org/xfn/11">
	<title><?php echo $site_title; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<script type="text/javascript" src="<?php echo base_url();?>js/jquery.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>js/tooltip.js"></script>
	<link  href="<?php echo $template;?>css/style.css" rel="stylesheet" type="text/css" />
	<?php echo $head_data; ?>  
	<?php $this->events->trigger('admin/header');?>
	<base href="<?php echo base_url(); ?>" />
</head>

<body class="custom">
	<?php if ( ! defined('NO_VERSION_CHECK') && $settings['script_version'] != $settings['script_latest']) { ?>
		<div class="head_notice"><?php echo sprintf(lang('lang_out_of_date'),$settings['script_latest']); ?></div>
		<!-- <div class="information">Test information</div> -->
	<?php } //endif; ?>
	<div id="wrapper">
		<div class="container_16">
			<div id="header">
					<div id="logo" class="grid_12">68kb</div>
					<div id="logo_info" class="grid_4 right">
						<div class="user_info">
							<div>
								<img width="24" height="24" src="<?php echo gravatar($this->users_auth->get_data('user_email'), 'PG', 24); ?>" alt="gravatar" class="gravatar" />
								Welcome <?php echo $this->users_auth->get_data('user_username'); ?> <br />
								<a href="<?php echo site_url('admin/users/edit/'.$this->session->userdata('user_id')); ?>">Edit Account</a> | <a href="<?php echo site_url('admin/logout'); ?>">Logout</a> | <a href="<?php echo site_url() ?>" target="_blank">View Site</a>
							</div>
						</div>
					</div>
			</div>
			<div class="clear"></div>
			<div id="topnav" class="grid_16 clearfix">
				<div id="tabs">
					<?php $this->load->view('admin/inc/nav'); ?>
				</div>
			</div>
			<div class="clear"></div>
			
			<div class="grid_16 border">
				
				<div id="subnav">
					<?php $this->load->view('admin/inc/sub_nav'); ?>
				</div>
				
				<?php if($this->session->flashdata('msg')) {
					echo '<div id="msg" class="notice"><p>'. $this->session->flashdata('msg') .'</p></div>';
				} ?>
				
				<?php if($this->session->flashdata('info')) {
					echo '<div id="msg" class="information"><p>'. $this->session->flashdata('info') .'</p></div>';
				} ?>
				
				<?php if($this->session->flashdata('error')) {
					echo '<div id="msg" class="error"><p>'. $this->session->flashdata('error') .'</p></div>';
				} ?>
				
				
					<?php echo $body; ?>
				
			</div>
			
			<div class="grid_16">
				<div id="footer">
					&copy; <?php echo date("Y"); ?> 68kb - v<?php echo $settings['script_version']; ?> - Build <?php echo $settings['script_build']; ?><br />
					Time: <?php echo $this->benchmark->elapsed_time();?> - Memory: <?php echo $this->benchmark->memory_usage();?>
				</div>
			</div>
		</div>
	</div>
<?php $this->events->trigger('admin/footer');?>

</body>
</html>