<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * iClassEngine
 *
 * THIS IS COPYRIGHTED SOFTWARE
 * PLEASE READ THE LICENSE AGREEMENT
 * http://iclassengine.com/user_guide/lCInse.html
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
 * Users Parser Library
 * 
 * @subpackage	Libraries
 * @link		http://iclassengine.com/user_guide/
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
/* Location: ./upload/includes/iclassengine/modules/users/libraries/Users_parser.php */ 