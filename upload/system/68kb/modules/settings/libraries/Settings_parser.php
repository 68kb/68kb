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
 * Settings Parser
 * 
 * @subpackage	Parser
 * @link		http://68kb.com/user_guide/
 *
 */
class Settings_parser
{	
	private $_ci;
	
	function __construct()
	{
		$this->_ci =& get_instance();
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Get Setting (Notice Singular)
	 *
	 * Used to pull out one specific setting from the settings table.
	 *
	 * Here is an example: 
	 * <code>
	 * {kb:settings:get name="site_name"}
	 * </code>
	 *
	 * @access	public
	 * @param	string
	 * @return	mixed
	 */
	public function get($data = array())
	{
		$option_name = (int) $this->_get_param('name', $data);
		
		return $this->_ci->settings->get_setting($option_name);
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
/* End of file Settings_parser.php */
/* Location: ./upload/system/68kb/modules/settings/libraries/Settings_parser.php */ 