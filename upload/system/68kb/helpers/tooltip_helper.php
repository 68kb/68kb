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
 * Tool Tip Helper
 *
 * @subpackage	Helpers
 * @link		http://68kb.com/user_guide/
 */

// ------------------------------------------------------------------------

/**
 * Create a tooltip
 *
 *
 * @param	string
 *
 * @return	string
 */
function tooltip($title = '') 
{
	if ($title == '')
	{
		return FALSE;
	}
	
	$template_path = base_url() . 'themes/cp/';
	
	$start_link = '<a href="javascript:void(0);" title="'.$title.'" class="tooltip">';
	$img = '<img src="'.$template_path.'images/icons/small/info.png" border="0" alt="'. lang('lang_info') .'" />';
	$end_link = '</a>';
	
	return $start_link . $img . $end_link; 
}

/* End of file tooltip_helper.php */
/* Location: ./upload/includes/68kb/helpers/tooltip_helper.php */ 