<ul id="subtablist">
	
	<?php if($nav == 'home' || $this->uri->segment(2) == ''): ?>
	
		<li>
			<?php 
				if($nav == 'home' || $this->uri->rsegment(1) == '') $class = 'current';
				echo build_link(site_url('admin'), lang('lang_dashboard'), 'home', $class);
			?>
		</li>
		<li>
			<?php 
				$class = ($this->uri->segment(2) == 'listings') ? 'current' : '';
				echo build_link(site_url('admin/logout'), lang('lang_logout'), 'lock', $class);
			?>
		</li>
		
		<?php $this->events->trigger('admin_tpl/home');?>
		
	<?php elseif($nav == 'orders'): ?>
	
		<li>
			<?php 
				$class = ($this->uri->segment(2) == 'orders') ? 'current' : '';
				echo build_link(site_url('admin/orders'), lang('lang_manage_orders'), 'money', $class);
			?>
		</li>
		
		<?php if ($this->users_auth->check_role('can_manage_products')) { ?>
		<li>
			<?php 
				$class = ($this->uri->segment(2) == 'products') ? 'current' : '';
				echo build_link(site_url('admin/products'), lang('lang_manage_products'), 'package', $class);
			?>
		</li>
		<li>
			<?php 
				$class = ($this->uri->segment(3) == 'options') ? 'current' : '';
				echo build_link(site_url('admin/products/options'), lang('lang_product_options'), 'bricks', $class);
			?>
		</li>
		<?php } ?>
	
		<?php if ($this->users_auth->check_role('can_manage_coupons')) { ?>
		<li>
			<?php 
				$class = ($this->uri->segment(2) == 'coupons') ? 'current' : '';
				echo build_link(site_url('admin/coupons'), lang('lang_coupons_discounts'), 'basket', $class);
			?>
		</li>
		<?php } ?>
		
		<?php $this->events->trigger('admin_tpl/orders');?>
		
	<?php elseif ($nav=='content'): ?>
	
		<?php if ($this->users_auth->check_role('can_manage_content')) { ?>
		<li>
			<?php 
				$class = ($this->uri->segment(2) == 'pages') ? 'current' : '';
				echo build_link(site_url('admin/pages'), lang('lang_content_editor'), 'content', $class);
			?>
		</li>
		<?php } ?>
		
		<?php $this->events->trigger('admin_tpl/content');?>
		
	<?php elseif ($nav=='users'): ?>
		
		<?php if ($this->users_auth->check_role('can_manage_users')) { ?>
			<li>
				<?php 
					$class = ($this->uri->segment(2) == 'users') ? 'current' : '';
					echo build_link(site_url('admin/users'), lang('lang_manage_users'), 'user', $class);
				?>
			</li>
		
			<li>
				<?php 
					$class = ($this->uri->segment(2) == 'usergroups') ? 'current' : '';
					echo build_link(site_url('admin/usergroups'), lang('lang_user_groups'), 'users', $class);
				?>
			</li>
		<?php } ?>
		
		<?php $this->events->trigger('admin_tpl/users');?>
		
	<?php elseif ($nav=='listings'): ?>
		
		<?php if ($this->users_auth->check_role('can_manage_categories')) { ?>
			<li>
				<?php 
					$class = ($this->uri->segment(2) == 'categories') ? 'current' : '';
					echo build_link(site_url('admin/categories'), lang('lang_browsecats'), 'categories', $class);
				?>
			</li>
		<?php } ?>
		
		<?php $this->events->trigger('admin_tpl/listings');?>
		
	<?php elseif($nav == 'settings'): ?>
	
		<li>
			<?php 
				$class = ($this->uri->segment(2) == 'settings') ? 'current' : '';
				echo build_link(site_url('admin/settings'), lang('lang_settings'), 'settings', $class);
			?>
		</li>
		
		<?php if ($this->users_auth->check_role('can_manage_themes')) { ?>
		<li>
			<?php 
				$class = ($this->uri->segment(2) == 'themes') ? 'current' : '';
				echo build_link(site_url('admin/themes'), lang('lang_theme_settings'), 'themes', $class);
			?>
		</li>
		<?php } ?>
		
		<?php if ($this->users_auth->check_role('can_manage_modules')) { ?>
		<li>
			<?php 
				$class = ($this->uri->segment(2) == 'modules') ? 'current' : '';
				echo build_link(site_url('admin/addons'), lang('lang_modules'), 'plugin', $class);
			?>
		</li>
		<?php } ?>
		
		<?php $this->events->trigger('admin_tpl/settings');?>
	<?php endif; ?>

</ul>