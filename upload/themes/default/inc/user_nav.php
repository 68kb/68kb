<?php if (kb_user_logged_in()): ?>
	<p>
		<a href="{kb:site:link}users/profile/{kb:users:user_name}" class="first">
		<span>Welcome,</span> {kb:users:user_name}</a> | <a href="{kb:site:link}users/account">My Account</a>
	</p>
<?php else: ?>
	<p>
		<a href="{kb:site:link}users/login">Login</a> | <a href="{kb:site:link}users/register">Register</a>
	</p>
<?php endif; ?>