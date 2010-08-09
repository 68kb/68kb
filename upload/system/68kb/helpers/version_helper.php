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
 * Version Helpers
 *
 * @subpackage	Helpers
 * @link		http://68kb.com/user_guide/
 */

// ------------------------------------------------------------------------

/**
 * Checks for the latest release
 *
 * @return 	string
 */
function version_check()
{
	$fp = @fsockopen("www.68kb.com", 80, $errno, $errstr, 30);
	
	$return = '';
	
	if ( ! $fp) 
	{
	    echo "$errstr ($errno)<br />\n";
	} 
	else 
	{
	    $out = "GET /downloads/latest/version.txt HTTP/1.1\r\n";
	    $out .= "Host: www.68kb.com\r\n";
	    $out .= "Connection: Close\r\n\r\n";

	    fwrite($fp, $out);
	
	    while ( ! feof($fp)) 
	    {
	        $return .= fgets($fp, 128);
	    }
	
		// Get rid of HTTP headers
		$content = explode("\r\n\r\n", $return);
		$content = explode($content[0], $return);

		// Assign version to var
		$version = trim($content[1]);
		
	    fclose($fp);
	
		return $version;
	}
}
/* End of file version_helper.php */
/* Location: ./upload/includes/68kb/helpers/version_helper.php */ 