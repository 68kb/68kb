<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		
		<!-- Stylesheets -->
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/reset.css" type="text/css" />
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/style.css" type="text/css" />
		<script type="text/javascript">
            var base_url = '<?php echo base_url(); ?>';
		</script>
		<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.js"></script>
		<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/installer.js"></script>

		<title>PyroCMS Installer</title>
	</head>
	<body>
		<!-- Main wrapper -->
		<div id="wrapper">
			<div id="logo">
				<img src="<?php echo base_url(); ?>assets/images/logo.png" alt="PyroCMS" width="200" height="76" />
			</div>
			<!-- The header -->
			<div id="header">
				<ul>
					<li><?php echo anchor('', lang('intro'), $this->uri->segment(2, '') == '' ? 'id="current"' : ''); ?></li>
					<li><?php echo anchor('installer/step_1', lang('step1'), $this->uri->segment(2, '') == 'step_1' ? 'id="current"' : ''); ?></li>
					<li><span id="<?php echo $this->uri->segment(2, '') == 'step_2' ? 'current' : ''?>"><?php echo lang('step2'); ?></span></li>
					<li><span id="<?php echo $this->uri->segment(2, '') == 'step_3' ? 'current' : ''?>"><?php echo lang('step3'); ?></span></li>
					<li><span id="<?php echo $this->uri->segment(2, '') == 'step_4' ? 'current' : ''?>"><?php echo lang('step4'); ?></span></li>
					<li><span id="<?php echo $this->uri->segment(2, '') == 'complete' ? 'current' : ''?>"><?php echo lang('final'); ?></span></li>
				</ul>
			</div>
			<!-- The content -->
			<div id="content">
				<?php if($this->session->flashdata('message')): ?>
				<div id="notification" class="<?php if($this->session->flashdata('message_type')){echo $this->session->flashdata('message_type');} ?>">
					<p><?php echo $this->session->flashdata('message'); ?></p>
				</div>
				<?php endif; ?>
				<?php echo $page_output; echo "\n"; ?>
			</div>
		</div>
	</body>
</html>