<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>68KB Setup</title>
<link href="<?php echo base_url();?>themes/cp/css/setup.css" rel="stylesheet" type="text/css" />
</head>

<body> 
	<div id="wrapper">
		<div id="content">

			<h1>68KB Setup</h1>
			
			<!-- // Content // -->
			<?php if ( ! empty($errors)) { ?>
				<?php foreach ($errors AS $msg) {  ?>
					<?php if ($msg <> '') { ?>
						<div class="error"><p>ERROR: <?php echo $msg; ?></p></div>
					<?php } ?>
				<?php } ?>
				
				<p>Please fix the above errors before you can continue.
				
			<?php } else { ?>
			
				<?php echo $body; ?>	  
			
			<?php } ?>
			<!-- // End Content // -->
		
		</div>
		
		<div id="footer">
			&copy; <?php echo date("Y"); ?> 68KB - <?php echo $version; ?> - 
			Time: <?php echo $this->benchmark->elapsed_time();?> - Memory: <?php echo $this->benchmark->memory_usage();?>
		</div>
</div>



</body>
</html>