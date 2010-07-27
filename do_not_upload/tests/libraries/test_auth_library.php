<?php
class test_auth_library extends CodeIgniterUnitTestCase
{
	protected $_ci = '';
	
	var $user = '';
	
	function __construct()
	{
		parent::__construct();
		
		$this->UnitTestCase('Auth Library');
		$this->_ci->load->library('users/users_auth');
		$this->_ci->load->model('users/users_model');
		
		$this->_ci->db->truncate('users'); 
		
		$insert_data = array(
			    'user_email' => 'demo@demo.com',
			    'user_username' => 'demo',
			    'user_password' => 'demo',
			    'user_join_date' => time(),
				'user_group'	=> 1
			);
		$user_id = $this->_ci->users_model->add_user($insert_data);
	}

	function setUp()
	{
		$this->user = $this->_ci->users_model->get_user(1);
    }

    function tearDown()
	{
        
    }

	public function test_included()
	{
		$this->assertTrue(class_exists('users_auth'));
		$this->assertTrue(class_exists('users_model'));
	}
	
	public function test_logged_in()
	{
		$this->_ci->db->flush_cache();
		$var = $this->_ci->users_auth->logged_in();
		$this->assertFalse($var);
	}
	
	function test_check_role()
	{
		$this->_ci->db->flush_cache();
		$var = $this->_ci->users_auth->check_role('can_access_admin');
		$this->assertFalse($var);
	}
	
	function test_login()
	{
		$this->_ci->db->flush_cache();
		$var = $this->_ci->users_auth->login($this->user['user_username'], $this->user['user_password']);
		$this->assertTrue($var);
	}
	
	function test_failed_login()
	{
		$this->_ci->db->flush_cache();
		$var = $this->_ci->users_auth->login('test', 'dummypass');
		$this->assertEqual($var, lang('lang_user_login_error'));
	}
	
	function test_banned_login()
	{
		$this->_ci->db->flush_cache();
		$insert_data = array(
			    'user_email' => 'banned@demo.com',
			    'user_username' => 'banned',
			    'user_password' => 'user',
			    'user_join_date' => time(),
				'user_group'	=> 4
			);
		$this->_ci->users_model->add_user($insert_data);
		$this->_ci->db->flush_cache();
		$var = $this->_ci->users_auth->login('banned', 'user');
		
		$this->assertEqual($var, lang('lang_user_banned'), lang('lang_user_banned'));
	}
	
	function test_pending_login()
	{
		$this->_ci->db->flush_cache();
		$insert_data = array(
			    'user_email' => 'pending@demo.com',
			    'user_username' => 'pending',
			    'user_password' => 'user',
			    'user_join_date' => time(),
				'user_group'	=> 3
			);
		$this->_ci->users_model->add_user($insert_data);
		$this->_ci->db->flush_cache();
		$var = $this->_ci->users_auth->login('pending', 'user');
		// $this->dump($this->_ci->db->last_query());
		$this->assertEqual($var, lang('lang_group_pending'), lang('lang_group_pending'));
	}
	
}