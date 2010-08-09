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
 * Database Migrations
 *
 * @subpackage	Models
 * @link		http://68kb.com/user_guide/
  */
class Article_fields
{
	var $msg = '';
	
	// ------------------------------------------------------------------------
	
	function up()
	{
		$CI =& get_instance();
		
		$CI->load->dbforge();
		
		if ( ! $CI->db->table_exists('article_fields'))
		{
			$fields = array(
					'article_field_id' => array('type' => 'INT','constraint' => 11,'unsigned' => TRUE,'auto_increment' => TRUE),
			);
			$CI->dbforge->add_field($fields);
			
			$CI->dbforge->add_key('article_field_id', TRUE);
			
			if ($CI->dbforge->create_table('article_fields'))
			{	
				if ($CI->migrate->verbose)
				{
					$this->msg .= "Added article_fields table...<br />";
				}
			}
			else
			{
				$this->msg = 'ERROR: Adding article_fields Table.';
			}
		}
		return $this->msg;
	}

	// ------------------------------------------------------------------------

	function down()
	{
		$CI =& get_instance();

		$CI->load->dbforge();

		if ($CI->dbforge->drop_table('article_fields'))
		{
			if ($CI->migrate->verbose)
			{
				return "Dropping article_fields table...";
			}
		}
	}
}

/* End of file 009_Article_fields.php */
/* Location: ./upload/setup/migrations/009_Article_fields.php */ 