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
class Searching
{
	var $msg = '';
	
	// ------------------------------------------------------------------------
	
	function up()
	{
		$CI =& get_instance();
		
		$CI->load->dbforge();
		
		if ( ! $CI->db->table_exists('search'))
		{
			$CI->dbforge->add_field("search_id varchar(32) NOT NULL default '0'");
			$CI->dbforge->add_field("search_date int(11) NOT NULL default '0'");
			$CI->dbforge->add_field("search_keywords mediumtext NOT NULL");
			$CI->dbforge->add_field("search_user_id int(11) NOT NULL default '0'");
			$CI->dbforge->add_field("search_ip varchar(16) NOT NULL default ''");
			$CI->dbforge->add_field("search_total int(6) NOT NULL default '0'");
			
			$CI->dbforge->add_key('search_id', TRUE);
			$CI->dbforge->add_key('search_date');
			
			if ($CI->dbforge->create_table('search'))
			{	
				if ($CI->migrate->verbose)
				{
					return "Added search table...";
				}
			}
			else
			{
				return 'ERROR: Adding search Table.';
			}
		}
		return $this->msg;
	}

	// ------------------------------------------------------------------------

	function down()
	{
		$CI =& get_instance();

		$CI->load->dbforge();
		
		if ($CI->dbforge->drop_table('search'))
		{
			if ($CI->migrate->verbose)
			{
				return "Dropping search table...";
			}
		}
	}
}

/* End of file 008_Searching.php */
/* Location: ./upload/setup/migrations/008_Searching.php */ 