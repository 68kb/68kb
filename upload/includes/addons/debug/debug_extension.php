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
 * Debug Module
 *
 * This is used to display debug information. 
 *
 * @subpackage	Modules
 * @link		http://68kb.com/user_guide/addons/debug
 *
 */
class Debug_extension
{
	
	private $_ci;
	
	/**
	 * Setup events
	 *
	 * @access	public
	 */
	public function __construct($modules)
	{
		$this->_ci = CI_Base::get_instance();
		$modules->register('template/build', $this, 'debug_footer');
		$this->_ci->config->set_item('debug', TRUE);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Set error reporting
	 *
	 * @access	public
	 */	
	public function errorset()
	{
		error_reporting(-1);
		ini_set("display_errors", 1);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Show the debug in the footer.
	 *
	 * @access	public
	 * @return	string
	 */	
	public function debug_footer()
	{
		$this->errorset();
		if ($this->_ci->users_auth->get_data('user_group') == 1)
		{
			$this->_ci->output->enable_profiler(TRUE);
		}
	}
}

/* End of file debug_extension.php */
/* Location: ./upload/includes/addons/debug/debug_extension.php */ 