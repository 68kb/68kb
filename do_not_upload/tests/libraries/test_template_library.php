<?php
class test_template_library extends CodeIgniterUnitTestCase
{
	protected $_ci = '';

	function __construct()
	{
		parent::__construct();

		$this->UnitTestCase('Template Library');
	}

	function setUp()
	{

    }

    function tearDown()
	{
		
    }

	function test_template_title()
	{
		$var = $this->_ci->template->title('Test');
		$this->assertPattern('/Test |/', $var);
	}
	
}