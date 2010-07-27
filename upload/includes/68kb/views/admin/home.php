<div class="grid_10 alpha">
	<div id="content">
		<h2><?php echo lang('lang_new_orders'); ?> <span><?php echo lang('lang_since_last_login'); ?> </span> <a class="view_all_orders" href="<?php echo site_url('admin/orders'); ?>"><?php echo lang('lang_all_orders'); ?></a></h2>
	<table class="list" width="100%">
		<tr>
			<th><?php echo lang('lang_order_id'); ?></th>
			<th><?php echo lang('lang_status'); ?></th>
			<th><?php echo lang('lang_name'); ?></th>
			<th><?php echo lang('lang_date'); ?></th>
			<th><?php echo lang('lang_total'); ?></th>
			<th style="width: 96px;"><?php echo lang('lang_actions'); ?></th>
		</tr>
		<?php if ($orders) { ?>
			<?php foreach ($orders AS $row) { ?>
				<tr class="<?php echo alternator('first', 'second'); ?>">
					<td><?php echo $row['order_id']; ?></td>
					<td><span class="<?php echo $row['status_class']; ?>"><?php echo $row['status_internal']; ?></span></td>
					<td><?php echo $row['user_username']; ?></td>
					<td><?php echo date($this->config->item('short_date_format'), $row['order_date']) ?></td>
					<td><?php echo format_money($row['order_total']) ?></td>
					<td>
						<a class="modify" href="<?php echo site_url('admin/orders/view/'.$row['order_id']); ?>">View Order</a></li>
					</td>
				</tr>
			<?php } ?>
		<?php } else { ?>
			<tr>
				<td colspan="6">No new orders</td>
			</tr>
		<?php } ?>
	</table>
	
		<?php $this->events->trigger('admin/home');?>
		
	</div>
</div>

<div class="grid_6 omega">
	<table width="100%">
		<tr>
			<th>Site Statistics</th>
			<th>Value</th>
		</tr>
		<tr class="">
			<td>You are running</td>
			<td>v<?php echo $settings['script_version']; ?></td>
		</tr>
		<tr class="">
			<td>License Key</td>
			<td><?php echo $this->config->item('license_key'); ?></td>
		</tr>
		<tr class="">
			<td>Total Listings</td>
			<td></td>
		</tr>
		<tr class="">
			<td>Total Members</td>
			<td>1</td>
		</tr>
		<?php $this->events->trigger('admin/home/stats');?>
	</table>
	
	<?php if ($news): ?>
	<div class="qsearch">
		<h3>68kb News <a href="http://68kb.com/blog/rss"><img src="<?php echo $template;?>images/icons/small/rss.png" alt="RSS Feed" /></a></h3>
		<div id="blogrss" class="rssnews">
			<?php foreach ($news as $news_item) { ?>
				<h4><a href="<?php echo $news_item->get_permalink(); ?>" target='_blank'><?php echo $news_item->get_title(); ?></a></h4>
				<?php
				$desc = str_replace('[...]', '', $news_item->get_description());
				$desc = strip_tags($desc);
				$desc = word_limiter($desc, 50);
				?>
				<p><?php echo $desc; ?></p>
			<?php } ?>
		</div>
	</div>
	<?php endif; ?>
	
</div>

<div class="clear"></div>