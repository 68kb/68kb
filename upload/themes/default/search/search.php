<h2>{lang:lang_search}</h2>

{kb:search:form class="search_form" show_categories="yes"}
	<p><label for="keywords">{lang:lang_keywords}:</label> <input type="text" name="keywords" value="" /></p>
	<p><label for="cats">{lang:lang_category}:</label> {kb:cats}</p>
	<p class="continue"><input type="submit" name="submit" value="<?php echo lang('lang_search'); ?>" /></p>
{/kb:search:form}