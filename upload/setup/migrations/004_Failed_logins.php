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
class Failed_logins
{
	var $msg = '';
	
	// ------------------------------------------------------------------------
	
	function up()
	{
		$CI =& get_instance();
		
		$CI->load->dbforge();
		
		if ( ! $CI->db->table_exists('failed_logins'))
		{
			$fields = array(
					'failed_id' => array('type' => 'INT','constraint' => 11,'unsigned' => TRUE,'auto_increment' => TRUE),
			);
			$CI->dbforge->add_field($fields);
			$CI->dbforge->add_field("failed_username varchar(255) NOT NULL default ''");
			$CI->dbforge->add_field("failed_ip varchar(16) NOT NULL default ''");
			$CI->dbforge->add_field("failed_date int(11) NOT NULL default '0'");
			
			$CI->dbforge->add_key('failed_id', TRUE);
			
			if ($CI->dbforge->create_table('failed_logins'))
			{	
				if ($CI->migrate->verbose)
				{
					$this->msg .= "Added failed_logins table...<br />";
				}
			}
			else
			{
				$this->msg = 'ERROR: Adding failed_logins Table.';
			}
		}
		return $this->msg;
	}

	// ------------------------------------------------------------------------

	function down()
	{
		$CI =& get_instance();

		$CI->load->dbforge();

		if ($CI->dbforge->drop_table('failed_logins'))
		{
			if ($CI->migrate->verbose)
			{
				return "Dropping failed_logins table...";
			}
		}
	}
}

/* End of file 004_Failed_logins.php */
/* Location: ./upload/setup/migrations/004_Failed_logins.php */ 