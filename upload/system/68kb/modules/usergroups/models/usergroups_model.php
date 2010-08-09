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
 * User groups Model
 *
 * @subpackage	Models
 * @link		http://68kb.com/user_guide/
 * 
 */
class Usergroups_model extends CI_Model
{
	/**
	 * Constructor
	 *
	 * @return 	void
	 */
	public function __construct() 
	{
		parent::__construct();
		log_message('debug', 'User Groups Model Initialized');
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Get a list of all groups
	* 
	* @return	mixed 	The array on success, FALSE on failure
	*/
	public function get_groups()
	{
		$this->db->from('user_groups')
				->order_by('group_id', 'ASC'); 
				
		$query = $this->db->get();
		
		if ($query->num_rows() == 0) 
		{
			return FALSE;
		}
		
		$row = $query->result_array();
		
		$query->free_result();
		
		return $row;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Get a group
	* 
	* @return	mixed 	The array on success, FALSE on failure
	*/
	public function get_group($group_id)
	{
		$this->db->from('user_groups')
				->where('group_id', $group_id); 
				
		$query = $this->db->get();
		
		if ($query->num_rows() == 0) 
		{
			return FALSE;
		}
		
		$row = $query->row_array();
		
		$query->free_result();
		
		return $row;
	}
	
	// ------------------------------------------------------------------------
	
	/**
 	* Add User
 	* 
 	* @param	array
 	* @return	mixed
 	*/
	public function add_group($data)
	{
		$this->db->insert('user_groups', $data);
		
		if ($this->db->affected_rows() == 0) 
		{
			return FALSE;
		} 
		
		$group_id = $this->db->insert_id();
		
		$this->events->trigger('usergroups_model/add_group', $group_id);
		
		return $user_id;
	}
	
	// ------------------------------------------------------------------------
	
	/**
 	* Edit Group
 	* 
 	* @param	int
 	* @param	array
 	* @return	bool
 	*/
	public function edit_group($group_id, $data)
	{
		$group_id = (int) $group_id;
		
		$this->db->where('group_id', $group_id);
		$this->db->update('user_groups', $data);
		
		
		if ($this->db->affected_rows() == 0) 
		{
			return FALSE;
		} 
		
		$this->events->trigger('usergroups_model/edit_group', $group_id);
		
		return TRUE;
	}
	
	// ------------------------------------------------------------------------
		
	/**
	* Delete Group
	* 
	* @param	int 	The id to delete.
	* @return	bool	TRUE on success.
	*/
	public function delete_group($group_id)
	{
		$group_id = (int) $group_id;
		
		$this->db->delete('user_groups', array('group_id' => $group_id)); 
		
		if ($this->db->affected_rows() == 0) 
		{
			return FALSE;
		} 
		
		$this->events->trigger('usergroups_model/delete_group', $group_id);
		
		return TRUE;
	}
}
/* End of file usergroups_model.php */
/* Location: ./upload/includes/68kb/modules/usergroups/models/usergroups_model.php */ 