
<?php // Any subcategories? ?>
{kb:categories:table heading="Sub Categories" show_total="yes" cat_parent="{category_id}"}

<?php if (isset($category_id) && $category_id > 0): ?>

	<h2 class="cat_name">{kb:cat_name}</h2>
	<p class="cat_description">{kb:cat_description}</p>

	<?php if ($has_articles > 0): ?>

		<ul class="articles">
			{kb:articles:get category="{category_id}" per_page="2"}
				<li><a href="{kb:article_url}">{kb:article_title}</a></li>
			{/kb:articles:get}
		</ul>

		<div class="pagination">
			{kb:articles:paging}
		</div>

	<?php else: ?>

		<p>{lang:lang_no_articles}</p>

	<?php endif; ?>

<?php endif; ?>