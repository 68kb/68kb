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
 * Admin Users Controller
 *
 * @subpackage	Controllers
 * @link		http://68kb.com/user_guide/admin/users.html
 *
 */
class Admin extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();
		
		log_message('debug', 'User_groups Controller Initialized');
		
		$this->load->model('usergroups_model');
		
		if ( ! $this->users_auth->check_role('can_manage_users'))
		{
			show_error(lang('not_authorized'));
		}
	}
	
	// ------------------------------------------------------------------------
	
	/** 
	* Show table grid
	*/
	public function index()
	{
		$data['nav'] = 'users';
		
		$this->template->title(lang('lang_user_groups'));
		
		$this->db->from('user_groups')->order_by('group_id', 'ASC'); 
		
		$query = $this->db->get();
		
		if ($query->num_rows() > 0) 
		{
			$row = $query->result_array();
			
			foreach ($row as $k => $group)
			{
				$row[$k]['group_members'] = $this->_get_group_members($group['group_id']);
			}
			
			$query->free_result();
		}
		else
		{
			$group = '';
		}
		
		$data['options'] = $row;
		
		$this->events->trigger('admin/usergroups/index', $data);
		
		$this->template->build('admin/usergroups', $data);
	}
	
	// ------------------------------------------------------------------------
	
	public function add()
	{
		$data['nav'] = 'users';
		
		$this->template->title(lang('lang_add_user_group'));
		
		$group = $this->usergroups_model->get_group(2);
		
		// Assign all the settings to the template.
		foreach ($group as $key => $value) 
		{
			$data[$key] = $value;
		}
		
		$this->load->model('usergroups/usergroups_model');
		
		$this->load->helper(array('form', 'url', 'html', 'date'));
		
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('group_name', 'lang:lang_title', 'required|');
		$this->form_validation->set_rules('group_description', 'lang:lang_description', 'required');
		
		$this->events->trigger('usergroups_controller/add');
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->template->build('admin/add', $data);
		}
		else
		{
			$insert = array(
				'group_name' 					=> $this->input->post('group_name', TRUE),
				'group_description' 			=> $this->input->post('group_description', TRUE),
				'can_view_site' 				=> $this->input->post('can_view_site', TRUE),
				'can_access_admin' 				=> $this->input->post('can_access_admin', TRUE),
				'can_manage_articles' 			=> $this->input->post('can_manage_articles', TRUE),
				'can_delete_articles' 			=> $this->input->post('can_delete_articles', TRUE),
				'can_manage_users' 				=> $this->input->post('can_manage_users', TRUE),
				'can_manage_categories' 		=> $this->input->post('can_manage_categories', TRUE),
				'can_delete_categories' 		=> $this->input->post('can_delete_categories', TRUE),
				'can_manage_settings' 			=> $this->input->post('can_manage_settings', TRUE),
				'can_manage_utilities' 			=> $this->input->post('can_manage_utilities', TRUE),
				'can_manage_themes' 			=> $this->input->post('can_manage_themes', TRUE),
				'can_manage_modules' 			=> $this->input->post('can_manage_modules', TRUE)
			);
			
			$user_group = $this->usergroups_model->add_group($insert);
			
			$this->session->set_flashdata('msg', lang('lang_settings_saved'));
			
			redirect('admin/usergroups/');
		}
	}
	
	// ------------------------------------------------------------------------
	
	public function edit($group_id = '')
	{
		if ( ! is_numeric($group_id))
		{
			redirect('admin/usergroups');
		}
		
		$group = $this->usergroups_model->get_group($group_id);
		
		// Assign all the settings to the template.
		foreach ($group as $key => $value) 
		{
			$data[$key] = $value;
		}
		
		$data['nav'] = 'users';
		
		$this->template->title(lang('lang_user_groups'));
		
		$this->load->model('usergroups/usergroups_model');
		
		$this->load->helper(array('form', 'url', 'html', 'date'));
		
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('group_name', 'lang:lang_title', 'required|');
		$this->form_validation->set_rules('group_description', 'lang:lang_description', 'required');
		
		$this->events->trigger('usergroups_controller/edit');
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->template->build('admin/edit', $data);
		}
		else
		{
			if ($group_id == 1) // Super admins can do everything
			{
				$insert = array(
					'group_name' 					=> $this->input->post('group_name', TRUE),
					'group_description' 			=> $this->input->post('group_description', TRUE),
				);
			}
			else
			{
				$insert = array(
					'group_name' 					=> $this->input->post('group_name', TRUE),
					'group_description' 			=> $this->input->post('group_description', TRUE),
					'can_view_site' 				=> $this->input->post('can_view_site', TRUE),
					'can_access_admin' 				=> $this->input->post('can_access_admin', TRUE),
					'can_manage_articles' 			=> $this->input->post('can_manage_articles', TRUE),
					'can_delete_articles' 			=> $this->input->post('can_delete_articles', TRUE),
					'can_manage_users' 				=> $this->input->post('can_manage_users', TRUE),
					'can_manage_categories' 		=> $this->input->post('can_manage_categories', TRUE),
					'can_delete_categories' 		=> $this->input->post('can_delete_categories', TRUE),
					'can_manage_settings' 			=> $this->input->post('can_manage_settings', TRUE),
					'can_manage_utilities' 			=> $this->input->post('can_manage_utilities', TRUE),
					'can_manage_themes' 			=> $this->input->post('can_manage_themes', TRUE),
					'can_manage_modules' 			=> $this->input->post('can_manage_modules', TRUE)
				);
			}
			
			
			$user_group = $this->usergroups_model->edit_group($group_id, $insert);
			
			$this->session->set_flashdata('msg', lang('lang_settings_saved'));
			
			redirect('admin/usergroups/edit/'.$group_id);
		}
	}
	
	// ------------------------------------------------------------------------
	
	public function delete($group_id = '')
	{
		if ( ! is_numeric($group_id))
		{
			redirect('admin/usergroups');
		}
		if ($this->_get_group_members($group_id) > 0)
		{
			show_error('This group has members. You cant delete it.');
		}
		else
		{
			$this->usergroups_model->delete_group($group_id);
			$this->session->set_flashdata('msg', lang('lang_settings_saved'));
			redirect('admin/usergroups');
		}
	}
	// ------------------------------------------------------------------------
	
	/**
	* Get the total member for a group
	* 
	* @return	int
	*/
	private function _get_group_members($group_id)
	{
		$this->db->where('user_group', $group_id); 
		return $this->db->count_all_results('users');
	}
}

/* End of file admin.php */
/* Location: ./upload/includes/68kb/modules/usergroups/controllers/admin.php */ 