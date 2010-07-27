<script type="text/javascript">
jQuery(document).ready(function($) {
	$('a[rel*=optimize]').fancybox();
	$('a[rel*=repair]').fancybox();
	$('a[rel*=delete_cache]').fancybox();
})
</script>

<div class="grid_16">
	<div id="content">
		<h2><?php echo lang('lang_settings'); ?></h2>
		
		<table class="main" width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<th width="50%"><?php echo lang('lang_main_settings'); ?></th>
				<th><?php echo lang('lang_utilities'); ?></th>
			</tr>
			<tr>
				<td>
					<?php if ($this->users_auth->check_role('can_manage_settings')) { ?>
					<ul>
						<li><?php echo anchor('admin/settings/general/', lang('lang_general_settings')) ?></li>
						<li><?php echo anchor('admin/gateways', lang('lang_gateways')); ?></li>
						<li><a href="<?php echo site_url('admin/settings/listings/') ?>"><?php echo lang('lang_checkout_settings'); ?></a></li>
						<li><a href="<?php echo site_url('admin/settings/email/'); ?>"><?php echo lang('lang_email_settings'); ?></a></li>
						<li><a href="<?php echo site_url('admin/listings/status_list/'); ?>"><?php echo lang('lang_listing_statuses'); ?></a></li>
						<li><a href="<?php echo site_url('admin/orders/status_list'); ?>"><?php echo lang('lang_order_statuses'); ?></a></li>
						<?php $this->events->trigger('admin/settings_main/left'); ?>
					</ul>
					<?php } else { ?>
						<?php echo lang('not_authorized'); ?>
					<?php } ?>
				</td>
				<td>
					<?php if ($this->users_auth->check_role('can_manage_utilities')) { ?>
					<ul>
						<li><a href="<?php echo site_url('admin/settings/optimize'); ?>" rel="optimize"><?php echo lang('lang_optimize'); ?></a></li>
						<li><a href="<?php echo site_url('admin/settings/repair'); ?>" rel="repair"><?php echo lang('lang_repair_db'); ?></a></li>
						<li><a href="<?php echo site_url('admin/settings/delete_cache'); ?>" rel="delete_cache"><?php echo lang('lang_delete_cache'); ?></a></li>
						<li><a href="<?php echo site_url('admin/settings/backup'); ?>"><?php echo lang('lang_backup_db'); ?></a></li>
						<?php $this->events->trigger('admin/settings_main/right'); ?>
					</ul>
					<?php } ?>
				</td>
				
			</tr>
		</table>
		
	</div>
</div>
