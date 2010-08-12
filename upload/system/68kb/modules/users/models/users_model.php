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
 * Users Model
 *
 * @subpackage	Models
 * @link		http://68kb.com/user_guide/
 */
class Users_model extends CI_Model {
	
	/**
	 * Constructor
	 *
	 * @return void
	 **/
	public function __construct()
	{
		parent::__construct();
		log_message('debug', 'Users_model Initialized');
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Get user
	 *
	 * @param 	mixed
	 * @return 	array
	 * @uses	_get_user_by_id
	 * @uses	_get_user_by_uri
	 */
	public function get_user($query)
	{
		if (is_numeric($query)) 
		{
			$row = $this->_get_user_by_id($query);
        } 
		else 
		{
			$row = $this->_get_user_by_username($query);
        }

		return $row;
	}
	
	// ------------------------------------------------------------------------	
	
	/**
	 * Get User By id
	 *
	 * @param 	int
	 * @return	mixed
	 */
	private function _get_user_by_id($user_id)
	{
		$user_id = (int) $user_id;
		
		$this->db->from('users')
				->join('user_groups', 'user_group = group_id', 'left')
				->where('user_id', $user_id);
				
		$query = $this->db->get();
		
		
		if ($query->num_rows() != 1)
		{
			return FALSE;
		}
		
		$data = $query->row_array();
		
		$query->free_result();
		
		return $data;
	}
	
	// ------------------------------------------------------------------------	
	
	/**
	 * Get User By id
	 *
	 * @param 	int
	 * @return	mixed
	 */
	private function _get_user_by_username($user_name)
	{	
		$this->db->from('users')
				->join('user_groups', 'user_group = group_id', 'left')
				->where('user_username', $user_name);
				
		$query = $this->db->get();
		
		if ($query->num_rows() != 1)
		{
			return FALSE;
		}
		
		$data = $query->row_array();
		
		$query->free_result();
		
		return $data;
	}
	
	// ------------------------------------------------------------------------
	
	/**
 	* Add User
 	* 
 	* @param	array
 	* @return	mixed
 	*/
	public function add_user($data)
	{
		$data['user_api_key'] = $this->generate_api_key();
		$data['user_join_date'] = time();
		$data['user_last_login'] = time();
		$data['user_password'] = $this->hash_pass($data['user_password']);
		
		$this->db->insert('users', $data);
		
		if ($this->db->affected_rows() == 0) 
		{
			return FALSE;
		}
		
		$user_id = $this->db->insert_id();
		
		$this->events->trigger('users_model/add_user', $user_id);
		
		return $user_id;
	}
	
	// ------------------------------------------------------------------------
	
	/**
 	* Edit User
 	* 
 	* @param	array
 	* @return	bool
 	*/
	public function edit_user($user_id, $data)
	{
		$user_id = (int) $user_id;
		
		if ( ! empty($data['user_password']))
		{
			$data['user_password'] = $this->hash_pass($data['user_password']);
		}
		else
		{
			unset($data['user_password']);
		}
		
		$this->db->where('user_id', $user_id);
		$this->db->update('users', $data);

		if ($this->db->affected_rows() == 0) 
		{
			return FALSE;
		} 
		
		$this->events->trigger('users_model/edit_user', $user_id);
		
		return TRUE;
	}
	
	// ------------------------------------------------------------------------
		
	/**
	* Delete User
	* 
	* @param	int
	* @uses		delete_images
	* @return	bool
	*/
	public function delete_user($user_id)
	{
		$user_id = (int) $user_id;
		
		$this->db->delete('users', array('user_id' => $user_id)); 
		
		if ($this->db->affected_rows() == 0) 
		{
			return FALSE;
		} 
		
		// @todo - 
		// delete listings
		// delete listing images
		// delete orders? probably not
		// delete pms
		// delete coupons
		// 
		// $this->delete_images($user_id);
		// update cat count
		
		$this->events->trigger('users_model/delete_user', $user_id);

		return TRUE;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Checks if a username is already in use.
	 *
	 * @param	string
	 * @param	int
	 * @return 	bool
	 */
	public function username_check($username, $id = '')
	{
		$this->db->select('user_username')
					->from('users')
					->where('user_username', $username);
					
		if (is_numeric($id)) 
		{
			$this->db->where('user_id !=', $id);
		}
					
		$query = $this->db->get();
		
		if ($query->num_rows() > 0)
		{
			return FALSE;
		}
		
		return TRUE;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Checks if an email is already in use.
	 *
	 * @param	string
	 * @param	int
	 * @return 	bool
	 */
	public function email_check($email, $id = '')
	{
		$this->db->select('user_email')
					->from('users')
					->where('user_email', $email);
					
		if (is_numeric($id)) 
		{
			$this->db->where('user_id !=', $id);
		}
					
		$query = $this->db->get();
		
		if ($query->num_rows() > 0)
		{
			return FALSE;
		}
		
		return TRUE;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Hash the password
	 *
	 * @param	string
	 * @return 	string
	 */
	public function hash_pass($password)
	{
		$this->load->helper('security');
		
		$this->events->trigger('users_model/hash_pass', $password);
		
		$password = do_hash($password);
		
		return $password;
	}
	
	// ------------------------------------------------------------------------
		
	/**
	 * Finds users active listings
	 * 
	 * @param	int
	 * @return	string
	 */
	public function users_listings($listing_owner_id)
	{
		$listing_owner_id = (int) $listing_owner_id;
		
		$this->db->where('listing_owner_id', $listing_owner_id);
		$this->db->from('listings');
		
		return $this->db->count_all_results();
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Update user login info, such as IP-address or login time.
	 *
	 * @param	int
	 * @return	void
	 */
	public function update_login_info($user_id)
	{
		$data = array('user_ip' => $this->input->ip_address(), 'user_last_login' => time());
		$this->db->where('user_id', $user_id);
		$this->db->update('users', $data);
	}
	
	// ------------------------------------------------------------------------
		
	/**
	 * generate_api_key function.
	 * 
	 * @access public
	 * @param int $length. (default: 32)
	 * @return string
	 */
	public function generate_api_key($length = 32)
	{
		$this->load->helper('security');
		
		$string = random_string('alnum', $length);
		
		$this->db->select('user_api_key')->from('users')->where('user_api_key', $string);
		
		$query = $this->db->get();
		
		if ($query->num_rows() > 0) // The key exists so create a new one
		{
			$this->generate_api_key();
		}
		
		$query->free_result();
		
		return $string;
	}
	
	// ------------------------------------------------------------------------
		
	/**
	 * Update the API key in the user table
	 * 
	 * @param	string
	 * @param	int	
	 * @return	bool
	 */
	public function update_api_key($user_api_key, $user_id)
	{
		$user_id = (int) $user_id;
		
		$data = array('user_api_key' => $user_api_key);
		
		$this->db->where('user_id', $user_id);
		
		$this->db->update('users', $data);
		
		$this->events->trigger('users_model/update_api_key', $user_id);
		
		if ($this->db->affected_rows() == 0) 
		{
			return FALSE;
		} 
		
		return TRUE;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Resets a users password. Used by forgot password routine.
	 *
	 * @param 	string
	 * @return 	boolean
	 */
	public function reset_password($hash)
	{
		$this->db->select('user_id, user_verify')
					->from('users')
					->where('user_verify', $hash);
					
		$query = $this->db->get();
		
		if ($query->num_rows() != 1)
		{
			return FALSE;
		}
		
		$user = $query->row_array();
		
		$query->free_result();
		
		// Generate the new password
		$new_password = random_string('alnum', 12);
		$password = $this->hash_pass($new_password);
		
		$data = array('user_password' => $password);
		$this->db->where('user_id', $user['user_id']);
		$this->db->update('users', $data);
		
		if ($this->db->affected_rows() == 0) 
		{
			return FALSE;
		}
		
		// Set up the email
		$user = $this->get_user($user['user_id']);
		
		// Get all the email settings
		$this->load->library('email');
		
		$mailtype = $this->email->mailtype;
		
		$site_email = $this->settings->get_setting('site_email');
		
		$site_name = $this->settings->get_setting('site_name');
		
		$user['password'] = $new_password;
		
		// Generate the login link
		$user['login_url'] = site_url('users/login/');
		
		// Are we sending html or text? 
		if ($mailtype == 'html')
		{
			$this->template->set_layout('emails/html/layout');
			$email_message = $this->template->build('emails/html/new_pass', $user, TRUE);
		}
		else
		{
			$this->template->set_layout('emails/text/layout');
			$email_message = $this->template->build('emails/text/new_pass', $user, TRUE);
		}
		
		$this->email->from($site_email, $site_name);
		
		$this->email->to($user['user_email']);
		
		$this->email->subject(lang('lang_forgot_pass_new'));
		
		$this->email->message($email_message);

		$this->email->send();
		
		log_message('debug', 'Forgot Pass Email: '. $this->email->print_debugger());
		
		// Assign the layout back to the theme layout.
		$this->template->set_layout('layout');
		return $new_password;
	}
	
	// ------------------------------------------------------------------------
	
	public function send_new_password($user_id)
	{
		$user_id = (int) $user_id;
		
		$user = $this->get_user($user_id);
		
		// Generate the random key
		$user_verify = random_string('alnum', 22);
		
		$data = array('user_verify' => $user_verify);
		$this->db->where('user_id', $user_id);
		$this->db->update('users', $data);
		
		// Get all the email settings
		$this->load->library('email');
		
		$site_email = $this->settings->get_setting('site_email');
		
		$site_name = $this->settings->get_setting('site_name');
		
		// Generate the validate link
		$user['validate_url'] = site_url('users/reset/'.$user_verify);
		
		// Are we sending html or text? 
		if ($this->email->mailtype == 'html')
		{
			$this->template->set_layout('emails/html/layout');
			$email_message = $this->template->build('emails/html/forgot_pass_conf', $user, TRUE);
		}
		else
		{
			$this->template->set_layout('emails/text/layout');
			$email_message = $this->template->build('emails/text/forgot_pass_conf', $user, TRUE);
		}
		
		$this->email->from($site_email, $site_name);
		
		$this->email->to($user['user_email']);
		
		$this->email->subject(lang('lang_forgot_pass'));
		
		$this->email->message($email_message);

		$this->email->send();
		
		log_message('debug', 'Forgot Pass Email: '. $this->email->print_debugger());
		
		// Assign the layout back to the theme layout.
		$this->template->set_layout('layout');
	}

	// ------------------------------------------------------------------------
		
	/**
	 * User Notes
	 *
	 * Get all notes for a user.
	 * 
	 * @param	int
	 * @return	array
	 */
	public function user_notes($user_id)
	{
		$user_id = (int) $user_id;
		
		$this->db->from('user_notes')
					->join('users', 'note_added_by = user_id', 'inner')
					->where('note_user_id', $user_id)
					->order_by('note_date', 'DESC');
		
		$query = $this->db->get();

		if ($query->num_rows() == 0) 
		{
			return FALSE;
		}
		
		$data = $query->result_array();
		
		$query->free_result();
		
		return $data;
	}

}

/* End of file users_model.php */
/* Location: ./upload/system/68kb/modules/users/models/users_model.php */ 