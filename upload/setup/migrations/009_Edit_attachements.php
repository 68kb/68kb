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
class Edit_attachements
{
	var $msg = '';
	
	// ------------------------------------------------------------------------
	
	function up()
	{
		$CI =& get_instance();
		
		$CI->load->dbforge();
		
		if ( ! $CI->db->field_exists('attach_title', 'attachments'))
		{
			$fields = array(
				'attach_name' => array(
					'name' => 'attach_file',
					'type' => 'varchar',
					'constraint' => '55'
				),
			);
			$CI->dbforge->modify_column('attachments', $fields);
			
			$fields = array(
				'attach_title' => array(
					'type' => 'VARCHAR',
					'constraint' => '55',
				),
			);
			$CI->dbforge->add_column('attachments', $fields);
		}
		
		$this->msg = 'Altered Attachments Table.';
		return $this->msg;
	}

	// ------------------------------------------------------------------------

	function down()
	{
		
	}
}
/* End of file 010_Edit_attachements.php */
/* Location: ./upload/setup/migrations/010_Edit_attachements.php */ 