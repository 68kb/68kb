<ul>
	<li <?php if($nav=='home') echo 'class="selected"'; ?>>
		<a href="<?php echo site_url('admin'); ?>">
			<span><img src="<?php echo $template;?>images/icons/small/home.png" alt="<?php echo lang('lang_dashboard'); ?>" /><?php echo lang('lang_dashboard'); ?></span>
		</a>
	</li>
	
	<?php if ($this->users_auth->check_role('can_manage_articles')) { ?>
	<li <?php if($nav=='articles') echo 'class="selected"'; ?>>
		<a class="" href="<?php echo site_url('admin/kb/articles'); ?>">
			<span><img src="<?php echo $template;?>images/icons/small/content.png" alt="<?php echo lang('lang_articles'); ?>" /><?php echo lang('lang_articles'); ?></span>
		</a>
	</li>
	<?php } ?>
	
	<?php if ($this->users_auth->check_role('can_manage_users')) { ?>
	<li <?php if($nav=='users') echo 'class="selected"'; ?>>
		<a class="" href="<?php echo site_url('admin/users'); ?>">
			<span><img src="<?php echo $template;?>images/icons/small/user.png" alt="<?php echo lang('lang_users'); ?>" /><?php echo lang('lang_users'); ?></span>
		</a>
	</li>
	<?php } ?>
	
	<?php if ($this->users_auth->check_role('can_manage_modules')) { ?>
	<li <?php if($nav=='addons') echo 'class="selected"'; ?>>
		<a class="" href="<?php echo site_url('admin/addons'); ?>">
			<span><img src="<?php echo $template;?>images/icons/small/plugin.png" alt="<?php echo lang('lang_modules'); ?>" /><?php echo lang('lang_modules'); ?></span>
		</a>
	</li>
	<?php } ?>
	
	<li <?php if($nav=='settings') echo 'class="selected"'; ?>>
		<a class="" href="<?php echo site_url('admin/settings'); ?>">
			<span><img src="<?php echo $template;?>images/icons/small/settings.png" alt="<?php echo lang('lang_settings'); ?>" /><?php echo lang('lang_settings'); ?></span>
		</a>
	</li>
	
	<?php $this->events->trigger('admin_tpl/nav');?>
</ul>