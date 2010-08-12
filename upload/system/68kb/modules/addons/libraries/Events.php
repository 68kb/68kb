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
			$dir = $row->module_directory;
			$extension = strtolower(str_replace(array(EXT, '_extension'), '', $dir).'_extension');
			$config = strtolower(str_replace(array(EXT, '_config'), '', $dir).'_config');
			
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
						$this->_ci->load->helper($folder.'/'.$helper_file);
					}
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
	
	// ------------------------------------------------------------------------
	
	/**
	 * Process tags
	 * 
	 * This is used to process tags from a string. (DB Results)
	 *
	 * @param	string - The data
	 * @return 	string - The processed data
	 */
	public function process_tags($data = '')
	{
		$result = $this->_ci->simpletags->parse($data, array(), array($this->_ci->events, 'parser_callback'));
		return $result['content'];
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Callback from template parser
	 *
	 * @param	array
	 * @return 	mixed
	 */
	public function parser_callback($data)
	{
		if ( ! isset($data['segments'][0]) OR ! isset($data['segments'][1]))
		{
			return FALSE;
		}
		
		// Setup our paths from the data array
		$class = $data['segments'][0];
		$method = $data['segments'][1];
		$addon = strtolower($class);
		$return_data = '';
		
		// This loads a library from the addons directory.
		if (in_array($class, $this->add_ons))
		{
			$addon_path = EXTPATH.$class.'/libraries/'.$class.EXT;
			if ( ! file_exists($addon_path))
			{
				log_message('error', 'Unable to load: '.$class);
				$return = FALSE;
			}
			else
			{
				// Load that library
				$this->_ci->load->library($class.'/'.$class, $data);
				
				// How about a language file?
				$lang_path = EXTPATH.$class.'/language/'.$this->_ci->config->item('language').'/'.$addon.'_lang'.EXT;
				if (file_exists($lang_path))
				{
					$this->_ci->lang->load($addon.'/'.$addon);
				}
				
				// Now the fun stuff! 
				$return_data = $this->_process($class, $method, $data);
			}
		}
		else 
		{
			// Now we are going to check the core "modules" and see if this is what they want
			$addon_path = APPPATH.'modules/'.$class.'/libraries/'.ucfirst($class).'_parser'.EXT;
			if ( ! file_exists($addon_path))
			{
				$addon_path = APPPATH.'modules/kb/libraries/'.ucfirst($class).'_parser'.EXT;
				if ( ! file_exists($addon_path))
				{
					log_message('error', 'Unable to load: '.$class);
					$return = FALSE;
				}
				else
				{
					$this->_ci->load->library('kb/'.$class.'_parser', $data);
					$return_data = $this->_process($class.'_parser', $method, $data);
				}
			}
			else
			{
				$this->_ci->load->library($class.'/'.$class.'_parser', $data);
				$return_data = $this->_process($class.'_parser', $method, $data);
			}
		}
		
		if (is_array($return_data) && ! empty($return_data))
		{
			if ( ! $this->_is_multi($return_data))
			{
				$return_data = $this->_make_multi($return_data);
			}
			
			$content = $data['content'];
			$parsed_return = '';
			$simpletags = new Simpletags();
			foreach ($return_data as $result)
			{
				$parsed = $simpletags->parse($content, $result);
				$parsed_return .= $parsed['content'];
			}
			unset($simpletags);

			$return_data = $parsed_return;
		}
		
		return $return_data;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Process
	 *
	 * Just process the class
	 *
	 * @access	private
	 * @param	object
	 * @param	string
	 * @param	array
	 * @return	mixed
	 */
	private function _process($class, $method, $data)
	{
		if (method_exists($class, $method))
		{
			return $this->_ci->$class->$method($data);
		}
		return FALSE;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Ensure we have a multi array
	 */
	private function _is_multi($array) 
	{
		return (count($array) != count($array, 1));
	}
	
	// --------------------------------------------------------------------

	/**
	 * Forces a standard array in multidimensional.
	 *
	 * @param	array
	 * @param	int		Used for recursion
	 * @return	array	The multi array
	 */
	private function _make_multi($flat, $i=0) 
	{ 
	    $multi = array(); 
	    foreach ($flat as $item => $value) 
	    { 
	        $return[$i][$item] = $value;
	    } 
	    return $return; 
	}
}
/* End of file Events.php */
/* Location: ./upload/system/68kb/modules/addons/libraries/Events.php */ 