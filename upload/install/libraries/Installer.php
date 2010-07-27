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
 * Installer
 *
 * Idea from pyrocms
 */
class Installer
{
	private $_ci;
	
	// ------------------------------------------------------------------------
	
	function __construct()
	{
		$this->_ci =& get_instance();
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * @return string The PHP version
	 *
	 * Function to retrieve the PHP version
	 */
	function get_php_version()
	{
		// Set the PHP version
		$php_version = phpversion();
		
		// Validate the version
		return ($php_version >= 5) ? $php_version : FALSE;
	}
	
	/**
	 * @param 	string $data The post data
	 * @return 	bool
	 * 
	 * Function to validate the $_POST results from step 3
	 */
	function validate()
	{
		// Save this junk for later
		$this->ci->session->set_userdata(array(
			'hostname' => $this->ci->input->post('hostname'),
			'username' => $this->ci->input->post('username'),
			'password' => $this->ci->input->post('password'),
			'port' => $this->ci->input->post('port'),
			'http_server' => $this->ci->input->post('http_server')
		));

		$this->ci->load->library('form_validation');

		$this->ci->form_validation->set_rules(array(
			array(
				'field' => 'hostname',
				'label'	=> 'Server',
				'rules'	=> 'trim|required'
			),
			array(
				'field' => 'username',
				'label'	=> 'Username',
				'rules'	=> 'trim|required'
			),
			array(
				'field' => 'password',
				'label'	=> 'Password',
				'rules'	=> 'trim'
			),
			array(
				'field' => 'port',
				'label'	=> 'Port',
				'rules'	=> 'trim|required'
			),
			array(
				'field' => 'http_server',
				'label'	=> 'Server Software',
				'rules'	=> 'trim|required'
			),
		));

		return $this->ci->form_validation->run();
	}

	function test_db_connection()
	{
		$hostname = $this->ci->session->userdata('hostname');
		$username = $this->ci->session->userdata('username');
		$password = $this->ci->session->userdata('password');
		$port	  = $this->ci->session->userdata('port');

		return @mysql_connect("$hostname:$port", $username, $password);
	}
	
	/**
	 * @param 	string $data The data from the form
	 * @return 	array
	 *
	 * Install the PyroCMS database and write the database.php file
	 */
	function install($data)
	{				
		// Retrieve the database server, username and password from the session
		$server 	= $this->ci->session->userdata('hostname') . ':' . $this->ci->session->userdata('port');
		$username 	= $this->ci->session->userdata('username');
		$password 	= $this->ci->session->userdata('password');
		$database 	= $data['database'];
		
		// User settings
		$user_salt		= substr(md5(uniqid(rand(), true)), 0, 5);
		$data['user_password'] 	= sha1($data['user_password'] . $user_salt);

		// Get the SQL for the default data and parse it
		$data_sql		= file_get_contents('./sql/2-default-data.sql');
		$data_sql		= str_replace('__VERSION__', 	CMS_VERSION,				$data_sql);

		// Get the SQL for the user data and parse it
		$user_sql		= file_get_contents('./sql/3-default_user.sql');
		$user_sql		= str_replace('__EMAIL__', 		$data['user_email'], 		$user_sql);
		$user_sql		= str_replace('__USERNAME__', 	$data['user_name'], 		$user_sql);
		$user_sql		= str_replace('__PASSWORD__', 	$data['user_password'], 	$user_sql);
		$user_sql		= str_replace('__FIRSTNAME__', 	$data['user_firstname'], 	$user_sql);
		$user_sql		= str_replace('__LASTNAME__', 	$data['user_lastname'], 	$user_sql);
		$user_sql		= str_replace('__SALT__', 		$user_salt,					$user_sql);
		$user_sql		= str_replace('__NOW__', 		time(),						$user_sql);
		
		// Create a connection
		if( !$this->db = mysql_connect($server, $username, $password) )
		{
			return array('status' => FALSE,'message' => 'The installer could not connect to the MySQL server or the database, be sure to enter the correct information.');
		}
		
		// Do we want to create the database using the installer ? 
		if( !empty($data['create_db'] ))
		{
			mysql_query('CREATE DATABASE IF NOT EXISTS '.$database, $this->db);
		}
		
		// Select the database we created before
		if( !mysql_select_db($database, $this->db) )
		{
			return array(
						'status'	=> FALSE,
						'message'	=> '',
						'code'		=> 101
					);
		}
		
		// HALT...! Query time!
		if( !$this->_process_schema('1-tables') )
		{
			return array(
						'status'	=> FALSE,
						'message'	=> mysql_error($this->db),
						'code'		=> 102
					);
		}
		
		if( !$this->_process_schema($data_sql, FALSE) )
		{
			return array(
						'status'	=> FALSE,
						'message'	=> mysql_error($this->db),
						'code'		=> 103
					);
		}
		
		if( !$this->_process_schema($user_sql, FALSE) )
		{
			return array(
						'status'	=> FALSE,
						'message'	=> mysql_error($this->db),
						'code'		=> 104
					);
		}
			
		// If we got this far there can't have been any errors. close and bail!
		mysql_close($this->db);
		
		// Write the database file
		if( ! $this->write_db_file($database) )
		{
			return array(
						'status'	=> FALSE,
						'message'	=> '',
						'code'		=> 105
					);
		}
		
		// Write the config file.
		if( ! $this->write_config_file() )
		{
			return array(
						'status'	=> FALSE,
						'message'	=> '',
						'code'		=> 106
					);
		}

		return array('status' => TRUE);
	}

	private function _process_schema($schema_file, $is_file = TRUE)
	{
		// String or file?
		if ( $is_file == TRUE )
		{
			$schema 	= file_get_contents('./sql/' . $schema_file . '.sql');
		}
		else
		{
			$schema 	= $schema_file;
		}
		
		// Parse the queries
		$queries 	= explode('-- command split --', $schema);
		
		foreach($queries as $query)
		{
			$query = rtrim( trim($query), "\n;");
			
			@mysql_query($query, $this->db);
			
			if(mysql_errno($this->db) > 0)
			{
				return FALSE;
			}
		}
		
		return TRUE;
	}
	
	/**
	 * @param 	string $database The name of the database
	 *
	 * Writes the database file based on the provided database settings
	 */
	function write_db_file($database)
	{
		// First retrieve all the required data from the session and the $database variable
		$server 	= $this->ci->session->userdata('hostname');
		$username 	= $this->ci->session->userdata('username');
		$password 	= $this->ci->session->userdata('password');
		$port		= $this->ci->session->userdata('port');
		
		// Open the template file
		$template 	= file_get_contents('./assets/config/database.php');
		
		$replace = array(
			'__HOSTNAME__' 	=> $server,
			'__USERNAME__' 	=> $username,
			'__PASSWORD__' 	=> $password,
			'__DATABASE__' 	=> $database,
			'__PORT__' 		=> $port ? $port : 3306
		);
		
		// Replace the __ variables with the data specified by the user
		$new_file  	= str_replace(array_keys($replace), $replace, $template);
		
		// Open the database.php file, show an error message in case this returns false
		$handle 	= @fopen('../application/config/database.php','w+');
		
		// Validate the handle results
		if($handle !== FALSE)
		{
			return @fwrite($handle, $new_file);
		}
		
		return FALSE;
	}
	
	/**
	 * @return bool
	 *
	 * Writes the config file.n
	 */
	function write_config_file()
	{
		// Open the template
		$template = file_get_contents('./assets/config/config.php');
		
		$server_name = $this->ci->session->userdata('http_server');
		$supported_servers = $this->ci->config->item('supported_servers');

		// Able to use clean URLs?
		if($supported_servers[$server_name]['rewrite_support'] !== FALSE)
		{
			$index_page = '';
		}
		
		else
		{
			$index_page = 'index.php';
		}
		
		// Replace the __INDEX__ with index.php or an empty string
		$new_file = str_replace('__INDEX__', $index_page, $template);
		
		// Open the database.php file, show an error message in case this returns false
		$handle = @fopen('../application/config/config.php','w+');
		
		// Validate the handle results
		if($handle !== FALSE)
		{
			return fwrite($handle, $new_file);
		}
		
		return FALSE;
	}
}

/* End of file installer_lib.php */
/* Location: ./installer/libraries/installer_lib.php */