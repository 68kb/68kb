<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 68kb
 *
 * An open source knowledge base script
 *
 * @package		68kb
 * @author		Eric Barnes (http://ericlbarnes.com)
 * @copyright	Copyright (c) 2010, 68kb
 * @license		http://68kb.com/user_guide/license.html
 * @link		http://68kb.com
 * @since		Version 2.0
 */

// ------------------------------------------------------------------------

/**
* Gravatar Helper
*
* @subpackage   Helpers
* @author       David Cassidy
* @link			http://codeigniter.com/wiki/Gravatars/
* @Updated: 	$Date: 2010-02-06 15:00:38 -0500 (Sat, 06 Feb 2010) $
*/

/**
* Gravatar
*
* Fetches a gravatar from the Gravatar website using the specified params
*
* @access  public
* @param   string
* @param   string
* @param   integer
* @param   string
* @return  string
*/
if ( ! function_exists('gravatar'))
{
	function gravatar( $email, $rating = 'PG', $size = '80', $default = '' ) 
	{
		$email = md5($email);

		// Return the generated URL
		return "http://gravatar.com/avatar.php?gravatar_id="
			.$email."&amp;rating="
			.$rating."&amp;size="
			.$size."&amp;default="
			.$default;
	}
}
/* End of file gravatar_helper.php */
/* Location: ./upload/includes/68kb/helpers/gravatar_helper.php */ 