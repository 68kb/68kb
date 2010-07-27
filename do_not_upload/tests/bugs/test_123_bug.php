<?php
class test_123_bug extends CodeIgniterUnitTestCase
{
	protected $_ci = '';

	function __construct()
	{
		parent::UnitTestCase();
	}

	function setUp()
	{

    }

    function tearDown()
	{
        //$this->CI->install_model->table_location(TRUE);
    }

	function test_issue123()
	{
		$this->assertEqual(12,12);
	}

}