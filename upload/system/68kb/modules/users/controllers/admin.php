<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * iClassEngine
 *
 * THIS IS COPYRIGHTED SOFTWARE
 * PLEASE READ THE LICENSE AGREEMENT
 * http://iclassengine.com/user_guide/policies/license
 *
 * @package		iClassEngine
 * @author		ICE Dev Team
 * @copyright	Copyright (c) 2010, 68 Designs, LLC
 * @license		http://iclassengine.com/user_guide/policies/license
 * @link		http://iclassengine.com
 * @since		Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * Admin Users Controller
 *
 * @subpackage	Controllers
 * @link		http://iclassengine.com/user_guide/admin/users.html
 *
 */
class Admin extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('users_model');
		$this->load->model('orders/orders_model');
		
		if ( ! $this->users_auth->check_role('can_manage_users'))
		{
			show_error(lang('not_authorized'));
		}
		
		$this->security->csrf_set_cookie();
	}
	
	// ------------------------------------------------------------------------
	
	/** 
	* Show table grid
	*/
	public function index()
	{
		$data['nav'] = 'users';
		
		$this->template->set_metadata('stylesheet', base_url() . 'themes/cp/css/smoothness/jquery-ui.css', 'link');
		$this->template->set_metadata('js', 'js/dataTables.min.js', 'js_include');
		
		$this->template->title = lang('lang_manage_users');
		
		$this->template->build('admin/users_grid', $data); 
	}
	
	// ------------------------------------------------------------------------
	
	public function add_note($user_id)
	{
		$user_id = (int) $user_id;
		
		$data['user_id'] = $user_id;
		$data['action'] = 'add';
		
		$this->load->helper(array('form', 'url', 'html', 'date'));
		
		$this->load->library('form_validation');
		
		$data['textarea'] = array(
			'name'		=> 'note',
			'id'		=> 'note',
			'value'		=> set_value('note'),
			'rows'   	=> '7',
			'cols'		=> '30',
			'style'		=> 'width:90%;',
			'class' 	=> 'inputtext'
		);
		
		
		$this->form_validation->set_rules('note', 'lang:lang_note', 'required');
		$this->form_validation->set_rules('note_important', 'lang:lang_note_important', 'required');
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->template->set_layout('');
			$this->template->build('admin/add_note', $data);
		}
		else
		{
			$insert['note'] = $this->input->post('note', TRUE);
			$insert['note_important'] = $this->input->post('note_important', TRUE);
			$insert['note_user_id'] = $user_id;
			$insert['note_date'] = time();
			$insert['note_added_by'] = $this->session->userdata('user_id');

			$this->db->insert('user_notes', $insert);

			$data['success'] = lang('lang_settings_saved');
			$this->template->set_layout('');
			$this->template->build('admin/add_note', $data);
			// redirect('/admin/users/edit/'.$user_id, 'refresh');
		}
	}
	
	// ------------------------------------------------------------------------
	
	public function edit_note($note_id)
	{
		$note_id = (int) $note_id;
		
		$data['note_id'] = $note_id;
		$data['action'] = 'edit';
		
		$this->db->from('user_notes')
					->where('note_id', $note_id);
		
		$query = $this->db->get();
		
		$data['note'] = $query->row_array();
		
		$this->load->helper(array('form', 'url', 'html', 'date'));
		
		$this->load->library('form_validation');
		
		$data['textarea'] = array(
			'name'		=> 'note',
			'id'		=> 'note',
			'value'		=> set_value('note', strip_tags($data['note']['note'])),
			'rows'   	=> '7',
			'cols'		=> '30',
			'style'		=> 'width:90%;',
			'class' 	=> 'inputtext'
		);
		
		
		$this->form_validation->set_rules('note', 'lang:lang_note', 'required');
		$this->form_validation->set_rules('note_important', 'lang:lang_note_important', 'required');
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->template->set_layout('');
			$this->template->build('admin/add_note', $data);
		}
		else
		{
			$insert['note'] = $this->input->post('note', TRUE);
			$insert['note_important'] = $this->input->post('note_important', TRUE);
			
			$this->db->where('note_id', $note_id);
			$this->db->update('user_notes', $insert);

			$data['success'] = lang('lang_settings_saved');
			$this->template->set_layout('');
			$this->template->build('admin/add_note', $data);
			
		}
	}
	
	// ------------------------------------------------------------------------
	
	public function delete_note($note_id)
	{
		$note_id = (int) $note_id;
		
		$this->db->from('user_notes')
					->where('note_id', $note_id);
		
		$query = $this->db->get();
		
		$note = $query->row_array();
		
		$this->db->delete('user_notes', array('note_id' => $note_id)); 
		
		$this->session->set_flashdata('msg', lang('lang_settings_saved'));
		redirect('admin/users/edit/'.$note['note_user_id']);
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Add User
	*
	*/
	public function add()
	{
		$data['nav'] = 'users';
		
		$this->template->title = lang('lang_add_user');
		
		$this->load->model('usergroups/usergroups_model');
		
		$this->template->set_metadata('stylesheet', base_url() . 'themes/cp/css/smoothness/jquery-ui.css', 'link');
		$this->template->set_metadata('js', 'js/jquery-ui.min.js', 'js_include');
		
		$data['fields'] = '';
		if ($this->events->active('fields'))
		{
			$this->load->model('fields/fields_model');
			$data['fields'] = $this->fields_model->get_fields('users');
		}

		$data['groups'] = $this->usergroups_model->get_groups();
		
		$this->load->helper(array('form', 'url', 'html', 'date'));
		
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('user_username', 'lang:lang_username', 'required|alpha_numeric|callback_username_check');
		$this->form_validation->set_rules('user_email', 'lang:lang_email', 'required|valid_email');
		$this->form_validation->set_rules('user_group', 'lang:lang_user_group', 'required|is_natural_no_zero');
		$this->form_validation->set_rules('user_password', 'lang:lang_password', 'required');
		$this->form_validation->set_rules('user_password_confirm', 'lang:lang_password_confirm', 'required|matches[user_password]');
		
		$this->events->trigger('users_controller/edit');
		
		if ($data['fields'] <> '')
		{
			foreach ($data['fields'] as $fields) // setup rules for extra fields
			{
				if ($fields['field_type'] == 'checkbox') // checkboxes return as arrays. Have to add []. 
				{
					$this->form_validation->set_rules($fields['field_internal'].'[]', $fields['field_name'], $fields['field_validation']);
				}
				else
				{
					$this->form_validation->set_rules($fields['field_internal'], $fields['field_name'], 'trim|'.$fields['field_validation']);
				}
			}
		}
		
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->template->build('admin/users_add', $data);
		}
		else
		{
			$update = array(
				'user_username' 	=> $this->input->post('user_username', TRUE),
				'user_email' 		=> $this->input->post('user_email', TRUE),
				'user_group' 		=> $this->input->post('user_group', TRUE),
				'user_password' 	=> $this->input->post('user_password', TRUE)
			);
			
			// Lets get the extra fields 
			$fields_array = array();
			
			if (isset($data['fields']) && ! empty($data['fields']))
			{
				foreach ($data['fields'] as $fields) 
				{
					$value =  $this->input->post($fields['field_internal'], TRUE);

					// Convert checkboxes to comma delimites.
					if (is_array($value)) 
					{
						$value = implode(",", $value);
					}

					// Format seconds
					if ($fields['field_type'] == 'date')
					{
						$value = $value .' 00:00:00';
						$value = human_to_unix($value);
					}

					$fields_array['extra_field_'.$fields['field_internal']] = $value;
				}

				// Merge the fields_array into the original update_data array
				$update = array_merge($update, $fields_array);
			}
			
			$user_id = $this->users_model->add_user($update);
			
			$this->session->set_flashdata('msg', lang('lang_settings_saved'));
			
			redirect('admin/users/edit/'.$user_id);
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Edit User
	*
	*/
	public function edit()
	{
		$data['nav'] = 'users';
		
		$this->template->title = lang('lang_manage_users');
		
		$this->load->model('usergroups/usergroups_model');
		
		$this->template->set_metadata('stylesheet', base_url() . 'themes/cp/css/smoothness/jquery-ui.css', 'link');
		$this->template->set_metadata('js', 'js/jquery-ui.min.js', 'js_include');
		
		$data['fields'] = '';
		if ($this->events->active('fields'))
		{
			$this->load->model('fields/fields_model');
			$data['fields'] = $this->fields_model->get_fields('users');
		}

		$data['id'] = (int) $this->uri->segment(4, 0);
		$data['row'] = $this->users_model->get_user($data['id']);
		$data['notes'] = $this->users_model->user_notes($data['id']);
		$data['groups'] = $this->usergroups_model->get_groups();
		$data['active_listings'] = $this->users_model->users_listings($data['id']);
		$data['total_order_amount'] = $this->orders_model->user_total_spent($data['id']);
		$data['total_orders'] = $this->orders_model->get_users_orders($data['id'], TRUE);
		
		$this->load->helper(array('form', 'url', 'html', 'date'));
		
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('user_username', 'lang:lang_username', 'required|alpha_numeric|callback_username_check['.$data['id'].']');
		$this->form_validation->set_rules('user_email', 'lang:lang_email', 'required|valid_email');
		$this->form_validation->set_rules('user_group', 'lang:lang_user_group', 'required|is_natural_no_zero|callback_usergroup_check['.$data['id'].']');
		$this->form_validation->set_rules('user_password', 'lang:lang_password', '');
		$this->form_validation->set_rules('user_password_confirm', 'lang:lang_password_confirm', 'callback_password_check|matches[user_password]');
		
		$this->events->trigger('users_controller/edit');
		
		if ($data['fields'] <> '')
		{
			foreach ($data['fields'] as $fields) // setup rules for extra fields
			{
				if ($fields['field_type'] == 'checkbox') // checkboxes return as arrays. Have to add []. 
				{
					$this->form_validation->set_rules($fields['field_internal'].'[]', $fields['field_name'], $fields['field_validation']);
				}
				else
				{
					$this->form_validation->set_rules($fields['field_internal'], $fields['field_name'], 'trim|'.$fields['field_validation']);
				}
			}
		}
		
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->template->build('admin/users_edit', $data);
		}
		else
		{
			$user_id = (int) $this->input->post('user_id', TRUE);
			
			$update = array(
				'user_username' 	=> $this->input->post('user_username', TRUE),
				'user_email' 		=> $this->input->post('user_email', TRUE),
				'user_group' 		=> $this->input->post('user_group', TRUE),
				'user_password' 	=> $this->input->post('user_password', TRUE)
			);
			
			// Lets get the extra fields 
			
			$fields_array = array();
			
			if (isset($data['fields']) && ! empty($data['fields']))
			{
				foreach ($data['fields'] as $fields) 
				{
					$value =  $this->input->post($fields['field_internal'], TRUE);

					// Convert checkboxes to comma delimites.
					if (is_array($value)) 
					{
						$value = implode(",", $value);
					}

					// Format seconds
					if ($fields['field_type'] == 'date')
					{
						$value = $value .' 00:00:00';
						$value = human_to_unix($value);
					}

					$fields_array['extra_field_'.$fields['field_internal']] = $value;
				}

				// Merge the fields_array into the original update_data array
				$update = array_merge($update, $fields_array);
			}
			
			
			$field_id = $this->users_model->edit_user($user_id, $update);
			
			$this->session->set_flashdata('msg', lang('lang_settings_saved'));
			
			redirect('admin/users/edit/'.$user_id);
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	* Validate the username
	* 
	* @access	public
	* @return	string
	*/
	public function username_check($username, $id)
	{
		$this->db->select('user_username')
					->from('users')
					->where('user_username', $username)
					->where('user_id !=', $id);
					
		$query = $this->db->get();
		
		if ($query->num_rows() > 0)
		{
			$this->form_validation->set_message('username_check', 'The username is already in use.');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	* Validate the password
	* 
	* @access	public
	* @return	string
	*/
	public function password_check($pass)
	{
		if ($this->input->post('user_password') && ! $this->input->post('user_password_confirm'))
		{
			$this->form_validation->set_message('password_check', lang('lang_password_confirm'));
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	
	// ------------------------------------------------------------------------
	
	/**
	* Check to see if this is the only admin account
	*/
	public function usergroup_check($user_group, $id)
	{
		if ($user_group > 1)
		{
			$this->db->select('user_group')->from('users')->where('user_id', $id);
			$query = $this->db->get();
			if ($query->num_rows() > 0)
			{
				$row = $query->row();
				if ($row->user_group == 1)
				{
					$this->db->where('user_group', '1'); 
					if ($this->db->count_all_results('users') == 1)
					{
						$this->form_validation->set_message('usergroup_check', lang('lang_error_user_group'));
						return FALSE;
					}
				}
			}
			return TRUE;
		}
		return TRUE;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Delete users listings
	 */
	public function delete_user_items($user_id)
	{
		if ( ! ctype_digit($user_id)) 
		{
			$this->session->set_flashdata('msg', lang('lang_error_record'));
			redirect('admin/users/');
		}
		
		$this->load->model('listings/listings_model');
		$this->db->select('listing_id')->from('listings')->where('listing_owner_id', $user_id);
		$query = $this->db->get();
		
		$rows = $query->num_rows();
		
		if ($rows > 0) 
		{
			$data = $query->result_array();
			foreach ($data AS $row)
			{
				$this->listings_model->delete_listing($row['listing_id']);
			}
		}
		
		$query->free_result();
		
		$this->session->set_flashdata('msg', lang('lang_settings_saved'));
		redirect('admin/users/edit/'.$user_id);
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Search Users
	*
	* This is used by the ajax searching
	* 
	* @access	public
	* @return	string
	*/
	public function search($output = '')
	{
		if ($this->input->post('searchword'))
		{
			$q = $this->input->post('searchword');
			$this->db->from('users')->or_like('user_username', $q);
			$query = $this->db->get();
			
			if ($query->num_rows() == 0)
			{
				$output = '';
			}
			
			foreach ($query->result() as $row)
			{
				$output .= '
					<div class="display_box" align="left">
					<img src="'.gravatar($row->user_email, 'PG', 25).'" style="width:25px; float:left; margin-right:6px" />
					<a href="javascript:void(0);" onclick="add_user(\''.$row->user_username.'\')">'.$row->user_username.'</a><br/>
					<span style="font-size:9px; color:#999999">'.$row->user_email.'</span></div>
				';
			}
		}
		
		if ($output == '')
		{
			$output = '
				<div class="display_box" align="left">
					'.lang('no_results').'
				</div>
			';
		}
		
		echo $output;
	}

	// ------------------------------------------------------------------------
	
	/**
	* Reset a user api key
	*
	* @param	int
	* @return 	void
	*/
	public function reset_api($user_id)
	{
		$api_key = $this->users_model->generate_api_key();
		
		$user_id = (int) $user_id;
		
		if ($this->users_model->update_api_key($api_key, $user_id))
		{
			$this->session->set_flashdata('msg', lang('lang_settings_saved'));
		}
		else
		{
			$this->session->set_flashdata('msg', lang('lang_settings_saved'));
		}
		
		redirect('admin/users/edit/'.$user_id);
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Grid
	*
	* This is used by the data table js. 
	* 
	* @access	public
	* @return	string
	*/
	public function grid()
	{
		$iTotal = $this->db->count_all('users');
		
		$this->db->start_cache();
		
		//$this->db->select('user_id, user_ip, user_first_name, user_last_name, user_email, user_username, user_group, user_join_date, user_last_login');
		$this->db->from('users')
				->join('user_groups', 'user_group = group_id', 'inner');
		
		/* Searching */
		if($this->input->post('sSearch') != '')
		{
			$q = $this->input->post('sSearch', TRUE);
			$this->db->orlike('user_first_name', $q);
			$this->db->orlike('user_last_name', $q);
			$this->db->orlike('user_email', $q);
			$this->db->orlike('user_username', $q);
		}
		
		/* Sorting */
		if ($this->input->post('iSortCol_0'))
		{
			$sort_col = $this->input->post('iSortCol_0');
			for($i=0; $i < $sort_col; $i++)
			{
				$this->db->order_by($this->_column_to_field($this->input->post('iSortCol_'.$i)), $this->input->post('iSortDir_'.$i));
			}
		}
		else
		{
			$this->db->order_by('user_last_login', 'desc');
		}
		
		$this->db->stop_cache();
		
		$iFilteredTotal = $this->db->count_all_results();
		
		$this->db->start_cache();
		
		/* Limit */
		if ($this->input->post('iDisplayStart') && $this->input->post('iDisplayLength') != '-1' )
		{
			$this->db->limit($this->input->post('iDisplayLength'), $this->input->post('iDisplayStart'));
		}
		elseif($this->input->post('iDisplayLength'))
		{
			$this->db->limit($this->input->post('iDisplayLength'));
		}
		
		$query = $this->db->get();
		
		$output = '{';
		$output .= '"sEcho": '.$this->input->post('sEcho').', ';
		$output .= '"iTotalRecords": '.$iTotal.', ';
		$output .= '"iTotalDisplayRecords": '.$iFilteredTotal.', ';
		$output .= '"aaData": [ ';
		
		foreach ($query->result() as $row)
		{
			$gravatar = '<img width="32" height="32" src="'.gravatar($row->user_email, 'PG', 32).'" class="gravatar" alt="'.$row->user_username.'" />';

			$user_username = $gravatar .' <strong><a href="'.site_url('admin/users/edit/'.$row->user_id).'">'.$row->user_username.'</a></strong> <div class="ip"> '. $row->user_ip .'</div>'; 
			
			$output .= "[";
			$output .= '"'.addslashes($user_username).'",';
			$output .= '"'.addslashes(format_date($row->user_join_date)).'",';
			$output .= '"'.addslashes(format_date($row->user_last_login)).'",';
			$output .= '"'.addslashes($row->group_name).'"';
			$output .= "],";
		}
		
		$output = substr_replace( $output, "", -1 );
		$output .= '] }';

		echo $output;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Relate column to field
	*
	* This is used by the data table js. 
	* 
	* @param	string
	* @return	string
	*/
	private function _column_to_field($i)
	{
		if ($i == 0)
		{
			return "user_username";
		}
		elseif ($i == 1)
		{
			return "user_join_date";
		}
		elseif ($i == 2)
		{
			return "user_last_login";
		}
		elseif ($i == 3)
		{
			return 'group_name';
		}
		elseif ($i == 5)
		{
			return 'listing_modified';
		}
		elseif ($i == 6)
		{
			return "listing_price";
		}
	}
	
}

/* End of file admin.php */
/* Location: ./upload/includes/iclassengine/modules/users/controllers/admin.php */