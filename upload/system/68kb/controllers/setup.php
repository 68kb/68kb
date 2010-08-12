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
 * Setup Controller
 *
 *
 * @subpackage	Controllers
 * @link		http://68kb.com/user_guide/
 *
 */
class Setup extends Controller
{
	/**
	 * Script Version
	 */
	var $script_version = '2.0.0';
	
	/**
	 * Build Version
	 */
	var $build_version = '072710';
	
	/**
	 * Array of template data
	 */
	var $data = array();
	
	/**
	 * Hold any errors
	 */
	var $errors = array();
	
	/**
	 * Is the db installed? We assume it isn't
	 */
	var $is_installed = FALSE;
	
	/**
	 * Min PHP Version
	 */
	var $min_php = '5.1';
	
	/**
	 * Min MySQL
	 */
	var $min_mysql = '';
	
	
	/**
	 * Setup the setup. :-) 
	 */
    function __construct()
	{
		parent::__construct();
		
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		
		// Check we are running the min version of php.
		if (is_php($this->min_php) == FALSE)
		{
 			$this->errors[] = 'PHP version '. $this->min_php .' or higher required.';
		}
		
		// Set the version
		$this->data['version'] = $this->script_version;
		
		// Guess the url
		$url = "http://".@$_SERVER['HTTP_HOST'];
		$url .= preg_replace('@/+$@','',dirname(@$_SERVER['SCRIPT_NAME'])).'/';
		$this->config->set_item('base_url', $url);
		$this->data['base_url'] = $url;
		
		// load the language
		$this->lang->load('setup');
	}
    
	// ------------------------------------------------------------------------
	
