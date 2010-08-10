<?php if (kb_user_logged_in()): ?>
	<p>
		<a href="<?php echo site_url('users/profile/{kb:users:user_name}')?>" class="first">
		<span>Welcome,</span> {kb:users:user_name}</a> | <a href="<?php echo site_url('users/account')?>">My Account</a>
	</p>
<?php else: ?>
	<p>
		<a href="<?php echo site_url('users/login') ?>">Login</a> | <a href="<?php echo site_url('users/register') ?>">Register</a>
	</p>
<?php endif; ?>