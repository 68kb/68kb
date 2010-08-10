/*-------------------------
* Idea for this from
* CodeIgniter User Guide
-------------------------*/
function create_menu(basepath)
{
	var base = (basepath == 'null') ? '' : basepath;

	document.write(
		'<table cellpadding="0" cellspaceing="0" align="center" border="0" style="width:98%"><tr>' +
		'<td class="td" valign="top">' +

		'<ul>' +
		'<li><a href="'+base+'index.html">User Guide Home</a></li>' +	
		'<li><a href="'+base+'toc.html">Table of Contents Page</a></li>' +
		'</ul>' +	

		'<h3>Basic Info</h3>' +
		'<ul>' +
			'<li><a href="'+base+'general/requirements.html">System Requirements</a></li>' +
			'<li><a href="'+base+'license.html">License Agreement</a></li>' +
			'<li><a href="'+base+'changelog.html">Change Log</a></li>' +
			'<li><a href="'+base+'general/credits.html">Credits</a></li>' +
		'</ul>' +	
		
		'<h3>Installation</h3>' +
		'<ul>' +
			'<li><a href="'+base+'installation/index.html">Installation Instructions</a></li>' +
			'<li><a href="'+base+'installation/upgrading.html">Upgrading Instructions</a></li>' +
			'<li><a href="'+base+'installation/troubleshooting.html">Troubleshooting</a></li>' +
		'</ul>' +
		

				
		'</td><td class="td_sep" valign="top">' +

		'<h3>Admin Operations</h3>' +
		'<ul>' +
			'<li><a href="'+base+'overview/dashboard.html">Dashboard</a></li>' +
			'<li><a href="'+base+'overview/articles.html">Articles</a></li>' +
			'<li><a href="'+base+'overview/categories.html">Categories</a></li>' +
			'<li><a href="'+base+'overview/glossary.html">Glossary</a></li>' +
			'<li><a href="'+base+'overview/users.html">Users</a></li>' +
			'<li><a href="'+base+'overview/comments.html">Comments</a></li>' +
			'<li><a href="'+base+'overview/settings.html">Settings</a><ul>' +
				'<li><a href="'+base+'overview/utilities.html">Utilities</a></li>' +
			'</ul></li>' +
		'</ul>' +
		
		'</td><td class="td_sep" valign="top">' +

		
		'<h3>Templates &amp; Themes</h3>' +
		'<ul>' + 
		'<li><a href="'+base+'design/themes.html">Working with themes</a></li>' + 
		'<li><a href="'+base+'design/tags.html">68KB Theme Tags</a></li>' + 
		'</ul>' + 
		
		'<h3>Advanced Operations</h3>' +
		'<ul>' + 
		'<li><a href="'+base+'developer/config.html">Config File</a></li>' +
		'<li><a href="'+base+'developer/translate.html">Translating and Localization</a></li>' +
		'<li><a href="'+base+'developer/urls.html">68KB URLs</a></li>' +
		'<li><a href="'+base+'developer/caching.html">Caching</a></li>' +
		'</ul>' + 
		
		'</td><td class="td_sep" valign="top">' +
		
		'<h3>Developer</h3>' +
		'<ul>' + 
		'<li><a href="'+base+'developer/modules.html">Building Add-Ons</a></li>' +
		'<li><a href="'+base+'developer/api.html">XML-RPC API</a></li>' +
		'</ul>' +

		'<h3>Additional Resources</h3>' +
		'<ul>' +
		'<li><a href="http://68kb.com/forums/">Community Forums</a></li>' +
		'</ul>' +	
		
		'</td></tr></table>');
}