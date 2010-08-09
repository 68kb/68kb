
<?php // Any subcategories? ?>
{kb:categories:table cat_parent="<?php echo $category_id; ?>"}

<?php if (isset($category_id)): ?>
	
	<h2>{kb:cat_name} Articles</h2>
	
	<ul class="articles">
		{kb:articles:get category="<?php echo $category_id; ?>"}
			<li><a href="{kb:article_url}">{kb:article_title}</a></li>
		{/kb:articles:get}
	</ul>
	
	<div class="pagination">
		{kb:articles:paging}
	</div>
	
<?php endif; ?>