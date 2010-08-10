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
 * Auth Library
 * 
 * Modified for our use from:
 * http://www.bramme.net/2008/07/auth-library-for-codeigniter-tutorial/
 *
 * @subpackage	Libraries
 *
 */
class Users_parser
{	
	private $_ci;
	
	private $_data = array();
	
	function __construct($data = array())
	{
		$this->_ci =& get_instance();
		$this->_data = $data;
	}
	
	/**
	* Content Plugin
	*
	* @param	array
	* @return 	mixed
	*/
	function user_name($data)
	{
		return $this->_ci->users_auth->get_data('user_username');
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Generate a gravatar
	 */
	function gravatar($data)
	{
		$defaults = array('email' => '', 'size' => 60, 'rating' => 'PG');
		
		$options = $this->_ci->settings->get_params($data['attributes'], $defaults);
		
		if ($options['email'])
		{
			return gravatar($options['email'], $options['rating'], $options['size']);
		}
		
		return FALSE;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Get a single param
	 *
	 * @param	string  - The array key
	 * @return	mixed 	- The value
	 */
	private function _get_param($key, $data)
	{
		if (isset($data['attributes'][$key])) 
		{
			return $data['attributes'][$key];
		}
		return FALSE;
	}
}

/* End of file Users_parser.php */
/* Location: ./upload/system/68kb/modules/users/libraries/Users_parser.php */ 