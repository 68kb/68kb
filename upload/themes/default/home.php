<div id="home">
	
	{kb:categories:table heading="{lang:lang_categories}" cat_parent="0" cols="3" show_total="yes"}
	<hr />
	
	<div class="grid_4 alpha">
		<h3>Most Popular</h3>
		<ol>
			{kb:articles:get sort_column="article_hits" sort_order="desc"}
				<li><a href="{kb:article_url}">{kb:article_title}</a></li>
			{/kb:articles:get}
		</ol>
	</div>
	<div class="grid_4 omega">
		<h3>Recent Articles</h3>
		<ol>
			{kb:articles:get sort_column="article_date" sort_order="desc"}
				<li><a href="{kb:article_url}">{kb:article_title}</a></li>
			{/kb:articles:get}
		</ol>
	</div>
</div>