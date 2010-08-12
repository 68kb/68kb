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
 * Get a specific Setting
 *
 * @subpackage	Helpers
 * @param		int
 * @return 		string
 */
if ( ! function_exists('ice_get_setting'))
{
	function ice_get_setting($param)
	{
		$CI =& get_instance();
	
		return $CI->settings->get_setting($param);
	}
}

/* End of file settings_helper.php */
/* Location: ./upload/includes/68kb/modules/settings/helpers/settings_helper.php */ 