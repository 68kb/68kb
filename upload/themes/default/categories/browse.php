<h2>{kb:cat_name}</h2>
{kb:cat_description}

<ul class="articles">
	{kb:articles:get category="<?php echo $category_id; ?>"}
		<li><a href="{kb:article_url}">{kb:article_title}</a></li>
	{/kb:articles:get}
</ul>

{kb:articles:paging}