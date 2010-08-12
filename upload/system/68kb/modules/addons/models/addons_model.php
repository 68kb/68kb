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
 * Add-Ons Model
 *
 * @subpackage	Models
 * @link		http://68kb.com/user_guide/
 * 
 */
class Addons_model extends CI_Model {

	/**
	 * Location of add-ons directory
	 * @access 	private
	 * @var 	string
	 */
 	private $_addons_dir = '';
	
	/**
	 * Active add-ons
	 * @access	private
	 * @var		array
	 */
	private $_active_addons = array();
	
	// ------------------------------------------------------------------------
	
	/**
	 * Constructor
	 *
	 * Setup add-ons path.
	 **/
	public function __construct()
	{
		parent::__construct();
		
		log_message('debug', 'Add-ons model Initialized');
		
		$this->_addons_dir = EXTPATH;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Loads active add-ons
	 *
	 * @uses 	exists
	 * @return 	array
	 */
	public function load_active()
	{
		$modules = array();
		
		$result = $this->_get_addons();
		
		if (empty($result))
		{
			return FALSE;
		}
		
		foreach ($result as $row)
		{	
			if ( ! file_exists($this->_addons_dir . $row['module_name'].'/'.$row['module_name'].'_config.xml'))
			{
				continue;
			}

			$data = $this->_get_config($row['module_name']);
			
			if (empty($data))
			{
				continue;
			}
			
			$row['server_version'] = $data['module_version'];
			$row['module_admin'] = $data['module_admin'];
			$row['help_file'] = $data['help_file'];
			
			unset($data);
			
			$modules[] = $row;
		}
		return $modules;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Loads inactive add-ons
	 *
	 * Scans the add-ons directory for inactive add-ons
	 *
	 * @uses		_get_addons
	 * @uses		_get_config
	 * @return 		array
	 */
	public function load_inactive()
	{
		$this->load->helper('directory');
		
		// Get all active add-ons.
		$active = $this->_get_addons();
		
		// Setup an active array because in_array doesn't work with multideminsions.
		$active_array = array();

		if (is_array($active) && count($active) > 0)
		{
			foreach ($active AS $item)
			{
				$active_array = array_merge(array($item['module_directory']), $active_array);
			}
		}
	
		$available_module = array();
		
		// Scan all add-ons looking for inactive ones.
		$map = directory_map($this->_addons_dir, 1);
		
		foreach ($map AS $directory => $file)
		{
			if (file_exists($this->_addons_dir . $file.'/'.$file.'_config.xml'))
			{
				if ( ! $active OR ! in_array($file, $active_array))
				{
					$available_module[] = $this->_get_config($file);
				}
			}
		}
		
		return $available_module;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Get an add-on's config.php and return the $data array.
	 * If the config.php file is not found or the $data array
	 * is not present it returns an empty array.
	 *
	 * @param 	string
	 * @return 	array
	 */
	private function _get_config($module_directory)
	{
		$config = strtolower($this->security->xss_clean($module_directory));
		
		if ( ! file_exists($this->_addons_dir . $module_directory.'/'.$module_directory.'_config.xml'))
		{
			return FALSE;
		}
		
		$data = array();
		
		$config = simplexml_load_file($this->_addons_dir . $module_directory.'/'.$module_directory.'_config.xml');
		/*
		foreach ($config->children() as $child) 
		{
			$name = $child->getName();
			if ( ! $child->children()) 
			{
				$data[$name] = (string) $child[0];
			} 
			else 
			{
				foreach ($child->children() as $childs) 
				{
					$child_name = $childs->getName();
					$data[$name][$child_name] = (string) $childs[0];
				}
			}
		}
		// print_r($data); die;
		*/
		
		$data['module_name'] 			= $module_directory;
		$data['module_display_name'] 	= (string) $config->title;
		$data['module_version'] 		= (string) $config->version;
		$data['module_description'] 	= (string) $config->description;
		$data['module_order'] 			= (int) $config->order;
		
		// Check any dependencies 
		if ($config->dependencies)
		{
			$data['required'] = $config->dependencies->required;
			$data['optional'] = $config->dependencies->optional;
		}
		
		// does it have an admin?
		$data['module_admin'] = (file_exists($this->_addons_dir . $module_directory.'/controllers/admin.php')) ? TRUE : FALSE;
		
		// does it have a help file?
		$data['help_file'] = (file_exists($this->_addons_dir.'/'.$module_directory.'/'.$module_directory.'_readme.txt')) ? TRUE : FALSE;

		// does it have an init file?
		$data['uninstall'] = (file_exists($this->_addons_dir.'/'.$module_directory.'/'.$module_directory.'_init.php')) ? TRUE : FALSE;
		
		return $data;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Get an add-on's config.php and return the $data array.
	 * If the config.php file is not found or the $data array
	 * is not present it returns an empty array.
	 *
	 * @param 	string
	 * @return 	array
	 */
	public function get_config($module_directory)
	{
		$config = strtolower($this->security->xss_clean($module_directory));
		
		if ( ! file_exists($this->_addons_dir . $module_directory.'/'.$module_directory.'_config.xml'))
		{
			return FALSE;
		}
		
		$data = array();
		
		$config = simplexml_load_file($this->_addons_dir . $module_directory.'/'.$module_directory.'_config.xml');
		
		foreach ($config->children() as $child) 
		{
			$name = $child->getName();
			
			if ( ! $child->children()) 
			{
				$data[$name] = (string) $child[0]; //name = value, etc = value
			} 
			else 
			{
				foreach ($child->children() as $the_child) 
				{
					$child_name = $the_child->getName();
					if ( ! $the_child->children()) 
					{
						$data[$name][$child_name] = (string) $the_child[0]; //dependency[required] = value
					} 
					else 
					{
						foreach ($the_child->children() as $sub_child) 
						{
		       				//last level since no other existing elements go deeper
		       				$sub_child_name = $sub_child->getName();
		       				$data[$name][$sub_child_name][] = (string) $sub_child[0]; //dependency[required][0], dependency[required][1]
  						}
					}
				}
			}
		}
		return $data;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Checks if a module file exists
	 *
	 * @param	string
	 * @param	string
	 * @return 	boolean
	 */
	public function exists($module_directory, $file = '')
	{
		if ($file == '')
		{
			$file = strtolower($module_directory.'_config.xml');
		}
		
		if (file_exists($this->_addons_dir.'/'.$module_directory.'/'.$file))
		{
			return TRUE;
		}
		return FALSE;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Activate a module
	 * 
	 * @uses 	exists
	 * @uses 	_get_config
	 * @uses	init_module
	 * @param 	string
	 * @return	bool
	 */
	public function activate($module_directory)
	{
		if ( ! $this->exists($module_directory))
		{
			return FALSE;
		}
		
		$this->db->from('modules')->where('module_directory', $module_directory);
		
		$query = $this->db->get();
		
		if ($query->num_rows() > 0)
		{
			return FALSE;
		}
		
		if ( ! $data = $this->_get_config($module_directory))
		{
			return FALSE;
		}
		
		// Check required 
		if (isset($data['required']) && ! empty($data['required']))
		{
			$fail = FALSE;
			
			$required = lang('lang_dependencies') . '<br />';
			
			foreach ($data['required'] AS $item)
			{
				if ( ! $this->events->active(strtolower($item)))
				{
					$required .= $item.'<br />';
					$fail = TRUE;
				}
			}
			
			if ($fail)
			{
				$this->session->set_flashdata('error', $required);
				return FALSE;
			}
		}
		
		// Check optionals
		if (isset($data['optional']) && ! empty($data['optional']))
		{
			$show_message = FALSE;
			
			$optional = lang('lang_optional_dependencies') . '<br />';
			foreach ($data['optional'] AS $item)
			{
				if ( ! $this->events->active(strtolower($item)))
				{
					$optional .= $item.'<br />';
					$show_message = TRUE;
				}
			}
			
			if ($show_message)
			{
				$this->session->set_flashdata('info', $optional);
			}
		}
		
		// Module doesn't exist
		$module_data = array(
			'module_name' 			=> $data['module_name'], 
			'module_display_name' 	=> $data['module_display_name'], 
			'module_description' 	=> $data['module_description'], 
			'module_directory' 		=> $data['module_name'], 
			'module_version' 		=> $data['module_version'], 
			'module_order'	 		=> $data['module_order'], 
			'module_active' 		=> 'yes'
		);

		$this->db->insert('modules', $module_data);
		
		if ($this->db->affected_rows() == 0) 
		{
			return FALSE;
		}
		
		// Delete any cache
		$this->cache->delete('load_addons');
		
		$module_id = $this->db->insert_id();
		
		return $this->init_module($module_directory, 'install');
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Get all active add-ons
	 *
	 * @return 	array
	 * @access 	private
	 */
 	private function _get_addons()
 	{
		if ( ! empty ($this->_active_addons))
		{
			return $this->_active_addons;
		}
		
		$this->db->from('modules')
				->order_by('module_display_name', 'ASC');
		
		$query = $this->db->get();
		
		if ($query->num_rows() == 0) 
		{
			return FALSE;
		}
 		
		$this->_active_addons = $query->result_array();
		return $this->_active_addons;
 	}

	// ------------------------------------------------------------------------
	
	/**
	 * Get a single module
	 *
	 * @param 	int
	 * @return 	array
	 */
	public function get_module($module_id)
	{
		$module_id = (int) $module_id;
		
		$this->db->from('modules')->where('module_id', $module_id);
		
		$query = $this->db->get();
		
		if ($query->num_rows() == 0) 
		{
			return FALSE;
		}
		
		return $query->row_array(); 
 	}

	
	// ------------------------------------------------------------------------
	
	/**
	 * Show A Module read-me page.
	 *
	 * @param 	string
	 * @return 	mixed
	 * @access 	public
	 */
	public function show_docs($module_name)
 	{
		$module_name = (string) $module_name;
		
		if ( ! file_exists($this->_addons_dir.$module_name.'/'.$module_name.'_readme.txt'))
		{
			return FALSE;
		}
		
		return $this->_addons_dir . $module_name.'/'.$module_name.'_readme.txt';
 	}

	// ------------------------------------------------------------------------
	
	/**
	 * Load an add-on init file
	 *
	 * @param	string
	 * @return 	bool
	 */
	private function _load_init($module_name)
	{
		$module_name = (string) $module_name;
		
		$this->load->library('addons/init');
		
		if ( ! file_exists($this->_addons_dir . $module_name.'/'.$module_name.'_init.php'))
		{
			return FALSE;
		}
		
		require_once($this->_addons_dir . $module_name.'/'.$module_name.'_init.php');
		
		return TRUE;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Install an add-on
	 *
	 * @param	string
	 * @param	string
	 * @return 	mixed
	 * @uses	_load_init
	 **/ 
	public function init_module($module_name, $action = '', $msg = '')
	{	
		$this->_load_init($module_name);
		
		$class = ucfirst($module_name).'_init';
		
		if (class_exists($class)) 
		{
			$init_class = new $class($this);
		}
		
		if ($action == 'install')
		{
			if (method_exists($class, 'install'))
			{
				$msg = $init_class->install();
			}
		}
		elseif ($action == 'uninstall') 
		{
			if (method_exists($class, 'uninstall'))
			{
				$msg = $init_class->uninstall();
			}
			$this->db->delete('modules', array('module_name' => $module_name));
		}
		elseif ($action == 'deactivate') 
		{
			$this->db->delete('modules', array('module_name' => $module_name));
		}
		elseif ($action == 'upgrade') 
		{
			$data = $this->_get_config($module_name);
			
			if (method_exists($class, 'upgrade'))
			{
				$msg = $init_class->upgrade($data['module']['version']);
			}
			$module_data = array(
				'module_name' 			=> $data['module_name'], 
				'module_display_name' 	=> $data['module_display_name'], 
				'module_description' 	=> $data['module_description'], 
				'module_directory' 		=> $data['module_name'], 
				'module_version' 		=> $data['module_version'], 
				'module_order'	 		=> $data['module_order'], 
				'module_active' 		=> 'yes'
			);
			$this->db->where('module_name', $module_name);
			$this->db->update('modules', $module_data);
		}
		
		$this->events->trigger('init_addon');
		
		// Delete any cache
		$this->cache->delete('load_addons');
		
		if ($msg != '')
		{
			return $msg;
		}
		
		return TRUE;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Try to remove the add-on directory
	 *
	 * @param	string
	 * @return	bool
	 */
	public function remove_dir($module_directory)
	{
		if ( ! $this->exists($module_directory))
		{
			return FALSE;
		}
		
		$this->init_module($module_directory, 'uninstall');
		
		$opendir = opendir($this->_addons_dir .'/'. $module_directory);
		while (FALSE !== ($module = readdir($opendir)))
		{
			if ($module != '.' && $module != '..')
			{
				if ( ! unlink($this->_addons_dir .'/'. $module_directory .'/'. $module))
				{
					break; // can not unlink.
				}
			}
		}
		closedir($opendir);
		
		if (@rmdir($this->_addons_dir .'/'. $module_directory))
		{
			return TRUE;
		}
		
		return FALSE;
	}
}
/* End of file addons_model.php */
/* Location: ./upload/includes/68kb/modules/addons/models/addons_model.php */ 