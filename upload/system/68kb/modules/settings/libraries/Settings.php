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
 * Settings Library
 *
 * @subpackage	Libraries
 * @link		http://68kb.com/user_guide/
 *
 */
class Settings
{
	/**
	 * Global CI Object
	 */
	private $_ci;
	
	/**
	 * Settings array used to pass settings to template
	 *
	 * @access 	private
	 * @var 	array
	 */
	private $settings = array();
	
	/**
	 * Settings group array
	 *
	 * @access 	private
	 * @var 	array
	 */
	private $settings_group = array();
	
	// ------------------------------------------------------------------------
	
	/**
	 * Constructor assign CI instance
	 *
	 * @return 	void
	 */
	public function __construct() 
	{
		$this->_ci = CI_Base::get_instance();
		$this->_ci->benchmark->mark('get_settings_start');
		$this->get_settings();
		$this->_ci->benchmark->mark('get_settings_end');
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Get Settings
	 *
	 * Get all the auto loaded settings from the db.
	 *
	 * @return	array
	 */
	public function get_settings()
	{
		// If the array is not empty we already have them. 
		if ( ! empty ($this->settings))
		{
			return $this->settings;
		}
		
		$this->_ci->db->select('option_name,option_value')
					->from('settings')
					->where('auto_load', 'yes');
					
		$query = $this->_ci->db->get();
		
		if ($query->num_rows() == 0)
		{
			return FALSE;
		}
		
		foreach ($query->result() as $k=>$row)
		{
			$this->settings[$row->option_name] = $row->option_value;
		}
		
		return $this->settings;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Get Setting (Notice Singular)
	 *
	 * Used to pull out one specific setting from the settings table.
	 *
	 * Here is an example: 
	 * <code>
	 * <?php
	 * $this->settings->get_setting('site_name');
	 * ?>
	 * </code>
	 *
	 * @access	public
	 * @param	string
	 * @return	mixed
	 */
	public function get_setting($option_name)
	{
		if (isset($this->settings[$option_name]))
		{
			return $this->settings[$option_name];
		}
		
		$this->_ci->db->select('option_value')
					->from('settings')
					->where('option_name', $option_name);
					
		$query = $this->_ci->db->get();
		
		if ($query->num_rows() == 0)
		{
			return FALSE;
		}
		
		$row = $query->row();
		
		return $row->option_value;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Get Settings By Group
	 *
	 * Get all the settings from one group
	 * 
	 * @param	string
	 * @return	object
	 */
	public function get_settings_by_group($option_group = '')
	{
		if ($option_group == '')
		{
			return FALSE;
		}
		
		$this->_ci->db->select('option_name,option_value')
						->from('settings')
						->where('option_group', $option_group);
		
		$query = $this->_ci->db->get();
		
		if ($query->num_rows() == 0)
		{
			return FALSE;
		}
		
		foreach ($query->result() as $k=>$row)
		{
			$this->settings_group[$row->option_name] = $row->option_value;
		}
		
		return $this->settings_group;
	}
	
	// ------------------------------------------------------------------------
	
	/**
 	* Edit Setting
 	* 
 	* @param	string
 	* @param	string
 	* @return	bool
 	*/
	public function edit_setting($option_name, $option_value)
	{
		$data = array( 
			'option_value' => $option_value
		);
		$this->_ci->db->where('option_name', $option_name);
		$this->_ci->db->update('settings', $data);
		
		if ($this->_ci->db->affected_rows() == 0) 
		{
			return FALSE;
		} 
		
		return TRUE;
	}
	
	// ------------------------------------------------------------------------
	
	/**
 	* Delete Setting
 	* 
 	* @param	string
 	* @param	string
 	* @return	bool
 	*/
	public function delete_settings_by_group($option_group)
	{
		$this->_ci->db->delete('settings', array('option_group' => $option_group)); 
		
		if ($this->_ci->db->affected_rows() == 0) 
		{
			return FALSE;
		} 
		
		return TRUE;
	}
	
	// ------------------------------------------------------------------------
	
	/**
 	* Add Setting
 	* 
 	* @param	string
 	* @param	string
 	* @return	bool
 	*/
	public function add_setting($option_name, $option_value = '', $option_group = 'addon', $auto_load = 'no')
	{
		// Check and make sure it isn't already added.
		$this->_ci->db->select('option_value')
					->from('settings')
					->where('option_name', $option_name);
					
		$query = $this->_ci->db->get();
		
		if ($query->num_rows() > 0)
		{
			return FALSE;
		}
		
		// Now insert it
		$data = array( 
			'option_name' => $option_name,
			'option_value' => $option_value,
			'option_group' => $option_group,
			'auto_load' => $auto_load,
		);
		
		$this->_ci->db->insert('settings', $data);
		
		if ($this->_ci->db->affected_rows() == 0) 
		{
			return FALSE;
		} 
		
		return TRUE;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Parse Params
	 *
	 * Method to parse out params for helpers.
	 *
	 * @param	array params
	 * @param	array defaults
	 * @return 	array 
	 */
	public function parse_params($params = array(), $defaults = array())
	{
		// parse the params
		parse_str($params, $options);
		
		// now loop through and change the defaults
		foreach ($defaults as $key => $val)
		{
			if ( ! is_array($options))
			{
				if ( ! isset($$key) OR $$key == '')
				{
					$options[$key] = $val;
				}
			}
			else
			{	
				$options[$key] = ( ! isset($options[$key])) ? $val : $options[$key];
				$key = ( ! isset($options[$key])) ? $val : $options[$key];
			}
		}
		
		return $options;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Get params
	 *
	 * This is a helper used from the parser files to process a list of params
	 *
	 * @param	array - Params passed from view
	 * @param	array - Array of default params
	 * @return 	array 
	 */
	public function get_params($params = array(), $defaults = array())
	{
		// parse the params
		$options = $params;

		// now loop through and change the defaults
		foreach ($defaults as $key => $val)
		{
			if ( ! is_array($options))
			{
				if ( ! isset($$key) OR $$key == '')
				{
					$options[$key] = $val;
				}
			}
			else
			{	
				$options[$key] = ( ! isset($options[$key])) ? $val : $options[$key];
				$key = ( ! isset($options[$key])) ? $val : $options[$key];
			}
		}
		
		return $options;
	}
}

/* End of file Settings.php */
/* Location: ./upload/system/68kb/modules/settings/libraries/Settings.php */ 