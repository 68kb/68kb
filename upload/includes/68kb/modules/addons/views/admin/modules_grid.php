<div class="grid_16">
	<div id="content">
		
		<?php if (isset($msg) && $msg != ''): ?>
			<div class="notice"><p><?php echo $msg; ?></p></div>
		<?php endif; ?>
		
		<h2><?php echo lang('lang_modules'); ?></h2>
	
		<?php
		$this->table->set_template($this->table_template);
		$this->table->set_heading(
			lang('lang_title'),
			lang('lang_description'),
			lang('lang_active'),
			lang('lang_version'),
			lang('lang_actions')
		);
		
		if ($inactive) {
			foreach ($inactive as $row)
			{
				$title = $row['module_display_name'];
				$help = ($row['help_file']) ? anchor('admin/addons/docs/'.$row['module_name'], lang('lang_help')) : '';
				$activate = anchor('admin/addons/activate/'.$row['module_name'], lang('lang_activate')) .' | ';
				$delete = anchor('admin/addons/delete/'.$row['module_name'], lang('lang_delete'));
			
				$this->table->add_row(
				 	$title,
				 	$row['module_description'] .' '. $help,
					'<span class="not_completed">Not Active</span>',
				 	$row['module_version'],
				 	$activate . $delete
				 );
			}
		}
		
		if ($active)
		{
			foreach ($active as $row)
			{
				$title = ($row['module_admin']) ? anchor('admin/'.$row['module_name'], $row['module_display_name']) : $row['module_display_name'];
				$help = ($row['help_file']) ? anchor('admin/addons/docs/'.$row['module_name'], lang('lang_help')) : '';
				$upgrade = ($row['server_version'] != $row['module_version']) ? anchor('admin/addons/upgrade/'.$row['module_name'], lang('lang_upgrade')) .' | ' : '';
				$deactivate = anchor('admin/addons/deactivate/'.$row['module_name'], lang('lang_deactivate'));
				
				$this->table->add_row(
				 	$title,
				 	$row['module_description'] .' '. $help,
					'<span class="active">Active</span>',
				 	$row['module_version'],
				 	$upgrade . $deactivate
				 );
			}
		}
		
		echo $this->table->generate();
		$this->table->clear();
		?>
	</div>
</div>

<script type="text/javascript" charset="utf-8"> 
$(document).ready(function() {
	oTable = $('.main').dataTable( {

		"aoColumns": [null,null,null,null,null],
		"sPaginationType": "full_numbers",
		"bStateSave": true,
		"bJQueryUI": true
	} );
});
</script>