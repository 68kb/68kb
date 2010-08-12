<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * iClassEngine
 *
 * THIS IS COPYRIGHTED SOFTWARE
 * PLEASE READ THE LICENSE AGREEMENT
 * http://iclassengine.com/user_guide/policies/license
 *
 * @package		iClassEngine
 * @author		ICE Dev Team
 * @copyright	Copyright (c) 2010, 68 Designs, LLC
 * @license		http://iclassengine.com/user_guide/policies/license
 * @link		http://iclassengine.com
 * @since		Version 1.0
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
if ( ! function_exists('ice_user_logged_in'))
{
	function ice_user_logged_in()
	{
		$CI =& get_instance();
	
		return $CI->users_auth->logged_in();
	}
}

/* End of file user_helper.php */
/* Location: ./upload/includes/iclassengine/modules/users/helpers/user_helper.php */ 