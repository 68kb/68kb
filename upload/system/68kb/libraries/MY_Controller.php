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
 * Base Controller
 *
 *
 * @subpackage	Controllers
 * @link		http://68kb.com/user_guide/
 *
 */
class MY_Controller extends Controller
{
	protected $module;
	protected $controller;
	protected $method;
	
	// ------------------------------------------------------------------------
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		
		// Start benchmark
		$this->benchmark->mark('my_controller_start');
		
		// Load needed libraries
		if ( ! defined('ENV') && ! $this->db->table_exists('settings'))
		{
			redirect('setup');
			die;
		}
		
		// Load needed libs
		$this->load->library('session');
		$this->load->library('settings/settings');
		$this->load->library('cache/cache');
		$this->load->library('addons/events');
		$this->load->library('users/users_auth');
		
		// Setup first event
		$this->events->trigger('my_controller_start');
		
		// Run the daily cron job
		$this->_cron();
		
		// Work out module, controller and method and make them accessable throught the CI instance
        $this->module = $this->router->fetch_module();
        $this->controller = $this->router->fetch_class();
        $this->method = $this->router->fetch_method();
		
		// Setup ending my_controller event.
		$this->events->trigger('my_controller_end');
		
		// End Benchmark
		$this->benchmark->mark('my_controller_end');
	}
	
	// ------------------------------------------------------------------------
	
	private function _cron()
	{
		$this->benchmark->mark('cron_start');
		
		// If it hasn't ran since yesterday
		if ($this->settings->get_setting('script_last_cron') < strtotime("-1 day"))
		{
			
			if ( ! defined('NO_VERSION_CHECK'))
			{
				$this->load->helper('version');

				$this->settings->edit_setting('script_latest', version_check());
			}
			
			// Call any events that use cron
			$this->events->trigger('cron');
			
			// Set the last_cron to now.
			$this->settings->edit_setting('script_last_cron', time());
		}
		
		$this->benchmark->mark('cron_end');
	}
}

//include(APPPATH . 'libraries/Admin_Controller'.EXT);

/* End of file MY_Controller.php */
/* Location: ./upload/includes/68kb/libraries/MY_Controller.php */ 