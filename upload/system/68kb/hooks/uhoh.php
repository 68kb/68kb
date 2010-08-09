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
 * Load Exceptions
 *
 * Simply loads the Exception class
 */
function load_exceptions()
{
	// Do to a wierd bug I have to get the absolute paths here.
	define('ABS_APPPATH', realpath(APPPATH) . '/');

	if(CI_VERSION >= '2.0')
	{
		// For CodeIgniter 2.0
		define('ABS_SYSDIR', realpath(SYSDIR) . '/');
		load_class('Exceptions', 'core');		
	}
	else
	{
		// For CodeIgniter 1.7.2
		define('ABS_SYSDIR', realpath(BASEPATH) . '/');
		load_class('Exceptions');
	}
}

/* End of file uhoh.php */
/* Location: ./upload/includes/68kb/hooks/uhoh.php */ 