<div class="grid_16">
	<div id="content">
	<?php if($this->session->flashdata('msg')) {
		echo '<div class="notice"><p>'. $this->session->flashdata('msg') .'</p></div>';
	} ?>
	
	<div id="theme">
		<h3><?php echo lang('lang_current_template'); ?></h3>
		<table id="current-theme">
			<tr>
				<td>
					<img class="current" src="<?php echo $active['preview']; ?>" alt="Current theme preview" />
				</td>
				<td>
					<h3><?php echo $active['name']; ?></h3>
					<?php if ($active['admin']): ?>
						<a href="<?php echo site_url('admin/settings/edit_template/'.$active['dir']); ?>"><?php echo lang('lang_edit_template_settings'); ?></a>
					<?php endif; ?>
					<p class="description"><?php echo $active['description']; ?></p>
				</td>

			</tr>
		</table>

		<h3><?php echo lang('lang_available_templates'); ?></h3>
		<br class="clear" />
			<?php if(is_array($available_themes)): foreach($available_themes AS $row): ?>
				<div class="available_box">
					<div class="available_box_heading"><?php echo $row['title']; ?></div>
					<a href="<?php echo site_url('admin/settings/templates/'.$row['file']);?>"><img src="<?php echo $row['preview']; ?>" width="200" height="167" /></a>
					
					<p>
						<a href="<?php echo site_url('admin/settings/templates/'.$row['file']);?>"><?php echo lang('lang_activate'); ?></a>
					<?php if ($row['admin']): ?>
						| <a href="<?php echo site_url('admin/settings/edit_template/'.$row['file']); ?>"><?php echo lang('lang_edit_template_settings'); ?></a>
					<?php endif; ?>
					</p>
				</div>
			<?php endforeach; endif; ?>
		<br class="clear" />
	</div>

</div>
</div>