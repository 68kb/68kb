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
class Create_settings
{
	// ------------------------------------------------------------------------

	function up()
	{
		$CI =& get_instance();

		if ($CI->migrate->verbose)
		{
			$msg = "";
		}

		$class_methods = get_class_methods($this);
		foreach ($class_methods as $method_name)
		{
			if(substr($method_name, 0, 6) == 'table_')
			{
				$msg .= $this->$method_name();
			}
		}

		return $msg;
	}

	// ------------------------------------------------------------------------

	function down()
	{
		$CI =& get_instance();

		$CI->load->dbforge();

		if ($CI->migrate->verbose)
		{
			echo "Dropping core tables...";
		}
		$tables = $CI->db->list_tables();
		foreach($tables as $table)
		{
			$table = str_replace($CI->db->dbprefix, '', $table);
			if ($CI->dbforge->drop_table($table))
			{
				echo 'Dropping table '. $table.'<br />';
			}
		}
		return TRUE;
	}

	// ------------------------------------------------------------------------
	
	/**
	* Install Articles Table
	*/
	function table_articlestocat()
	{
		$CI =& get_instance();

		if ( ! $CI->db->table_exists('article2cat'))
		{
			$CI->dbforge->add_field("article_id int(20) default NULL");
			$CI->dbforge->add_field("category_id int(20) default NULL");
			
			$CI->dbforge->add_key('article_id', TRUE);
			$CI->dbforge->add_key('category_id', TRUE);
			if ($CI->dbforge->create_table('article2cat'))
			{
				return 'article2cat table installed...<br />';
			}
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Install Articles Table
	*/
	function table_articles()
	{
		$CI =& get_instance();

		if ( ! $CI->db->table_exists('articles'))
		{
			$fields = array(
					'article_id' => array('type' => 'INT','constraint' => 11,'unsigned' => TRUE,'auto_increment' => TRUE),
			);
			$CI->dbforge->add_field($fields);
			$CI->dbforge->add_field("article_uri varchar(55) NOT NULL default '0'");
			$CI->dbforge->add_field("article_title varchar(255) NOT NULL default ''");
			$CI->dbforge->add_field("article_keywords varchar(255) NOT NULL default ''");
			$CI->dbforge->add_field("article_description text NOT NULL");
			$CI->dbforge->add_field("article_short_desc text NOT NULL");
			$CI->dbforge->add_field("article_date int(11) NOT NULL default '0'");
			$CI->dbforge->add_field("article_modified int(11) NOT NULL default '0'");
			$CI->dbforge->add_field("article_display char(1) NOT NULL default 'N'");
			$CI->dbforge->add_field("article_hits int(11) NOT NULL default '0'");
			$CI->dbforge->add_field("article_author int(11) NOT NULL default '0'");
			$CI->dbforge->add_field("article_order int(11) NOT NULL default '0'");
			$CI->dbforge->add_field("article_rating int(11) NOT NULL default '0'");
			$CI->dbforge->add_key('article_id', TRUE);
			$CI->dbforge->add_key('article_uri', TRUE);
			$CI->dbforge->add_key('article_title', TRUE);
			if($CI->dbforge->create_table('articles'))
			{
				return 'articles table installed...<br />';
			}
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Install Articles Tags Table
	*/
	function table_article_tags()
	{
		$CI =& get_instance();

		if ( ! $CI->db->table_exists('article_tags'))
		{
			$CI->dbforge->add_field("tags_tag_id int(11) NOT NULL default '0'");
			$CI->dbforge->add_field("tags_article_id int(11) NOT NULL default '0'");
			$CI->dbforge->add_key('tags_tag_id', TRUE);
			if($CI->dbforge->create_table('article_tags'))
			{
				return 'article_tags table installed...<br />';
			}
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Install Attachments Table
	*/
	function table_attachments()
	{
		$CI =& get_instance();

		if ( ! $CI->db->table_exists('attachments'))
		{
			$fields = array(
					'attach_id' => array('type' => 'INT','constraint' => 11,'unsigned' => TRUE,'auto_increment' => TRUE),
			);
			$CI->dbforge->add_field($fields);
			$CI->dbforge->add_field("article_id int(11) NOT NULL default '0'");
			$CI->dbforge->add_field("attach_name varchar(55) NOT NULL default ''");
			$CI->dbforge->add_field("attach_type varchar(55) NOT NULL default ''");
			$CI->dbforge->add_field("attach_size varchar(55) NOT NULL default ''");
			$CI->dbforge->add_key('attach_id', TRUE);
			if($CI->dbforge->create_table('attachments'))
			{
				return 'attachments table installed...<br />';
			}
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Install Captcha Table
	*/
	function table_captcha()
	{
		$CI =& get_instance();

		if ( ! $CI->db->table_exists('captcha'))
		{
			$fields = array(
					'captcha_id' => array('type' => 'INT','constraint' => 11,'unsigned' => TRUE,'auto_increment' => TRUE),
			);
			$CI->dbforge->add_field($fields);
			$CI->dbforge->add_field("captcha_time int(10) NOT NULL default '0'");
			$CI->dbforge->add_field("ip_address varchar(16) NOT NULL default '0'");
			$CI->dbforge->add_field("word varchar(20) NOT NULL default ''");
			$CI->dbforge->add_field("a_size varchar(255) NOT NULL default ''");
			$CI->dbforge->add_key('captcha_id', TRUE);
			$CI->dbforge->add_key('word');
			if($CI->dbforge->create_table('captcha'))
			{
				return 'captcha table installed...<br />';
			}
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Install Categories
	*/
	function table_categories()
	{
		$CI =& get_instance();
		
		if ( ! $CI->db->table_exists('categories'))
		{
			$fields = array(
					'cat_id' => array('type' => 'INT','constraint' => 11,'unsigned' => TRUE,'auto_increment' => TRUE),
			);
			$CI->dbforge->add_field($fields);
			$CI->dbforge->add_field("cat_parent int(11) NOT NULL default '0'");
			$CI->dbforge->add_field("cat_uri varchar(55) NOT NULL default '0'");
			$CI->dbforge->add_field("cat_name varchar(255) NOT NULL default ''");
			$CI->dbforge->add_field("cat_description text NOT NULL");
			$CI->dbforge->add_field("cat_display char(1) NOT NULL DEFAULT 'N'");
			$CI->dbforge->add_field("cat_order int(11) NOT NULL default '0'");
			$CI->dbforge->add_key('cat_id', TRUE);
			$CI->dbforge->add_key('cat_uri', TRUE);
			$CI->dbforge->add_key('cat_name');
			$CI->dbforge->add_key('cat_parent');
			$CI->dbforge->add_key('cat_order');
			if($CI->dbforge->create_table('categories'))
			{
				return 'categories table installed...<br />';
			}
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Install Comments Table
	*/
	function table_comments()
	{
		$CI =& get_instance();
		
		if ( ! $CI->db->table_exists('comments'))
		{
			$fields = array(
					'comment_ID' => array('type' => 'INT','constraint' => 11,'unsigned' => TRUE,'auto_increment' => TRUE),
			);
			$CI->dbforge->add_field($fields);
			$CI->dbforge->add_field("comment_article_ID int(11) NOT NULL default '0'");
			$CI->dbforge->add_field("comment_author varchar(55) NOT NULL default ''");
			$CI->dbforge->add_field("comment_author_email varchar(55) NOT NULL default ''");
			$CI->dbforge->add_field("comment_author_IP varchar(16) NOT NULL default ''");
			$CI->dbforge->add_field("comment_date int(16) NOT NULL default '0'");
			$CI->dbforge->add_field("comment_content text NOT NULL");
			$CI->dbforge->add_field("comment_approved enum('0','1','spam') NOT NULL default '1'");
			$CI->dbforge->add_key('comment_ID', TRUE);
			$CI->dbforge->add_key('comment_article_ID');
			if($CI->dbforge->create_table('comments'))
			{
				return 'comments table installed...<br />';
			}
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Install Glossary Table
	*/
	function table_glossary()
	{
		$CI =& get_instance();
		
		if ( ! $CI->db->table_exists('glossary'))
		{
			$fields = array(
					'g_id' => array('type' => 'INT','constraint' => 11,'unsigned' => TRUE,'auto_increment' => TRUE),
			);
			$CI->dbforge->add_field($fields);
			$CI->dbforge->add_field("g_term varchar(55) NOT NULL default ''");
	        $CI->dbforge->add_field("g_definition text NOT NULL");
			$CI->dbforge->add_key('g_id', TRUE);
			$CI->dbforge->add_key('g_term');
			if($CI->dbforge->create_table('glossary'))
			{
				return 'glossary table installed...<br />';
			}
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Install Modules Table
	*/
	function table_modules()
	{
		$CI =& get_instance();
		
		if ($CI->db->table_exists('modules'))
		{
			$CI->dbforge->drop_table('modules');
		}
		
		
		if ( ! $CI->db->table_exists('modules'))
		{
			$fields = array(
				'module_id' => array('type' => 'INT','constraint' => 11,'unsigned' => TRUE,'auto_increment' => TRUE),
			);
			$CI->dbforge->add_field($fields);
			$CI->dbforge->add_field("module_name varchar(64) NOT NULL DEFAULT ''");
			$CI->dbforge->add_field("module_display_name varchar(64) NOT NULL DEFAULT ''");
			$CI->dbforge->add_field("module_description longtext NOT NULL");
			$CI->dbforge->add_field("module_directory varchar(64) NOT NULL DEFAULT ''");
			$CI->dbforge->add_field("module_version varchar(10) NOT NULL DEFAULT ''");
			$CI->dbforge->add_field("module_active ENUM('no','yes') NOT NULL DEFAULT 'yes'");
			$CI->dbforge->add_field("module_order int(11) NOT NULL DEFAULT '100'");
			$CI->dbforge->add_key('module_id', TRUE);
			$CI->dbforge->add_key('module_name', TRUE);
			$CI->dbforge->add_key('module_name');
			$CI->dbforge->add_key('module_active');
			$CI->dbforge->create_table('modules');
		}
	}
	
	/**
	* Install Search Log Table
	*/
	function table_searchlog()
	{
		$CI =& get_instance();
		
		if ($CI->db->table_exists('searchlog'))
		{
			$CI->dbforge->drop_table('searchlog');
		}
		
		if ( ! $CI->db->table_exists('searchlog'))
		{
			$fields = array(
					'searchlog_id' => array('type' => 'INT','constraint' => 11,'unsigned' => TRUE,'auto_increment' => TRUE),
			);
			$CI->dbforge->add_field($fields);
			
			$CI->dbforge->add_field("searchlog_term varchar(55) NOT NULL default ''");
			$CI->dbforge->add_field("searchlog_date int(11) NOT NULL default '0'");
			$CI->dbforge->add_field("searchlog_user_id int(11) NOT NULL default '0'");
			$CI->dbforge->add_field("searchlog_ip varchar(16) NOT NULL default ''");
			
	        $CI->dbforge->add_key('searchlog_id', TRUE);
			if($CI->dbforge->create_table('searchlog'))
			{
				return 'searchlog table installed...<br />';
			}
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Install Sessions Table
	*/
	function table_sessions()
	{
		$CI =& get_instance();
		
		if ( ! $CI->db->table_exists('sessions'))
		{
			$fields = array(
					'session_id' => array('type' => 'INT','constraint' => 11,'unsigned' => TRUE,'auto_increment' => TRUE),
			);
			$CI->dbforge->add_field($fields);
			$CI->dbforge->add_field("ip_address varchar(16) NOT NULL default '0'");
			$CI->dbforge->add_field("user_agent varchar(50) NOT NULL default ''");
			$CI->dbforge->add_field("last_activity int(10) NOT NULL default '0'");
	        $CI->dbforge->add_key('session_id', TRUE);
			if($CI->dbforge->create_table('sessions'))
			{
				return 'sessions table installed...<br />';
			}
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Install Settings table
	*/
	function table_settings()
	{
		$CI =& get_instance();
		
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
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Install Tags Table
	*/
	function table_tags()
	{
		$CI =& get_instance();
		
		if ( ! $CI->db->table_exists('tags'))
		{
			$fields = array(
					'id' => array('type' => 'INT','constraint' => 11,'unsigned' => TRUE,'auto_increment' => TRUE),
			);
			$CI->dbforge->add_field($fields);
			$CI->dbforge->add_field("tag varchar(30) NOT NULL default '0'");
	        $CI->dbforge->add_key('id', TRUE);
			if($CI->dbforge->create_table('tags'))
			{
				return 'tags table installed...<br />';
			}
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Install Users Table
	*/
	function table_users()
	{
		$CI =& get_instance();
		
		if ( ! $CI->db->table_exists('users'))
		{
			$fields = array(
					'id' => array('type' => 'INT','constraint' => 11,'unsigned' => TRUE,'auto_increment' => TRUE),
			);
			$CI->dbforge->add_field($fields);
			$CI->dbforge->add_field("custip varchar(16) NOT NULL default '0'");
			$CI->dbforge->add_field("firstname varchar(50) NOT NULL default ''");
			$CI->dbforge->add_field("lastname varchar(50) NOT NULL default ''");
			$CI->dbforge->add_field("email varchar(50) NOT NULL default ''");
			$CI->dbforge->add_field("username varchar(50) NOT NULL default ''");
			$CI->dbforge->add_field("password varchar(50) NOT NULL default ''");
			$CI->dbforge->add_field("joindate int(11) NOT NULL default '0'");
			$CI->dbforge->add_field("lastlogin int(11) NOT NULL default '0'");
			$CI->dbforge->add_field("cookie varchar(50) NOT NULL default ''");
			$CI->dbforge->add_field("session varchar(50) NOT NULL default ''");
			$CI->dbforge->add_field("level int(5) NOT NULL default '5'");
	        $CI->dbforge->add_key('id', TRUE);
			if($CI->dbforge->create_table('users'))
			{
				return 'users table installed...<br />';
			}
		}
	}
}
/* End of file 001_Create_settings.php */
/* Location: ./upload/setup/migrations/001_Create_settings.php */ 