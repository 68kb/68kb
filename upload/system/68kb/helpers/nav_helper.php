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
 * Tool Tip Helper
 *
 * @subpackage	Helpers
 * @link		http://68kb.com/user_guide/
 */

// ------------------------------------------------------------------------

/**
 * Build Admin Link
 *
 * @param	string
 * @param	string
 * @param	string
 * @param	string
 *
 * @return	string
 */
function build_link($link = '', $title = '', $image = '', $class = '') 
{
	$start_link = '<a href="'.$link.'" title="'.$title.'" class="'.$class.'">';
	$img = '<img src="themes/cp/images/icons/small/'.$image.'.png" alt="'. $title .'" />'. $title;
	$end_link = '</a>';
	return $start_link . $img . $end_link; 
}

/* End of file nav_helper.php */
/* Location: ./upload/includes/68kb/helpers/nav_helper.php */ 