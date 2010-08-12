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
class User_notes
{
	var $msg = '';
	
	// ------------------------------------------------------------------------
	
	function up()
	{
		$CI =& get_instance();
		
		$CI->load->dbforge();
		
		if ( ! $CI->db->table_exists('user_notes'))
		{
			$fields = array(
					'note_id' => array('type' => 'INT','constraint' => 11,'unsigned' => TRUE,'auto_increment' => TRUE),
			);
			$CI->dbforge->add_field($fields);
			
			$CI->dbforge->add_field("note_user_id int(11) NOT NULL default '0'");
			$CI->dbforge->add_field("note_added_by int(11) NOT NULL default '0'");
			$CI->dbforge->add_field("note_date int(11) NOT NULL default '0'");
			$CI->dbforge->add_field("note mediumtext NOT NULL");
			$CI->dbforge->add_field("note_important char(1) NOT NULL default 'n'");
			$CI->dbforge->add_field("note_show_user char(1) NOT NULL default 'n'");
			
			$CI->dbforge->add_key('note_id', TRUE);
			$CI->dbforge->add_key('note_user_id');
			$CI->dbforge->add_key('note_show_user');
			
			if ($CI->dbforge->create_table('user_notes'))
			{	
				if ($CI->migrate->verbose)
				{
					return "Added user_notes table...";
				}
			}
			else
			{
				return 'ERROR: Adding user_notes Table.';
			}
		}
		return $this->msg;
	}

	// ------------------------------------------------------------------------

	function down()
	{
		$CI =& get_instance();

		$CI->load->dbforge();
		
		if ($CI->dbforge->drop_table('user_notes'))
		{
			if ($CI->migrate->verbose)
			{
				return "Dropping user_notes table...";
			}
		}
	}
}

/* End of file 009_User_notes.php */
/* Location: ./upload/setup/migrations/009_User_notes.php */