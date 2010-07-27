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
class Events
{
	
	/**
	 * Global CI Object
	 */
	protected $_ci;
	
	/**
	 * Array of hook _listeners
	 */
	protected $_listeners = array();
	
	/**
	 * Array of add ons
	 */
	public $add_ons = array();
	
	/**
	 * Returned value from extension.
	 */
	protected $_call_it = array();
	
	// ------------------------------------------------------------------------
	
	/**
	 * Construct
	 * 
	 * Allow users to extend the system.
	 * Idea from the now defunct Iono
	 */
	public function __construct()
	{
		$this->_ci = CI_Base::get_instance();
		
		$this->_ci->benchmark->mark('add_ons_start');
		
		// Directory helper
		$this->_ci->load->helper('directory');
		
		// load any active modules first
		$this->_load_modules();
		
		// now auto load core helpers
		// by having this after the modules load it 
		// allows people to extend helpers
		$this->_core_helpers();
		
		$this->_ci->benchmark->mark('add_ons_end');
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Load Modules
	 *
	 * Loads all active modules 
	 *
	 */
	private function _load_modules()
	{
		// Confirm the modules table and add-ons are allowed.
		if ( ! $this->_ci->db->table_exists('modules') OR ! $this->_ci->config->item('allow_addons'))
		{
			return FALSE;
		}
		
		// See if we have the results cached.
		if ( ! $results = $this->_ci->cache->get('load_addons'))
		{
			$this->_ci->db->select('module_directory, module_name')
							->from('modules')
							->where('module_active', 'yes')
							->order_by('module_order')
							->order_by('module_display_name');

			$query = $this->_ci->db->get();

			$results = $query->result();

			$this->_ci->cache->write($results, 'load_addons', 60);
		}
		
		// And away we go...
		foreach ($results as $row)
		{
			
			// auto load any helpers
			$this->_load_helpers($row->module_directory);
			$this->_load_libraries($row->module_directory);

			$dir = $row->module_directory;
			$extension = strtolower(str_replace(EXT, '', str_replace('_extension', '', $dir)).'_extension');	
			$config = strtolower(str_replace(EXT, '', str_replace('_config', '', $dir)).'_config');	
			
			// Be sure config file exists else it can't be active.
			if (file_exists(EXTPATH . $row->module_directory .'/'. $config . '.xml'))
			{
				$this->add_ons[$row->module_name] = $row->module_directory;
			}
			
			if (file_exists(EXTPATH . $row->module_directory .'/'. $extension . EXT))
			{
				include_once(EXTPATH . $row->module_directory .'/'. $extension . EXT);
				$class = ucfirst($row->module_directory).'_extension';
				if (class_exists($class)) 
				{
					new $class($this);
					log_message('debug', 'Extension loaded: '.$extension);
				}
			}
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Load Core Helpers
	 *
	 * This function auto loads helpers.
	 *
	 * @access	public
	 * @param	array
	 * @return	void
	 */
	private function _core_helpers()
	{
		foreach (directory_map(APPPATH .'modules/', 2) AS $folder => $dir)
		{
			if ( ! is_array($dir))
			{
				continue;
			}
			
			if (in_array('helpers', $dir))
			{
				foreach (directory_map(APPPATH .'modules/'.$folder.'/helpers', 1) AS $file)
				{
					$helper_file = strtolower(str_replace(EXT, '', str_replace('_helper', '', $file)));
					if (file_exists(APPPATH.'modules/'.$folder.'/helpers/'.$helper_file.'_helper'.EXT))
					{
						$this->_ci->load->add_package_path(APPPATH.'modules/'.$folder.'/');
						$this->_ci->load->helper($helper_file);
					}
				}
			}
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Load any helpers for the module
	 *
	 * @param string	module directory
	 */
	private function _load_helpers($module_directory = '')
	{
		if ( ! is_dir(EXTPATH . $module_directory .'/helpers'))
		{
			return FALSE;
		}
		
		foreach (directory_map(EXTPATH . $module_directory .'/helpers', 1) AS $file)
		{
			if (strpos($file, '_helper') !== FALSE)
			{
				$helper_file = strtolower(str_replace(EXT, '', str_replace('_helper', '', $file)));
				if (file_exists(EXTPATH. $module_directory .'/helpers/'. $file))
				{
					$this->_ci->load->add_package_path(EXTPATH.$module_directory.'/');
					$this->_ci->load->helper($helper_file);
				}
			}
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Load any libraries for the module
	 *
	 * @param string	module directory
	 */
	private function _load_libraries($module_directory = '')
	{
		if ( ! is_dir(EXTPATH . $module_directory .'/libraries'))
		{
			return FALSE;
		}
		
		foreach (directory_map(EXTPATH . $module_directory .'/libraries', 1) AS $file)
		{
			if (strpos($file, '_library') !== FALSE)
			{
				$library_file = strtolower(str_replace(EXT, '', $file));
				if (file_exists(EXTPATH. $module_directory .'/libraries/'. $file))
				{
					$this->_ci->load->library($module_directory.'/'.$library_file);
				}
			}
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Register a listener for a given hook
	 *
	 * @param string $hook
	 * @param object $class_reference
	 * @param string $method
	 */
	public function register($hook, $class_reference, $method)
	{
		// Specifies a key so we can't define the same handler more than once
		$key = get_class($class_reference).'->'.$method;
		
		$this->_listeners[$hook][$key] = array($class_reference, $method);
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Trigger an event
	 *
	 * @param string $hook
	 * @param mixed $data
	 */
	public function trigger($hook, $data = '', $type = 'string')
	{
		// Reset the call it array
		$this->_call_it = array();
		
		// Now call any hooks
		if (isset($this->_listeners[$hook]) && is_array($this->_listeners[$hook]) && count($this->_listeners[$hook]) > 0)
		{
			foreach ($this->_listeners[$hook] as $listener)
			{
				// Set up variables
				$class = $listener[0];
				$method = $listener[1];
				if (method_exists($class,$method))
				{
					$this->_call_it[] = $class->$method($data);
				}
			}
		}
		
		return $this->trigger_return($type);
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Get the raw array of listener return values
	 * 
	 * @param string $type
	 * @return array
	 */
	public function trigger_return($type = 'string')
	{
		//return a concat string of all the _listeners returns
		if ($type == 'string') 
		{
			$string = '';
			foreach ($this->_call_it as $value) 
			{
				$string .= $value;
			}
			return $string;
		}
		//return an array of all the _listeners returns
		if ($type == 'array') 
		{
			return $this->_call_it;
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Find any active extensions for a given hook
	 *
	 * @param	string
	 * @return 	bool
	 **/
	public function active_hook($hook)
	{
		if (isset($this->_listeners[$hook]) && is_array($this->_listeners[$hook]) && count($this->_listeners[$hook]) > 0)
		{
			return TRUE;
		}
		return FALSE;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Active
	 * 
	 * Check if an add-on is active.
	 *
	 * @param	string
	 * @return 	bool
	 */
	public function active($add_on = '')
	{
		return (isset($this->add_ons[$add_on])) ? TRUE : FALSE;
	}
}
/* End of file Events.php */
/* Location: ./upload/includes/68kb/modules/addons/libraries/Events.php */ 