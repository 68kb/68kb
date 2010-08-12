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

include(APPPATH . 'libraries/MY_Controller'.EXT);

// ------------------------------------------------------------------------

/**
 * Base Front Controller
 *
 *
 * @subpackage	Controllers
 * @link		http://68kb.com/user_guide/
 *
 */
class Front_Controller extends MY_Controller
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
        
		// Start the benchmark for timing
		$this->benchmark->mark('front_controller_start');

	    // Check if the site is online
		if ($this->users_auth->get_data('user_group') > 1 && $this->config->item('site_offline'))
	    {
			$error = $this->config->item('site_offline_msg') ? $this->config->item('site_offline_msg') : lang('site_offline');
			show_error($error);
		}
		
		// Check if the current page is to be ignored
	    $current_page = $this->uri->uri_string();
		
		//echo $current_page;
		if ( ! strstr($current_page, 'login'))
		{
			$this->session->set_userdata('goto', $current_page);
		}
		
		// Can they view the site? 
		if ( ! $this->users_auth->check_role('can_view_site')) 
		{
			// Set an array of ignored pages.
		    $ignored_pages = array('/users/login', '/users/logout', '/users/register', '/users/forgot', '/users/reset');

		    $is_ignored_page = in_array($current_page, $ignored_pages);

			// Check the user is an admin
		    if ( ! $is_ignored_page)
			{
				// They didn't pass the checks. Log them out.
				$this->users_auth->logout();
				
				// Now forward to login page.
				redirect('users/login');
			}
		}
		
		// Set the theme view folder
		$this->template->set_theme($this->settings->get_setting('site_theme'));
		
		// Set the layout file.
		$this->template->set_layout('layout');
	    
		// Call Events
		$this->events->trigger('front_controller');
		
		// End the benchmark.
		$this->benchmark->mark('front_controller_end');
	}
}
/* End of file Front_Controller.php */
/* Location: ./upload/includes/68kb/libraries/Front_Controller.php */ 