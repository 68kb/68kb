<h1>{kb:article:article_title}</h1>
{kb:article:article_id}
{kb:article:article_description}

<div class="info">
	<p>{lang:lang_category}: {kb:article_cats}</p>
	<p>{lang:lang_last_updated}: {kb:article:article_modified} with {kb:article:article_hits} views</p>
</div>

<?php if ($attach): ?>
	<fieldset>
		<legend>{lang:lang_attachments}</legend>
		
		<ul>
		{kb:attach}
			<li><a href="{kb:download_path}" target="_blank">{kb:attach_title}</a></li>
		{/kb:attach}
		</ul>
	</fieldset>
<?php endif; ?>