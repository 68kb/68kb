<?php
/*
 *---------------------------------------------------------------
 * PHP ERROR REPORTING LEVEL
 *---------------------------------------------------------------
 *
 * By default CI runs with error reporting set to ALL.  For security
 * reasons you are encouraged to change this when your site goes live.
 * For more info visit:  http://www.php.net/error_reporting
 *
 */
	error_reporting(E_ALL);
	ini_set("display_errors", 1);

/*
|---------------------------------------------------------------
| INCLUDES FOLDER NAME
|---------------------------------------------------------------
|
| This is the location of the includes folder.
|
| NO TRAILING SLASH!
|
*/
	$includes_folder = "includes";
		
/*
|---------------------------------------------------------------
| SYSTEM FOLDER NAME
|---------------------------------------------------------------
|
| This variable must contain the name of your "system" folder.
| Include the path if the folder is not in the same  directory
| as this file.
|
| NO TRAILING SLASH!
|
*/
	$system_path = "includes/system";

/*
|---------------------------------------------------------------
| APPLICATION FOLDER NAME
|---------------------------------------------------------------
|
| If you want this front controller to use a different "application"
| folder then the default one you can set its name here. The folder
| can also be renamed or relocated anywhere on your server.
| For more info please see the user guide:
| http://codeigniter.com/user_guide/general/managing_apps.html
|
|
| NO TRAILING SLASH!
|
*/
	$application_folder = "includes/68kb";

/*
|---------------------------------------------------------------
| EXTENSIONS FOLDER NAME
|---------------------------------------------------------------
|
| This is the location of the extensions folder.
|
| NO TRAILING SLASH!
|
*/
	$extensions_folder = "includes/addons";

/*
 * -------------------------------------------------------------------
 *  CUSTOM CONFIG VALUES
 * -------------------------------------------------------------------
 *
 * The $assign_to_config array below will be passed dynamically to the
 * config class when initialized. This allows you to set custom config 
 * items or override any default config values found in the config.php file.  
 * This can be handy as it permits you to share one application between
 * multiple front controller files, with each file containing different 
 * config values.
 *
 * Un-comment the $assign_to_config array below to use this feature
 *
 */
	// $assign_to_config['name_of_config_item'] = 'value of config item';



// --------------------------------------------------------------------
// END OF USER CONFIGURABLE SETTINGS.  DO NOT EDIT BELOW THIS LINE
// --------------------------------------------------------------------




/*
 * ---------------------------------------------------------------
 *  Resolve the system path for increased reliability
 * ---------------------------------------------------------------
 */
	if (function_exists('realpath') AND @realpath($system_path) !== FALSE)
	{
		$system_path = realpath($system_path).'/';
	}
	
	// ensure there's a trailing slash
	$system_path = rtrim($system_path, '/').'/';

	// Is the sytsem path correct?
	if ( ! is_dir($system_path))
	{
		exit("Your system folder path does not appear to be set correctly. Please open the following file and correct this: ".pathinfo(__FILE__, PATHINFO_BASENAME));
	}

/*
 * -------------------------------------------------------------------
 *  Now that we know the path, set the main path constants
 * -------------------------------------------------------------------
 */		
	// The name of THIS file
	define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
	
	// The PHP file extension
	define('EXT', '.php');

 	// Path to the system folder
	define('BASEPATH', str_replace("\\", "/", $system_path));
		
	// Path to the front controller (this file)
	define('FCPATH', str_replace(SELF, '', __FILE__));
	
	// Name of the "system folder"
	define('SYSDIR', end(explode('/', trim(BASEPATH, '/'))));		
	
	// Root path
	define('ROOTPATH', pathinfo(__FILE__, PATHINFO_DIRNAME).'/');
	
	// The path to the "application" folder
	if (is_dir($application_folder))
	{
		define('APPPATH', $application_folder.'/');
	}
	else
	{		
		if ( ! is_dir(BASEPATH.$application_folder.'/'))
		{
			exit("Your application folder path does not appear to be set correctly. Please open the following file and correct this: ".SELF);	
		}
	
		define('APPPATH', BASEPATH.$application_folder.'/');
	}
	
	// The path to the "includes" folder
	if (is_dir($includes_folder))
	{
		define('INCPATH', $includes_folder.'/');
	}
	else
	{		
		if ( ! is_dir($includes_folder.'/'))
		{
			exit("Your includes folder path does not appear to be set correctly. Please open the following file and correct this: ".SELF);	
		}
	
		define('INCPATH', $includes_folder.'/');
	}
	
	// The path to the "addons" folder
	if (is_dir($extensions_folder))
	{
		define('EXTPATH', $extensions_folder.'/');
	}
	else
	{		
		if ( ! is_dir($extensions_folder.'/'))
		{
			exit("Your addons folder path does not appear to be set correctly. Please open the following file and correct this: ".SELF);	
		}
	
		define('EXTPATH', $extensions_folder.'/');
	}
	
/*
 * --------------------------------------------------------------------
 * LOAD THE BOOTSTRAP FILE
 * --------------------------------------------------------------------
 *
 * And away we go...
 *
 */
require_once BASEPATH.'core/CodeIgniter'.EXT;

/* End of file index.php */
/* Location: ./index.php */