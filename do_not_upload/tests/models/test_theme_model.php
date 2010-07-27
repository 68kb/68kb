<?php
class test_theme_model extends CodeIgniterUnitTestCase
{
	protected $_ci = '';
	
	function __construct()
	{
		parent::__construct();
		$this->UnitTestCase('Theme Model');

		$this->_ci->load->model('themes/theme_model');
	}

	function setUp()
	{
		$this->_ci->db->flush_cache();
    }

    function tearDown()
	{
		
    }
	
	public function test_included()
	{
		$this->assertTrue(class_exists('theme_model'));
	}
	
	public function test_activate()
	{
		$var = $this->_ci->theme_model->activate('default');
		$this->assertTrue($var);
	}
	
}