<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 68kb
 *
 * An open source knowledge base script
 *
 * @package		68kb
 * @author		68kb Dev Team
 * @copyright	Copyright (c) 2010, 68kb
 * @license		http://68kb.com/user_guide/license.html
 * @link		http://68kb.com
 * @since		Version 2.0
 */

// ------------------------------------------------------------------------

/**
 * Get a field for a user
 *
 * @subpackage	Helpers
 * @param		int
 * @return 		string
 */
if ( ! function_exists('ice_get_user_data'))
{
	function ice_get_user_data($param)
	{
		$CI =& get_instance();
	
		return $CI->users_auth->get_data($param);
	}
}

// ------------------------------------------------------------------------

/**
 * Find out if the user is logged in.
 *
 * @subpackage	Helpers
 * @param		int
 * @return 		bool
 */
if ( ! function_exists('kb_user_logged_in'))
{
	function kb_user_logged_in()
	{
		$CI =& get_instance();
	
		return $CI->users_auth->logged_in();
	}
}

/* End of file user_helper.php */
/* Location: ./upload/includes/68kb/modules/users/helpers/user_helper.php */ 