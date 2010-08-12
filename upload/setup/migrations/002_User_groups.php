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
class User_groups
{
	// ------------------------------------------------------------------------

	function up()
	{
		$CI =& get_instance();
		
		$CI->load->dbforge();
		
		if ( ! $CI->db->table_exists('user_groups'))
		{
			$fields = array(
					'group_id' => array('type' => 'INT','constraint' => 11,'unsigned' => TRUE,'auto_increment' => TRUE),
			);
			$CI->dbforge->add_field($fields);
			$CI->dbforge->add_field("group_name varchar(150) NOT NULL default ''");
			$CI->dbforge->add_field("group_description mediumtext NOT NULL");
			$CI->dbforge->add_field("can_view_site char(1) NOT NULL default 'y'");
			$CI->dbforge->add_field("can_access_admin char(1) NOT NULL default 'n'");
			$CI->dbforge->add_field("can_manage_articles char(1) NOT NULL default 'n'");
			$CI->dbforge->add_field("can_delete_articles char(1) NOT NULL default 'n'");
			$CI->dbforge->add_field("can_manage_users char(1) NOT NULL default 'n'");
			$CI->dbforge->add_field("can_manage_categories char(1) NOT NULL default 'n'");
			$CI->dbforge->add_field("can_delete_categories char(1) NOT NULL default 'n'");
			$CI->dbforge->add_field("can_manage_settings char(1) NOT NULL default 'n'");
			$CI->dbforge->add_field("can_manage_utilities char(1) NOT NULL default 'n'");
			$CI->dbforge->add_field("can_manage_themes char(1) NOT NULL default 'n'");
			$CI->dbforge->add_field("can_manage_modules char(1) NOT NULL default 'n'");
			
			$CI->dbforge->add_field("can_search char(1) NOT NULL default 'y'");
			
			$CI->dbforge->add_key('group_id', TRUE);
			if ($CI->dbforge->create_table('user_groups'))
			{
				$data = array(
					'group_name' 		=> 'Site Admins',
					'group_description' => 'Site Administrators',
					'can_view_site' 	=> 'y',
					'can_access_admin' 	=> 'y',
					'can_manage_articles' => 'y',
					'can_delete_articles' => 'y',
					'can_manage_users' => 'y',
					'can_manage_categories' => 'y',
					'can_delete_categories' => 'y',
					'can_manage_settings' => 'y',
					'can_manage_utilities' => 'y',
					'can_manage_themes' => 'y',
					'can_manage_modules' => 'y',
					'can_search' => 'y',
				);

				$CI->db->insert('user_groups', $data);
				
				$data = array(
					'group_name' 		=> 'Registered',
					'group_description' => 'Registered Users',
					'can_access_admin' 	=> 'n'
				);
				
				$CI->db->insert('user_groups', $data);
				
				$data = array(
					'group_name' 		=> 'Pending',
					'group_description' => 'Users Awaiting Email Confirmation',
					'can_access_admin' 	=> 'n'
				);
				
				$CI->db->insert('user_groups', $data);
				
				$data = array(
					'group_name' 		=> 'Banned',
					'group_description' => 'Banned Users',
					'can_view_site' 	=> 'n',
				);
				
				$CI->db->insert('user_groups', $data);
				
				$data = array(
					'group_name' 		=> 'Guest',
					'group_description' => 'Site Visitors not logged in',
					'can_access_admin' 	=> 'n'
				);
				
				$CI->db->insert('user_groups', $data);
				
				
				if ($CI->migrate->verbose)
				{
					return "Added User Groups table...";
				}
			}
			else
			{
				return 'ERROR: Adding User Groups Table.';
			}
		}
	}

	// ------------------------------------------------------------------------

	function down()
	{
		$CI =& get_instance();

		$CI->load->dbforge();

		if ($CI->dbforge->drop_table('user_groups'))
		{
			if ($CI->migrate->verbose)
			{
				return "Dropping user_groups table...";
			}
		}
	}
}

/* End of file 002_User_groups.php */
/* Location: ./upload/setup/migrations/002_User_groups.php */ 