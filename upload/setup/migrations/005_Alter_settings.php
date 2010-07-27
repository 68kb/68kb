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
class Alter_settings
{
	var $msg = '';
	
	// ------------------------------------------------------------------------
	
	function up()
	{
		$CI =& get_instance();
		
		$CI->load->dbforge();
		
		$CI->dbforge->drop_table('settings');
		
		if ( ! $CI->db->table_exists('settings'))
		{
			$fields = array(
					'option_id' => array('type' => 'INT','constraint' => 11,'unsigned' => TRUE,'auto_increment' => TRUE),
			);
			$CI->dbforge->add_field($fields);
			$CI->dbforge->add_field("option_name varchar(64) NOT NULL DEFAULT ''");
			$CI->dbforge->add_field("option_value mediumtext NOT NULL");
			$CI->dbforge->add_field("option_group varchar(55) NOT NULL DEFAULT 'site'");
			$CI->dbforge->add_field("auto_load ENUM('no','yes') NOT NULL DEFAULT 'yes'");
			
			$CI->dbforge->add_key('option_id', TRUE);
			$CI->dbforge->add_key('option_name', TRUE);
			$CI->dbforge->add_key('option_name');
			$CI->dbforge->add_key('auto_load');
			
			$CI->dbforge->create_table('settings');

			$data = array('option_name' => 'site_name','option_value' => 'Your Site', 'option_group' => 'site', 'auto_load' => 'yes');
			$CI->db->insert('settings', $data);
			$data = array('option_name' => 'site_email','option_value' => 'demo@demo.com', 'option_group' => 'site', 'auto_load' => 'yes');
			$CI->db->insert('settings', $data);
			$data = array('option_name' => 'site_keywords','option_value' => 'keywords, go, here', 'option_group' => 'site', 'auto_load' => 'yes');
			$CI->db->insert('settings', $data);
			$data = array('option_name' => 'site_description','option_value' => 'Site Description', 'option_group' => 'site', 'auto_load' => 'yes');
			$CI->db->insert('settings', $data);
			$data = array('option_name' => 'site_max_search','option_value' => '20', 'option_group' => 'site', 'auto_load' => 'yes');
			$CI->db->insert('settings', $data);
			$data = array('option_name' => 'site_cache_time','option_value' => '0', 'option_group' => 'site', 'auto_load' => 'yes');
			$CI->db->insert('settings', $data);
			$data = array('option_name' => 'site_theme','option_value' => 'default', 'option_group' => 'site', 'auto_load' => 'yes');
			$CI->db->insert('settings', $data);
			$data = array('option_name' => 'site_admin_template','option_value' => 'default', 'option_group' => 'site', 'auto_load' => 'yes');
			$CI->db->insert('settings', $data);
			$data = array('option_name' => 'site_bad_words','option_value' => "", 'option_group' => 'site', 'auto_load' => 'no');
			$CI->db->insert('settings', $data);
			$data = array('option_name' => 'script_version','option_value' => '', 'option_group' => 'script', 'auto_load' => 'yes');
			$CI->db->insert('settings', $data);
			$data = array('option_name' => 'script_build','option_value' => '', 'option_group' => 'script', 'auto_load' => 'yes');
			$CI->db->insert('settings', $data);
			$data = array('option_name' => 'script_db_version','option_value' => "", 'option_group' => 'script', 'auto_load' => 'yes');
			$CI->db->insert('settings', $data);
			$data = array('option_name' => 'script_latest','option_value' => '0', 'option_group' => 'script', 'auto_load' => 'yes');
			$CI->db->insert('settings', $data);
			$data = array('option_name' => 'script_last_cron','option_value' => "", 'option_group' => 'script', 'auto_load' => 'yes');
			$CI->db->insert('settings', $data);
			
			return 'settings table installed...<br />';
		}
		
		$this->msg = 'Altered Settings Table.';
		return $this->msg;
	}

	// ------------------------------------------------------------------------

	function down()
	{
		
	}
}

/* End of file 005_Alter_settings.php */
/* Location: ./upload/setup/migrations/005_Alter_settings.php */ 