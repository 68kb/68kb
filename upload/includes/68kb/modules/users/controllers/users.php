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
 * Users Controller
 *
 *
 * @subpackage	Controllers
 * @link		http://68kb.com/user_guide/
 *
 */
class Users extends Front_Controller 
{
	public function __construct()
	{
		parent::__construct();
		log_message('debug', 'Users Controller Initialized');
		$this->load->model('users_model');
		$this->load->helper(array('cookie', 'form', 'date'));
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Show the user over view page.
	 */
	public function index()
	{
		if ( ! $this->users_auth->logged_in())
		{
			redirect('users/login');
		}
		
		redirect('users/account');
	}
	
	// ------------------------------------------------------------------------
	
	public function account()
	{
		if ( ! $this->users_auth->logged_in())
		{
			redirect('users/login');
		}
		$data['user'] = $this->users_model->get_user($this->session->userdata('user_id'));
		
		$this->template->title(lang('lang_my_account'));
		
		$this->template->set_breadcrumb(lang('lang_my_account'), 'users/account');
		
		$this->template->build('home', $data);
	}
	
	// ------------------------------------------------------------------------
	
	public function profile($user = '')
	{
		if ($user == '')
		{
			redirect('home');
		}
		
		$data['user'] = $this->users_model->get_user($user);
		
		$this->template->title(lang('lang_user_profile'));
		$this->template->set_breadcrumb($data['user']['user_username'], '');
		$this->template->set_breadcrumb(lang('lang_user_profile'), '');
		
		// Get any viewable fields
		$fields = FALSE;
		if ($this->events->active('fields'))
		{
			$this->load->model('fields/fields_model');
			$fields = $this->fields_model->get_fields('users');
		}
		
		$extra = array();
		$i = 0;
		
		if ($fields)
		{
			foreach ($fields as $row) 
			{
				$name = 'extra_field_'.$row['field_internal'];
				if ($data['user'][$name] != '')
				{
					$extra[$i]['name'] = $row['field_name'];
					if ($row['field_type'] == 'date')
					{
						$extra[$i]['value'] = date($this->config->item('short_date_format'), $data['user'][$name]);
					}
					else
					{
						$extra[$i]['value'] = $data['user'][$name];
					}
				}
				$i++;
			}
		}
		

		$data['extra'] = $extra;
		
		$this->template->build('profile', $data);
	}
	
	// ------------------------------------------------------------------------
	
	public function listings()
	{
		if ( ! $this->users_auth->logged_in())
		{
			redirect('users/login');
		}
		
		$user_id = (int) $this->session->userdata('user_id');
		
		$this->load->model('listings/listings_model');
		
		$this->load->library('pagination');
		
		$config['per_page'] = $this->settings->get_setting('site_max_search');
		$config['num_links'] = 5;
		
		$search_options['listing_owner_id'] = $user_id;
		
		$config['total_rows'] = $this->listings_model->get_search_results($search_options, 'listing_added', 'desc', 0, 0, TRUE, TRUE, FALSE);

		$data['paging'] = $this->pagination->get_pagination($config['total_rows'], $config['per_page']);
		$offset = $this->pagination->offset;
		
		$data['listings'] = $this->listings_model->get_search_results($search_options, 'listing_added', 'desc', $offset, $config['per_page'], FALSE, TRUE, FALSE);
		
		$data['user'] = $this->users_model->get_user($this->session->userdata('user_id'));
		
		$this->template->title(lang('lang_my_listings'));
		
		$this->template->set_breadcrumb(lang('lang_my_account'), 'users/account');
		$this->template->set_breadcrumb(lang('lang_my_listings'), 'users/listings');
		
		$this->template->build('listings', $data);
	}
	
	public function orders($order_id = '')
	{
		if ( ! $this->users_auth->logged_in())
		{
			redirect('users/login');
		}
		
		$user_id = (int) $this->session->userdata('user_id');
		
		$this->load->model('orders/orders_model');
		$this->load->model('cart/cart_model');
		$this->load->model('listings/listings_model');
		
		$data['user'] = $this->users_model->get_user($this->session->userdata('user_id'));
		
		$this->template->title(lang('lang_order_history'));
		
		$this->template->set_breadcrumb(lang('lang_my_account'), 'users/account');
		$this->template->set_breadcrumb(lang('lang_order_history'), 'users/orders');
		
		if (is_numeric($order_id))
		{
			$data['cart'] = $this->cart_model->get_completed_cart($order_id);

			// Get a fresh order with new status
			$data['order'] = $this->orders_model->get_order_by_id($order_id);
		}
		else
		{
			$this->load->library('pagination');

			$config['per_page'] = $this->settings->get_setting('site_max_search');
			$config['num_links'] = 5;

			$search_options['listing_owner_id'] = $user_id;

			$config['total_rows'] = $this->orders_model->get_users_orders($user_id, TRUE);

			$data['paging'] = $this->pagination->get_pagination($config['total_rows'], $config['per_page']);
			$offset = $this->pagination->offset;

			$data['orders'] = $this->orders_model->get_users_orders($user_id);
		}
		
		$this->template->build('orders', $data);
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * User Login
	 */
	public function login()
	{
		$this->template->title(lang('lang_login'));
		$this->template->set_breadcrumb(lang('lang_login'), '');
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('username', 'lang:lang_username', 'required');
		$this->form_validation->set_rules('password', 'lang:lang_password', 'required');
		$this->form_validation->set_error_delimiters('<p class="error">', '</p>');
		$data['no_cache'] = TRUE;

		if ($this->form_validation->run() == false)
		{
			$this->template->build('login', $data);
		}
		else
		{
			$username = $this->input->post('username');
			$password = $this->input->post('password');
			$remember = $this->input->post('remember');
			
			$login = $this->users_auth->login($username, $password, $remember);
			if ($login !== TRUE)
			{
				$data['error'] = $login;
				$this->template->build('login', $data);
			}
			else
			{
				$this->users_auth->redirect();
			}
	    }
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Log the user out.
	 */
	public function logout()
	{
		$this->users_auth->logout();
		redirect('users/login');
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Allow the user to register
	 */
	public function account_modify()
	{
		if ( ! $this->users_auth->logged_in())
		{
			redirect('users/login');
		}
		
		$this->template->title(lang('lang_modify_my_account'));
		
		$this->template->set_breadcrumb(lang('lang_my_account'), 'users/account');
		$this->template->set_breadcrumb(lang('lang_modify_my_account'), 'users/account_modify');
		
		$this->template->set_metadata('stylesheet', base_url() . 'themes/cp/css/smoothness/jquery-ui.css', 'link');
		$this->template->set_metadata('js', 'js/jquery-ui.min.js', 'js_include');
		
		$data['fields'] = '';
		if ($this->events->active('fields'))
		{
			$this->load->model('fields/fields_model');
			$data['fields'] = $this->fields_model->get_fields('users');
		}
		
		$data['user'] = $this->users_model->get_user($this->session->userdata('user_id'));
		
		$this->load->helper(array('form', 'url', 'html', 'date'));
		
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('user_email', 'lang:lang_email', 'required|valid_email|unique_email');
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
			$this->template->build('modify', $data);
		}
		else
		{
			$password = $this->input->post('user_password', TRUE);
			
			$update = array(
				'user_email' 		=> $this->input->post('user_email', TRUE),
				'user_password' 	=> $password
			);
			
			// Lets get the extra fields 
			$fields_array = array();
			
			if (isset($data['fields']))
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
			
			$user_id = $this->users_model->edit_user($data['user']['user_id'], $update);
			
			$this->session->set_flashdata('msg', lang('lang_settings_saved'));
			
			redirect('users/account_modify');
		}
	}
	// ------------------------------------------------------------------------
	
	/**
	 * Allow the user to register
	 */
	public function register()
	{
		$this->template->title(lang('lang_register'));
		$this->template->set_breadcrumb(lang('lang_register'), '');
		
		$this->template->set_metadata('stylesheet', base_url() . 'themes/cp/css/smoothness/jquery-ui.css', 'link');
		$this->template->set_metadata('js', 'js/jquery-ui.min.js', 'js_include');
		
		$data['fields'] = '';
		if ($this->events->active('fields'))
		{
			$this->load->model('fields/fields_model');
			$data['fields'] = $this->fields_model->get_fields('users');
		}
		
		$this->load->helper(array('form', 'url', 'html', 'date'));
		
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('user_username', 'lang:lang_username', 'required|alpha_numeric|callback_username_check');
		$this->form_validation->set_rules('user_email', 'lang:lang_email', 'required|valid_email|callback_unique_email');
		$this->form_validation->set_rules('user_password', 'lang:lang_password', 'required');
		$this->form_validation->set_rules('user_password_confirm', 'lang:lang_password_confirm', 'required|matches[user_password]');
		$this->form_validation->set_rules('user_first_name', 'lang:lang_first_name', '');
		$this->form_validation->set_rules('user_last_name', 'lang:lang_last_name', '');
		
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
			$this->template->build('register', $data);
		}
		else
		{
			// @todo - Add setting for email validation.
			
			$username = $this->input->post('user_username', TRUE);
			$password = $this->input->post('user_password', TRUE);
			
			$update = array(
				'user_username' 	=> $username,
				'user_email' 		=> $this->input->post('user_email', TRUE),
				'user_group' 		=> 2,
				'user_password' 	=> $password
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
			
			$this->session->set_userdata('goto', 'users');
			
			// try to auto log them in.
			$this->users_auth->login($username, $password);
			
			redirect('users/account');
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	* Validate the username
	* 
	* @access	public
	* @return	string
	*/
	public function username_check($username)
	{
		if ($this->events->active_hook('users_controller/username_check'))
		{
			return $this->events->trigger('users_controller/username_check', $username);
		}
		
		$this->db->select('user_username')
					->from('users')
					->where('user_username', $username);
					
		$query = $this->db->get();
		
		if ($query->num_rows() > 0)
		{
			$this->form_validation->set_message('username_check', lang('lang_username_in_use'));
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
	 * Field Check
	 *
	 * Can be used by addons with form validation.
	 */
	public function field_check($field)
	{
		if ( ! $this->events->trigger('users_controller/field_check', $field))
		{
			return FALSE;
		}
		return TRUE;
	}
	
	// ------------------------------------------------------------------------

	/**
	* Validate the email
	* 
	* @access	public
	* @return	string
	*/
	public function unique_email($email)
	{
		if ($this->events->active_hook('users_controller/unique_email'))
		{
			return $this->events->trigger('users_controller/unique_email', $email);
		}
		
		$this->db->select('user_username')
					->from('users')
					->where('user_email', $email);
					
		$query = $this->db->get();

		if ($query->num_rows() > 0)
		{
			$this->form_validation->set_message('unique_email', lang('lang_email_in_use'));
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Forgot password form.
	 */
	public function forgot()
	{
		// Simple flood check.
		if ($this->session->userdata('last_check'))
		{
			$last_check = time() - $this->session->userdata('last_check');
			if($last_check < 5)
			{
				sleep(2);
			}
		}
		
		$this->session->set_userdata('last_check', time());
		
		$this->template->title(lang('lang_forgot_pass'));
		$this->template->set_breadcrumb(lang('lang_forgot_pass'), '');
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('email', 'lang:lang_email', 'required|valid_email|callback_email_check');
		
		$data['no_cache'] = TRUE;

		if ($this->form_validation->run() == FALSE)
		{
			$this->template->build('forgot', $data);
		}
		else
		{
			$user_email = $this->input->post('email', TRUE);
			
			$this->db->select('user_id')
						->from('users')
						->where('user_email', $user_email);

			$query = $this->db->get();
			
			$data = $query->row_array();
			
			$this->users_model->send_new_password($data['user_id']);
			
			$data['message'] = lang('lang_forgot_pass_sent_key');
			
			$this->template->build('forgot', $data);
	    }
	}
	
	// ------------------------------------------------------------------------

	/**
	* Validate the username
	* 
	* @access	public
	* @return	string
	*/
	public function email_check($user_email)
	{
		$this->db->select('user_email')
					->from('users')
					->where('user_email', $user_email);
					
		$query = $this->db->get();
		
		if ($query->num_rows() != 1)
		{
			$this->form_validation->set_message('email_check', lang('lang_forgot_pass_error'));
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Reset the password
	 *
	 * @param	string - Hash
	 */
	public function reset($hash = '')
	{
		if (strlen($hash) != 22)
		{
			$data['message'] = lang('lang_forgot_invalid_key');
		}
		
		if ($this->users_model->reset_password($hash))
		{
			$data['message'] = lang('lang_forgot_pass_sent');
		}
		else
		{
			$data['message'] = lang('lang_forgot_pass_sent');
		}
		$this->template->build('forgot', $data);
	}
}
/* End of file users.php */
/* Location: ./upload/includes/68kb/modules/users/controllers/users.php */ 