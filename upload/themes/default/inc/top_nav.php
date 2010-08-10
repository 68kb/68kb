<ul>
	<li><a href="index.php" title="css menus" <?php if ($module == '') echo 'class="current"'; ?>>Home</a></li>
	<li><a href="<?php echo site_url('categories'); ?>" <?php if ($this->uri->segment(1) == 'categories') echo 'class="current"'; ?>>Categories</a></li>
	<li><a href="<?php echo site_url('glossary'); ?>" <?php if ($this->uri->segment(1) == 'glossary') echo 'class="current"'; ?>>Glossary</a></li>
	<li><a href="<?php echo site_url('search'); ?>" <?php if ($module == 'search') echo 'class="current"'; ?>>Advanced Search</a></li>
</ul>