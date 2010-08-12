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
 * Database Migrations
 *
 * @subpackage	Models
 * @link		http://68kb.com/user_guide/
  */
class Alter_users
{
	var $msg = '';
	
	// ------------------------------------------------------------------------
	
	function up()
	{
		$CI =& get_instance();
		
		$CI->load->dbforge();
		
		if ($CI->db->field_exists('username', 'users'))
		{
			// Get all the users
			$CI->db->from('users');
			$query = $CI->db->get();

			// Drop the table
			$CI->dbforge->drop_table('users');

			// Now recreate it
			$fields = array(
					'user_id' => array('type' => 'INT','constraint' => 11,'unsigned' => TRUE,'auto_increment' => TRUE),
			);
			$CI->dbforge->add_field($fields);
			$CI->dbforge->add_field("user_ip varchar(16) NOT NULL default '0'");
			$CI->dbforge->add_field("user_email varchar(50) NOT NULL default ''");
			$CI->dbforge->add_field("user_username varchar(50) NOT NULL default ''");
			$CI->dbforge->add_field("user_password varchar(50) NOT NULL default ''");
			$CI->dbforge->add_field("user_group int(5) NOT NULL default '0'");
			$CI->dbforge->add_field("user_join_date int(11) NOT NULL default '0'");
			$CI->dbforge->add_field("user_last_login int(11) NOT NULL default '0'");
			$CI->dbforge->add_field("last_activity int(11) NOT NULL default '0'");
			$CI->dbforge->add_field("user_cookie varchar(50) NOT NULL default ''");
			$CI->dbforge->add_field("user_session varchar(50) NOT NULL default ''");
			$CI->dbforge->add_field("user_api_key varchar(50) NOT NULL default ''");
			$CI->dbforge->add_field("user_verify varchar(50) NOT NULL default ''");
	        $CI->dbforge->add_key('user_id', TRUE);
	        $CI->dbforge->add_key('user_username');
	        $CI->dbforge->add_key('user_email');
			$CI->dbforge->create_table('users');

			if ($query->num_rows() > 0) 
			{
				foreach ($query->result_array() as $row)
				{
					$data['user_ip'] = $row['custip'];
					$data['user_email'] = $row['email'];
					$data['user_username'] = $row['username'];
					$data['user_password'] = $row['password'];
					$data['user_group'] = $row['level'];
					$data['user_join_date'] = $row['joindate'];
					$data['user_last_login'] = $row['lastlogin'];
					$data['user_api_key'] = '';

					$this->db->insert('users', $data);
				}
			}

			$query->free_result();
		}

		
		$this->msg = 'Altered Users Table.';
		return $this->msg;
	}

	// ------------------------------------------------------------------------

	function down()
	{
		
	}
}

/* End of file 005_Alter_settings.php */
/* Location: ./upload/setup/migrations/005_Alter_settings.php */