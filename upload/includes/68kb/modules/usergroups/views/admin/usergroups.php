<div class="grid_16">
	<div id="content">
		<h2><?php echo lang('lang_user_groups'); ?> <a class="addnew" href="<?php echo site_url('admin/usergroups/add');?>"><?php echo lang('lang_add_user_group'); ?></a></h2>

		<?php if (isset($options) && $options): ?>
		<?php
			$this->table->set_template($this->table_template);
			$this->table->set_heading(
				lang('lang_id'),
				lang('lang_title'),
				lang('lang_description'),
				lang('lang_members'),
				lang('lang_actions')
			);
				foreach($options as $row)
				{
					$edit_url = 'admin/usergroups/edit/'.$row['group_id'];
					$delete_url = ($row['group_id'] > 5) ? anchor('admin/usergroups/delete/'.$row['group_id'], lang('lang_delete'), array('class' => 'delete')) : '';
					$this->table->add_row(
						$row['group_id'],
					 	anchor($edit_url, $row['group_name']),
					 	$row['group_description'],
					 	$row['group_members'],
						anchor($edit_url, lang('lang_edit'), array('class' => 'edit')) .'&nbsp;'. 
						$delete_url
					 );
				}
			echo $this->table->generate();
		?>
		<?php else: ?>
			<p><?php echo lang('no_results'); ?></p>
		<?php endif; ?>
	
	</div>
</div>