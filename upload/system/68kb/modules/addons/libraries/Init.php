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
 * Events
 *
 * @subpackage	Libraries
 * @link		http://68kb.com/user_guide/
 *
 */
class Init
{
	// CI Instance
	protected $_ci;
	
	protected $_mode = 'install';

	// ------------------------------------------------------------------------
	
	/**
	 * Setup CI Object
	 */
	public function __construct()
	{
		$this->_ci = CI_Base::get_instance();
		$this->_ci->load->dbforge();
		$this->_ci->load->dbutil();
	}

	// ------------------------------------------------------------------------
	
	/**
	 * Install Tables
	 *
	 * Loop through the class methods finding those named "table_" and run them.
	 *
	 * @return 	string
	 */
	public function install()
	{
		$msg = '';
		
		$this->_mode = 'install';
		
		$class_methods = get_class_methods($this);

		foreach ($class_methods as $method_name)
		{
			if (substr($method_name, 0, 6) == 'table_')
			{
				$msg .= $this->$method_name();
			}
		}
		return $msg;
	}

	// ------------------------------------------------------------------------
	
	/**
	 * Upgrade an Add-On
	 *
	 * Loop through the class methods finding those named "upgrade_" and run them.
	 *
	 * @return 	string
	 */
	public function upgrade($version = '')
	{
		$msg = '';
		
		$this->_mode = 'upgrade';
		
		$class_methods = get_class_methods($this);

		foreach ($class_methods as $method_name)
		{
			if (substr($method_name, 0, 6) == 'upgrade_')
			{
				$msg .= $this->$method_name();
			}
		}
		return $msg;
	}

	// ------------------------------------------------------------------------
	
	/**
	 * Uninstall an Add-On
	 *
	 * Loop through the class methods finding those named "table_" and delete them.
	 *
	 * @return 	string
	 */
	public function uninstall()
	{
		$this->_mode = 'uninstall';
		
		$class_methods = get_class_methods($this);
		
		foreach ($class_methods as $method_name) 
		{
			if (substr($method_name, 0, 6) == 'table_')
			{
				$drop = str_replace('table_', '', $method_name);
				$this->_ci->dbforge->drop_table($drop);
			}
		}
		return TRUE;
	}
}
/* End of file Init.php */
/* Location: ./upload/includes/68kb/modules/addons/libraries/Init.php */ 