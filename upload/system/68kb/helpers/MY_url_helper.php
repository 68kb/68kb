<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 68kb
 *
 * An open source knowledge base script
 *
 * @package		68kb
 * @author		Eric Barnes (http://ericlbarnes.com)
 * @copyright	Copyright (c) 2010, 68kb
 * @license		http://68kb.com/user_guide/license.html
 * @link		http://68kb.com
 * @since		Version 2.0
 */

// ------------------------------------------------------------------------

/**
 * Site URL
 *
 * Create a local URL based on your basepath. Segments can be passed via the
 * first parameter either as a string or an array.
 *
 * This has been modified to use a custom admin directory specified in config.
 *
 * @access	public
 * @param	string
 * @return	string
 */
if ( ! function_exists('site_url'))
{
	function site_url($uri = '')
	{
		$CI =& get_instance();
		
		// Replace admin with changed admin_directory.
		if (substr($uri, 0, 5) == 'admin')
		{
			$uri = str_replace('admin', $CI->config->item('admin', 'triggers'), $uri);
		}
		
		if (substr($uri, 0, 5) == 'users')
		{
			$uri = str_replace('users', $CI->config->item('users', 'triggers'), $uri);
		}
		
		if (substr($uri, 0, 10) == 'categories')
		{
			$uri = str_replace('categories', $CI->config->item('categories', 'triggers'), $uri);
		}
		
		return $CI->config->site_url($uri);
	}
}