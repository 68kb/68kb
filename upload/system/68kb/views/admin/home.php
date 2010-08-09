<div class="grid_10 alpha">
	<div id="content">
		
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