	/**
	 * This is the start of the setup. 
	 */
	function index()
	{
		$this->load->helper('typography');
		$this->data['body'] = $this->load->view('setup/license', $this->data, TRUE);
		$this->load_template();
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Validate the key entered is correct.
	 *
	 * @param	string - Key
	 * @return 	bool
	 */
	function key_check($key)
	{
		if ($key != $this->config->item('license_key'))
		{
			$this->form_validation->set_message('key_check', 'The license key you entered is not correct.');
			return FALSE;
		}
		return TRUE;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Install
	 * 
	 * Perform the install.
	 */
	function install()
	{
		$this->data['cache'] 				= $this->_writable(APPPATH.'cache');
		$this->data['config_path']		 	= $this->_writable(APPPATH.'config/config.php');
		$this->data['db_path']			 	= $this->_writable(APPPATH.'config/database.php');
		
		// Setup form validation
		$this->form_validation->set_rules('db_name', 'Database Name', 'required|callback__test_db');
		$this->form_validation->set_rules('db_hostname', 'Database Hostname', 'required');
		$this->form_validation->set_rules('db_username', 'Database Username', 'required');
		$this->form_validation->set_rules('db_password', 'Database Password', '');
		$this->form_validation->set_rules('db_prefix', 'Database Prefix', '');
		
		// Handle Validation
		if ($this->form_validation->run() == FALSE)
		{
			$this->data['body'] = $this->load->view('setup/install', $this->data, TRUE);
			$this->load_template();
		}
		else
		{
			// Assign out the posted data
			$db['dbdriver'] = "mysql";
			$db['hostname'] = $this->input->post('db_hostname', TRUE);
			$db['username'] = $this->input->post('db_username', TRUE);
			$db['password'] = $this->input->post('db_password', TRUE);
			$db['dbprefix'] = $this->input->post('db_prefix', TRUE);
			$db['database'] = $this->input->post('db_name', TRUE);
			$db['db_debug'] = FALSE;
			
			$this->_write_db_file($db);

			redirect('setup/install_final');
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Test the db settings
	 */
	public function _test_db()
	{
		$db['dbdriver'] = "mysql";
		$db['hostname'] = $this->input->post('db_hostname', TRUE);
		$db['username'] = $this->input->post('db_username', TRUE);
		$db['password'] = $this->input->post('db_password', TRUE);
		$db['database'] = $this->input->post('db_name', TRUE);
		$db['db_debug'] = FALSE;
		
		$this->load->database($db);

		if ($this->db->conn_id)
		{
			$this->load->dbutil();
			
			if ($this->dbutil->database_exists($db['database']))
			{
				return TRUE;
			}
			else
			{
				$this->load->dbforge();

				if ( ! $this->dbforge->create_database($db['database']))
				{
					$this->form_validation->set_message('_test_db', 'Can not create the database. Please create it manually.');
					return FALSE;
				}
				return TRUE;
			}
		}
		else
		{
			$this->form_validation->set_message('_test_db', 'Can not connect to the database');
			return FALSE;
		}
		die;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Writes the database file based on the provided database settings
	 */
	private function _write_db_file($db)
	{
		$this->load->helper('file');
		
		// Open the template file
		$template = read_file(ROOTPATH.'setup/sample_data/database.php');
		
		$replace = array(
			'__HOSTNAME__' 	=> $db['hostname'],
			'__USERNAME__' 	=> $db['username'],
			'__PASSWORD__' 	=> $db['password'],
			'__DATABASE__' 	=> $db['database'],
			'__DBPREFIX__' 	=> $db['dbprefix'],
		);
		
		// Replace the __ variables with the data specified by the user
		$new_file  	= str_replace(array_keys($replace), $replace, $template);
		
		$db_file = APPPATH.'config/database.php';
		if ( ! write_file($db_file, $new_file, 'w+'))
		{
			// This shouldn't happen. But just to be safe.
			show_error('Unable to write to the database file');
		}
		@chmod($db_file, FILE_READ_MODE);
		return TRUE;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Writes the database file based on the provided database settings
	 */
	private function _write_config_file($config)
	{
		$this->load->helper('file');
		$this->load->helper('string');
		
		// Open the template file
		$template = read_file(ROOTPATH.'setup/sample_data/config.php');
		
		$replace = array(
			'__ENCRYPTION__' 	=> random_string('alnum', 16),
			'__URL__'			=> $config['url']
		);
		
		// Replace the __ variables with the data specified by the user
		$new_file  	= str_replace(array_keys($replace), $replace, $template);
		$config_file = APPPATH.'config/config.php';
		if ( ! write_file($config_file, $new_file, 'w+'))
		{
			// This shouldn't happen. But just to be safe.
			show_error('Unable to write to the config file');
		}
		
		@chmod($config_file, FILE_READ_MODE);
		
		return TRUE;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Final install routine
	 */
	public function install_final()
	{
		// Setup form validation
		$this->form_validation->set_rules('url', 'Site Url', 'required');
		$this->form_validation->set_rules('username', 'Username', 'required|alpha_numeric');
		$this->form_validation->set_rules('password', 'Password', 'required');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		
		$this->data['guess_url'] = $this->data['base_url'];
		
		// Handle Validation
		if ($this->form_validation->run() == FALSE)
		{
			$this->data['body'] = $this->load->view('setup/install_details', $this->data, TRUE);
			$this->load_template();
		}
		else
		{
			// Assign out the posted data
			$this->data['username'] = $this->input->post('username', TRUE);
			$this->data['password'] = $this->input->post('password', TRUE);
			$this->data['email'] = $this->input->post('email', TRUE);
			$this->data['sample'] = $this->input->post('sample', TRUE);
			
			// Write the config file
			$config['url'] = $this->input->post('url', TRUE);
			$this->_write_config_file($config);
			
			// Now install the database.
			$this->_install_db();
			
			// Do they want sample data?
			if ($sample = $this->input->post('sample', TRUE))
			{	
				$this->_process_sql($sample);
			}
			
			$data = array( 
				'option_value' => $this->input->post('site_name')
			);
			$this->db->where('option_name', 'site_name');
			$this->db->update('settings', $data);
			
			$data = array( 
				'option_value' => $this->data['email']
			);
			$this->db->where('option_name', 'site_email');
			$this->db->update('settings', $data);
			
			$data = array( 
				'option_value' => $this->build_version
			);
			$this->db->where('option_name', 'script_build');
			$this->db->update('settings', $data);
			
			// Load the last setup template.
			$this->data['body'] = $this->load->view('setup/complete', $this->data, TRUE);
			$this->load_template();
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Run the install
	*
	*/
	private function _install_db()
	{
		$this->load->library('migrate');
		$this->migrate->setverbose(TRUE);
		
		$this->data['log'] = $this->migrate->install();
		
		$this->load->library('cache/cache');
		$this->load->library('addons/events');
		$this->load->model('users/users_model');
		
		// Update version
		//$this->settings->edit_setting('script_version', $this->script_version);
		
		$insert_data = array(
			    'user_email' => $this->data['email'],
			    'user_username' => $this->data['username'],
			    'user_password' => $this->data['password'],
			    'user_join_date' => time(),
				'user_group'	=> 1
			);
		$this->users_model->add_user($insert_data);
		
		// Now add version data
		$version_data['option_value'] = $this->script_version;
		$this->db->where('option_name', 'script_version');
		$this->db->update('settings', $version_data);
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Do the upgrade
	*
	* @access	public
	*/
	function upgrade()
	{
		$this->load->database();
		$this->load->library('migrate');
		$this->migrate->setverbose(TRUE);
		$this->data['log'] = $this->migrate->version();
		
		// now maintenance
		$version_data['option_value'] = $this->script_version;
		$this->db->where('option_name', 'script_version');
		$this->db->update('settings', $version_data);
		
		$data = array( 
			'option_value' => $this->build_version
		);
		$this->db->where('option_name', 'script_build');
		$this->db->update('settings', $data);
		
		//optimize db
		$this->load->dbutil();
		$this->dbutil->optimize_database();

		//delete cache
		$this->load->helper('file');
		delete_files($this->config->item('cache_path'));
		$this->load->library('settings/settings');
		$this->load->library('cache/cache');
		$this->cache->delete_all();
		
		// Load the last setup template.
		$this->data['body'] = $this->load->view('setup/complete', $this->data, TRUE);
		$this->load_template();
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Check if a file is writable.
	 *
	 * @param	string The file name.
	 * @access	private
	 */
	private function _writable($filename) 
	{
		if ( ! is_really_writable($filename)) 
		{
			$this->data['error'] = TRUE;
			return '<div class="fail">Not Writable</div>';
		}
		return '<div class="pass">Passed</div>';
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Checks to see if any errors exists.
	 *
	 * @return 	boolean True or False
	 */
	function is_error()
	{
		return (sizeof($this->errors) > 0) ? TRUE : FALSE;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Get the error list
	 *
	 * @return 	array
	 */
	function get_error_list()
	{
		return $this->errors;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Load the layout view file.
	 */
	function load_template()
	{
		if ($this->is_error())
		{
			$this->data['errors'] = $this->get_error_list();
		}
		$this->load->view('setup/layout.php', $this->data);
	}
	
	// ------------------------------------------------------------------------
	
	private function _process_sql($sql_file)
	{
		$file = ROOTPATH .'setup/sample_data/'.$sql_file.'.sql';
		
		if ( ! file_exists($file))
		{
			echo 'file not found';
			return FALSE;
		}
		
		$schema = file_get_contents($file);
		
		$schema = str_replace('{prefix}', $this->db->dbprefix, $schema);
		
		$queries = explode('-- query --', $schema);
		
		foreach($queries as $query)
		{
			$query = rtrim( trim($query), "\n;");
			$this->db->query($query);
		}
		
		return TRUE;
	}
	
	// ------------------------------------------------------------------------
	
	private function _drop_tables()
	{
		$this->load->dbforge();
		
		$tables = $this->db->list_tables();
		
		foreach ($tables as $table)
		{
			$table = str_replace($this->db->dbprefix, '', $table);
			$this->dbforge->drop_table($table);
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Migrate to a particular version
	 *
	 * This is mainly for dev purposes. 
	 *
	 */
	function version($id = NULL)
	{
		$this->load->library('migrate');
		$this->migrate->setverbose(TRUE);
		
		if (is_null($id)) 
		{
			show_error("Must pass in an id.");
		}
		
		$migrate = $this->migrate->version($id);
		
		if ( ! $migrate)
		{
			show_error($this->migrate->error);
		}
		else
		{
			echo $migrate ." <br />Migration Successful<br />";
		}
	}
}
/* End of file setup.php */
/* Location: ./upload/system/68kb/controllers/setup.php */ 