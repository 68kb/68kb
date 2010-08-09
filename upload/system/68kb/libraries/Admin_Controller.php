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

include(APPPATH . 'libraries/MY_Controller'.EXT);

// ------------------------------------------------------------------------

/**
 * Admin Base Controller
 *
 *
 * @subpackage	Controllers
 * @link		http://68kb.com/user_guide/
 *
 */
class Admin_Controller extends MY_Controller
{
	protected $admin_folder = 'admin';
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->benchmark->mark('admin_controller_start');
		
		if ($this->config->item('admin_directory'))
		{
			$this->admin_folder = $this->config->item('admin_directory');
		}
		
		$allow_access = FALSE;
	        	
	    // Set an array of ignored pages.
	    $ignored_pages = array($this->admin_folder.'/login', $this->admin_folder.'/logout');
			
	    // Check if the current page is to be ignored
	    $current_page = $this->uri->segment(1, '') . '/' . $this->uri->segment(2, '');
	    $is_ignored_page = in_array($current_page, $ignored_pages);
		
		// See if this user can access admin
		$is_admin = $this->users_auth->check_role('can_access_admin');
		
		// Check the user is an admin
	    if ( ! $is_ignored_page AND ! $is_admin)
		{
			// They didn't pass the checks. Log them out.
			$this->users_auth->logout();
			
			$this->session->set_userdata('goto', $current_page);
			
			// Now forward to login page.
			redirect($this->admin_folder.'/login');
		}
		
		// Tell the template we are in admin
		$this->template->in_admin(TRUE);
		
		// Disable the parser.
		$this->template->enable_parser(FALSE);
		
		// Load the base js file
		$this->template->set_metadata('js', 'admin/js/base/', 'js');
		$this->template->set_metadata('js', 'js/fancybox/jquery.fancybox-1.3.1.js', 'js_include');
		$this->template->set_metadata('stylesheet', base_url() . 'js/fancybox/jquery.fancybox-1.3.1.css', 'link');
		
		// Set the default admin layout file.
		$this->template->set_layout('admin/layout');
		
		$this->table_template = array(
			'table_open'		=> '<table class="main" id="grid" width="100%" border="0" cellspacing="0" cellpadding="0">',
			'row_start'			=> '<tr class="second">',
			'row_alt_start'		=> '<tr class="first">'
		);
								
		// Load helpers
		$this->load->helper(array('tooltip','nav'));
		
		$this->events->trigger('admin_controller');
		
		$this->benchmark->mark('admin_controller_end');
	}
}

/* End of file Admin_Controller.php */
/* Location: ./upload/includes/68kb/libraries/Admin_Controller.php */ 