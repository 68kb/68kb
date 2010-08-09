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

class modules_check
{
	private $_ci;
	
	protected $uri_string;
	
	protected $allowed = array();
	
	/**
	 * Construct
	 */
	public function __construct()
	{
		$this->_ci = CI_Base::get_instance();
		
		$this->_ci->load->helper('directory');
		
		$this->allowed = directory_map(APPPATH .'modules/', TRUE);
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Confirm a module can be used
	 */
	function modules_check()
	{
		$directory = $this->_ci->router->fetch_module();		

		if ($this->_ci->uri->segment(1) == 'setup')
		{
			return TRUE;
		}
		
		if ( ! $this->_ci->db->table_exists('modules'))
		{
			show_404($directory);
		}

		if ($directory && ! in_array($directory, $this->allowed))
		{
			if ( ! $this->_ci->config->item('allow_addons'))
			{
				show_404($directory);
			}
			
			if ( ! in_array($directory, $this->_ci->events->add_ons))
			{
				show_404($directory);
			}
		}
	}
}

/* End of file Modules.php */
/* Location: ./upload/includes/application/hooks/Modules.php */