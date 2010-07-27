<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo lang('lang_please_login'); ?></title>
<style type="text/css">

body {
	background-color: #EEEEEE;
	font: 12px/1.5 "Lucida Grande",Helvetica,Arial,sans-serif;
	text-align: center;
	color: #111;
	margin: 0;
	padding: 0;
}

#content  {
	text-align: left;
	padding: 3px;
	margin:	20px auto 0 auto;
	width: 440px;
	border: 1px solid #999;
	background: #CACACA;
}

h2 {
	color:#111111;
	font-family:"Palatino Linotype",Palatino,Georgia,"Times New Roman",Times,serif;
	font-size:2.5em;
	font-weight:400;
	line-height:1.267em;
	margin: 0 0 10px 0;
	padding:10px 5px 6px 20px;
	text-shadow:2px 2px #ccc;
	background:#EEEEEE;
	border-bottom: 1px solid #ccc;
}
h2.login {
	background:#EEEEEE url(<?php echo base_url(); ?>themes/cp/images/icons/medium/lock.png) no-repeat 8px 10px;
	color: #111111;
	padding: 10px 12px 10px 46px;
	clear:both;
}
.wrap {
	background:#EEEEEE none repeat scroll 0 0;
	border:1px solid #aaa;
	clear:both;
	padding:0.5em;
}
a:link, a:visited {
	color: #111;
	text-decoration: none;
}

a:hover {
	color: #111;
	text-decoration: underline;
}
label {
	font-weight: bold;
	text-align: right;
}
#footer {
	font-size: 10px;
}
.error {
	font-size: 14px;
	color:	#ce0000;
	background:	#fdf5b2;
	border:	1px solid #f3d589;
	width: 374px;
	padding: 5px;
}
.fail {
	background: #fff3a3 url(<?php echo base_url(); ?>themes/cp/images/icons/small/warning.png) no-repeat 10px center;
	padding-left: 35px;
	color: #9D200A;
	border:1px solid #e7bd72;
	margin: 5px 0;
}
.fail p {
	margin: 0;
	padding: 3px 0;
}
td {
	padding: 3px;
}
#submit {
	padding: 4px;
	margin: 5px;
}
</style>

</head>

<body id="login" onload="document.forms[0].username.focus();">
	<div id="content">
		<?php echo form_open('admin/login'); ?>
			<div class="wrap">
				<h2 class="login"><?php echo lang('lang_please_login');?></h2>
				
					<?php if (validation_errors()) {
							echo '<div class="fail">'. validation_errors() .'</div>'; 
						}
					?>
					
					<?php if(isset($error)) {
						echo '<div id="msg" class="fail"><p>'. $error .'</p></div>';
					} ?>

						<table width="100%">
							<tr>
								<td><label for="username"><?php echo lang('lang_username');?>:</label></td>
								<td><input name="username" type="text" id="username" value="<?php echo set_value('username'); ?>" /></td>
							</tr>
							<tr>
								<td><label for="password"><?php echo lang('lang_password');?>:</label></td>
								<td><input name="password" type="password" id="password" /></td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td><input type="checkbox" name="remember" id="remember" value="y" <?php echo set_checkbox('remember', 'y'); ?> />&nbsp;<label for="remember"><?php echo lang('lang_remember_me'); ?></label></td>
							</tr>
							<tr>
								<td colspan="2" valign="top">
									<div align="center">
										<input name="submit" type="submit" id="submit" value="Login" /> 
										&nbsp; <a href="<?php echo site_url('users/forgot'); ?>"><?php echo lang('lang_forgot_pass'); ?></a>
									</div>
								</td>
							</tr>
						</table>
				</div>
		</form>
	</div>
	

<div id="footer">
	&copy; <?php echo date("Y"); ?> 68kb - Time: <?php echo $this->benchmark->elapsed_time();?> - Memory: <?php echo $this->benchmark->memory_usage();?>
</div>
</body>
</html>