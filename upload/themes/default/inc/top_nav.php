<ul>
	<li><a href="{kb:site:link}" <?php if ($module == '') echo 'class="current"'; ?>>Home</a></li>
	<li><a href="{kb:site:link}categories" <?php if ($this->uri->segment(1) == 'categories') echo 'class="current"'; ?>>Categories</a></li>
	<li><a href="{kb:site:link}glossary" <?php if ($this->uri->segment(1) == 'glossary') echo 'class="current"'; ?>>Glossary</a></li>
	<li><a href="{kb:site:link}search" <?php if ($module == 'search') echo 'class="current"'; ?>>Advanced Search</a></li>
</ul>