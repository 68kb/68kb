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
		
	<?php elseif($nav == 'articles'): ?>
	
		<li>
			<?php 
				$class = ($this->uri->segment(3) == 'articles') ? 'current' : '';
				echo build_link(site_url('admin/kb/articles'), lang('lang_articles'), 'content', $class);
			?>
		</li>
		<?php if ($this->users_auth->check_role('can_manage_categories')): ?>
		<li>
			<?php 
				$class = ($this->uri->segment(2) == 'categories') ? 'current' : '';
				echo build_link(site_url('admin/categories'), lang('lang_browsecats'), 'categories', $class);
			?>
		</li>
		<?php endif; ?>
		<li>
			<?php 
				$class = ($this->uri->segment(3) == 'glossary') ? 'current' : '';
				echo build_link(site_url('admin/kb/glossary'), lang('lang_glossary'), 'wrench', $class);
			?>
		</li>
		
		<?php $this->events->trigger('admin_tpl/articles');?>
		
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
		
		<?php $this->events->trigger('admin_tpl/settings');?>
	<?php endif; ?>

</ul>