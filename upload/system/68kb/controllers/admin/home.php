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
 * Admin Home Controller
 *
 *
 * @subpackage	Controllers
 * @link		http://68kb.com/user_guide
 *
 */
class Home extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * index function.
	 * 
	 * @access public
	 * @return void
	 */
	public function index()
	{
		$this->load->helper('text');
		
		$data['nav'] = 'home';
		
		// buid rss
		//$this->load->model('pages/pages_model');
		$data['news'] = ''; // $this->pages_model->get_rss_feed('http://68kb.com/blog/rss', 3);
		
		// show home page
		$this->template->build('admin/home', $data);
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Login Controller
	*
	* Allow the admin to login
	*
	* @access	public
	*/
	public function login()
	{
		$data['no_cache'] = TRUE;
		if ($this->session->userdata('last_check'))
		{
			$last_check = time() - $this->session->userdata('last_check');
			if($last_check < 3)
			{
				sleep(2);
			}
		}

		$this->session->set_userdata('last_check', time());
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('username', 'lang:lang_username', 'required');
	    $this->form_validation->set_rules('password', 'lang:lang_password', 'required');
	    $this->form_validation->set_error_delimiters('<p>', '</p>');

		$this->template->set_layout(FALSE);
		
	    if ($this->form_validation->run() == false)
	    {
		    $this->template->build('admin/login', $data);
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
				$this->template->build('admin/login', $data);
			}
			else
			{
				$this->users_auth->redirect(TRUE);
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
		redirect('admin/login');
	}
}
/* End of file home.php */
/* Location: ./upload/includes/68kb/controllers/admin/home.php */ 