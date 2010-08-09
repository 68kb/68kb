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
 * User Groups Helper
 *
 *
 * @subpackage	Helpers
 *
 */
if ( ! function_exists('get_group_by_id'))
{
	function get_group_by_id($group_id)
	{
		$CI =& get_instance();
		
		$CI->db->select('group_name')
				->from('user_groups')
				->where('group_id', $group_id);
				
		$query = $CI->db->get();
		
		
		if ($query->num_rows() != 1)
		{
			return FALSE;
		}
		
		$data = $query->row_array();
		
		$query->free_result();
		
		return $data['group_name'];
	}
}
/* End of file usergroups_helper.php */
/* Location: ./upload/includes/68kb/modules/usergroups/helpers/usergroups_helper.php */ 