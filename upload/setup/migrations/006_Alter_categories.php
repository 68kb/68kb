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
class Alter_categories
{
	var $msg = '';
	
	// ------------------------------------------------------------------------
	
	function up()
	{
		$CI =& get_instance();
		
		$CI->load->dbforge();
		
		if ($CI->db->field_exists('article_id', 'article2cat'))
		{
			$fields = array(
				'article_id' => array(
					'name' => 'article_id_rel',
					'type' => 'int',
					'constraint' => '20'
				),
			);
			$CI->dbforge->modify_column('article2cat', $fields);
			
			$fields = array(
				'category_id' => array(
					'name' => 'category_id_rel',
					'type' => 'int',
					'constraint' => '20'
				),
			);
			$CI->dbforge->modify_column('article2cat', $fields);
		}
		
		if ( ! $CI->db->field_exists('cat_image', 'categories'))
		{
			// Get all the users
			$CI->db->from('categories');
			$query = $CI->db->get();

			// Drop the table
			$CI->dbforge->drop_table('categories');

			// Now recreate it
			$fields = array(
				'cat_id' => array('type' => 'INT','constraint' => 11,'unsigned' => TRUE,'auto_increment' => TRUE),
			);
			$CI->dbforge->add_field($fields);
			$CI->dbforge->add_field("cat_parent int(11) NOT NULL default '0'");
			$CI->dbforge->add_field("cat_uri varchar(255) NOT NULL default '0'");
			$CI->dbforge->add_field("cat_name varchar(55) NOT NULL default ''");
			$CI->dbforge->add_field("cat_keywords varchar(55) NOT NULL default ''");
			$CI->dbforge->add_field("cat_image varchar(55) NOT NULL");
			$CI->dbforge->add_field("cat_description text NOT NULL");
			$CI->dbforge->add_field("cat_allowads ENUM('no','yes') NOT NULL DEFAULT 'yes'");
			$CI->dbforge->add_field("cat_display ENUM('no','yes') NOT NULL DEFAULT 'yes'");
			$CI->dbforge->add_field("cat_order int(11) NOT NULL default '0'");
			$CI->dbforge->add_field("cat_promo text NOT NULL");
			$CI->dbforge->add_field("cat_views int(11) NOT NULL default '0'");
			
			$CI->dbforge->add_key('cat_id', TRUE);
			$CI->dbforge->add_key('cat_uri', TRUE);
			$CI->dbforge->add_key('cat_name');
			$CI->dbforge->add_key('cat_parent');
			$CI->dbforge->add_key('cat_order');
			$CI->dbforge->add_key('cat_display');
			
			$CI->dbforge->create_table('categories');

			if ($query->num_rows() > 0) 
			{
				foreach ($query->result_array() as $row)
				{
					$data['cat_parent'] = $row['cat_parent'];
					$data['cat_uri'] = $row['cat_uri'];
					$data['cat_name'] = $row['cat_name'];
					$data['cat_description'] = $row['cat_description'];
					$display = 'no';
					if ($row['cat_display'] == 1)
					{
						$display = 'yes';
					}
					$data['cat_display'] = $display;
					$data['cat_order'] = $row['cat_order'];

					$this->db->insert('categories', $data);
				}
			}

			$query->free_result();
		}

		
		$this->msg = 'Altered Categories Table.';
		return $this->msg;
	}

	// ------------------------------------------------------------------------

	function down()
	{
		
	}
}

/* End of file 005_Alter_settings.php */
/* Location: ./upload/setup/migrations/005_Alter_settings.php */