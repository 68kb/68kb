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
 * Auth Library
 * 
 * Modified for our use from:
 * http://www.bramme.net/2008/07/auth-library-for-codeigniter-tutorial/
 *
 * @subpackage	Libraries
 *
 */
class Users_auth
{
    private $_ci;
	
	private $user_data = array();
	
	private $_hash = '';
	
	private $_cookie_name = 'icelogin';
	
	private $_throttle = array(3 => 1, 10 => 2, 20 => 5);
	
	// ------------------------------------------------------------------------
	
    public function __construct()
    {
        $this->_ci = CI_Base::get_instance();
        log_message('debug', "User_lib Class Initialized");
        
		$this->_ci->benchmark->mark('users_lib_start');
		
		$this->_ci->load->library('session');
		$this->_ci->load->model('users/users_model');
		$this->_ci->load->helper('cookie');
        
        if ($this->logged_in())
        {
        	$this->user_data = $this->_ci->users_model->get_user($this->_ci->session->userdata('user_id'));
        }
		else
		{
			$this->user_data = $this->_set_guest();
		}
		
		$this->_ci->benchmark->mark('users_lib_end');
    }
    
	// --------------------------------------------------------------------
	
	/**
	 * Set the mode of the creation
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */	
	public function login($username = '', $password = '', $remember = 'n', $in_admin = FALSE)
    {	
		// Get the user with these details
		$this->user_data = $this->_ci->users_model->get_user($username);
        
		// Call pre login events
		$this->_ci->events->trigger('auth/login/start', $this->user_data);

		// See if we have user data and passwords match.
		if( ! $this->user_data OR empty($this->user_data) OR $this->user_data['user_password'] != $this->_ci->users_model->hash_pass($password))
		{
			$this->_check_failed_login($username);
			return lang('lang_user_login_error');
		}

		// Get the cookie hash
		$this->_get_hash();
		
		// Set the user id to a session
		$this->_create_session();
		
		// Super Admins are exempt from all these checks
		if ($this->user_data['user_group'] > 1)
		{
			// Checked for banned users
			if ($this->ban_check())
			{
				return lang('lang_user_banned');
			}

			// Can they access the site?
			if ($this->user_data['can_view_site'] == 'n')
			{
				return lang('not_authorized');
			}

			// Are they awaiting email confirmation? 
			if ($this->user_data['user_group'] == 3)
			{
				return lang('lang_group_pending');
			}

			// Can they access the admin?
			if ($in_admin && $this->user_data['can_access_admin'] != 'y')
			{
				return lang('not_authorized');
			}
		}
		
		if ($remember == 'y')
		{
			$this->_set_cookie();
		}
		
		$this->_ci->session->unset_userdata('last_check');

		// Call post login events
		$this->_ci->events->trigger('auth/login/end', $this->user_data);
		
		return TRUE;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Check a failed login attempt
	 *
	 * This is used so we can see if someone is trying to gain access. Then
	 * based off the number of failed attempts force the script to sleep a 
	 * few seconds.
	 *
	 * @param	string - Username
	 */
	private function _check_failed_login($username)
	{
		$data = array(
			'failed_username' 	=> $username,
			'failed_date'		=> time(),
			'failed_ip'			=> $this->_ci->input->ip_address()
		);
		
		$this->_ci->db->insert('failed_logins', $data);
		
		// Now check if we need to throttle.
		$this->_ci->db->where('failed_ip', $this->_ci->input->ip_address());
		$this->_ci->db->where('failed_date >', strtotime("-1 day"));
		$this->_ci->db->from('failed_logins');
		$failed_attempts = $this->_ci->db->count_all_results();
		
		krsort($this->_throttle);
		foreach ($this->_throttle as $attempts => $delay) 
		{
			if ($failed_attempts > $attempts) 
			{
				if (is_numeric($delay)) 
				{
					sleep($delay);
					//echo 'You must wait ' . $delay . ' seconds before your next login attempt';
				}
				break;
			}
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Save cookie login
	 *
	 * @return	void
	 */
	private function _set_cookie()
	{
		$cookie = serialize(array($this->user_data['user_username'], $this->_hash, md5($this->user_data['user_username'])));
		set_cookie(array(
			'name' 		=> $this->_cookie_name,
			'value'		=> $cookie,
			'expire'	=> '604800',
		));
	}
	
	// ------------------------------------------------------------------------
	
	/** 
	 * Create session data
	 * 
	 * @uses	_hash
	 */
	private function _create_session()
	{
		$user_cookie = md5($this->user_data['user_username']);
		
		// set all the session data
		// here we set the last login to the previous login
		// so we can use it to show new items.
		$session = array(
				'user_id' 			=> $this->user_data['user_id'],
				'user_username' 	=> $this->user_data['user_username'],
				'user_last_login'	=> $this->user_data['user_last_login'],
				'ip_address'		=> $this->_ci->input->ip_address(),
				'user_cookie'		=> $user_cookie,
				'hash'				=> $this->_hash
			);
		
		$this->_ci->session->set_userdata($session);
		
		// Update last login to now.
		$data = array(
			'user_ip' => $this->_ci->input->ip_address(), 
			'user_last_login' => time(),
			'user_cookie' => $user_cookie,
			'user_session' => $this->_ci->session->userdata('session_id')
		);
		$this->_ci->db->where('user_id', $this->user_data['user_id']);
		$this->_ci->db->update('users', $data);
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Create a unique hash
	 * 
	 * @access 		private
	 * @return 		void
	 * @internal 	requires that user_data be set
	 */
	private function _get_hash()
	{
		$this->_hash = md5($this->user_data['user_id'] . '-' . md5($this->user_data['user_username'] . '-' . $this->user_data['user_password']));
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Logout a user
	 */
	public function logout()
	{
		// Delete the auto login cookie
		delete_cookie($this->_cookie_name);
		
		// Clear the session data
		$session = array(
				'user_id' 			=> '',
				'user_username' 	=> '',
				'user_last_login'	=> '',
				'hash'				=> ''
			);

		$this->_ci->session->unset_userdata($session);
		
		// Destroy session
        $this->_ci->session->sess_destroy();
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Check if a user is logged in
	 * 
	 * First check the session then the cookie.
	 *
	 * @return 	bool
	 */
	public function logged_in()
	{
		if ($this->_ci->session->userdata('user_id') > 0 && $this->_ci->session->userdata('hash'))
		{
			return TRUE;
		}
		elseif (get_cookie($this->_cookie_name)) 
		{
			if ($this->_check_cookie() === TRUE) 
			{
				return TRUE;
			}
		}
		return FALSE;
	}
    
	// ------------------------------------------------------------------------
	
	/**
	 * Check Cookie
	 * 
	 * @access 	private
	 * @internal sets user_data that many private methods require
	 */
	private function _check_cookie() 
	{
		list($username, $hash, $cookie) = @unserialize(get_cookie($this->_cookie_name));
		
		if ( ! $username OR ! $hash OR ! $cookie) 
		{
			return FALSE;
		}
		
		// We do the query because we are using the username and cookie. 
		$this->_ci->db->from('users')
				->join('user_groups', 'user_group = group_id', 'left')
				->where('user_username', $username)
				->where('user_cookie', $cookie);
		
		$query = $this->_ci->db->get();
		
		if ($query->num_rows() <> 1) 
		{
			return FALSE;
		}
		
		// Set the user_data.  
		$this->user_data = $query->row_array();
		
		// Get the special hash
		$this->_get_hash();
		
		// Confirm the hashes match.
		if ($hash <> $this->_hash) 
		{
			return FALSE;
		}
		
		// Everything is good so create the session.
		$this->_create_session();

		return TRUE;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Check a role
	 * 
	 * Check if a user can perform a specific action.
	 * 
	 * @param	string
	 * @return 	bool
	 */
	public function check_role($role = NULL)
	{
		if ($this->get_data('user_group') == 1)
		{
			return TRUE;
		}
		
		if (isset($this->user_data[$role]) && $this->user_data[$role] == 'y')
		{
			return TRUE;
		}
		
		// Call any user role events
		if ($this->_ci->events->active_hook('auth/check_role'))
		{
			$event_array = array('user_data' => $this->user_data, 'role', $role);
			return $this->_ci->events->trigger('auth/check_role', $event_array);
		}
		
		return FALSE;
	}
	
	// ------------------------------------------------------------------------
	
	/** 
	 * Get an item for the user.
	 *
	 * @param 	string
	 * @return 	mixed
	 */
	public function get_data($data)
	{
		return ( ! isset($this->user_data[$data])) ? FALSE : $this->user_data[$data];
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Check if a user is banned 
	 *
	 * This method returns TRUE if they are banned. 
	 *
	 * @return 	bool
	 */
	public function ban_check()
	{
		// first check group. 4 is the banned group.
		if ($this->user_data['user_group'] == 4)
		{
			return TRUE;
		}
		// Now lets check email address
		
		// Call any ban check events
		if ($this->_ci->events->active_hook('auth/ban_check'))
		{
			return $this->_ci->events->trigger('auth/ban_check', $this->user_data);
		}
		
		return FALSE;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Set a user as a guest.
	 *
	 * @return 	array
	 */
	private function _set_guest()
	{
		$this->_ci->db->from('user_groups')
				->where('group_id', 5);
				
		$query = $this->_ci->db->get();
		
		if ($query->num_rows() != 1)
		{
			return FALSE;
		}
		
		$data = $query->row_array();
		
		$data['user_group'] = 5;
		$data['user_id'] = 0;
		
		$this->user_data = $data;
		
		$query->free_result();
		
		return $data;
	}
	// ------------------------------------------------------------------------
	
	/**
	 * Redirect user to page requested
	 *
	 * @param	bool
	 */
	public function redirect($in_admin = FALSE)
	{
		if ( ! $this->_ci->session->userdata('goto'))
		{
			if ($in_admin)
			{
				redirect('admin');
			}
			redirect('users/account');
		} 
		else 
		{
			$goto = $this->_ci->session->userdata('goto');
			$goto = strip_tags($goto);
			$goto = str_replace('.', '', $goto);
			redirect($goto);
		}
	}
	
}

/* End of file Users_auth.php */
/* Location: ./upload/includes/68kb/modules/users/libraries/Users_auth.php */ 