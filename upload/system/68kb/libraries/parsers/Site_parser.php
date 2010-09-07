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
 * Site Parser Library
 *
 * @subpackage	Libraries
 * @link		http://iclassengine.com/user_guide/tags/
 *
 */
class Site_parser
{
	private $_ci;

	private $_data = array();

	// ------------------------------------------------------------------------

	function __construct($data = array())
	{
		$this->_ci =& get_instance();
		$this->_data = $data;
	}

	// ------------------------------------------------------------------------

	/**
	 * Build a site link
	 *
	 * Wrapper for site_url function to generate page links
	 *
	 * @param 	array
	 * @return 	string
	 */
	public function link($data = array())
	{
		$page = (isset($data['attributes']['page'])) ? $data['attributes']['page'] : '';

		return site_url($page).'/';
	}
}

/* End of file Site_parser.php */
/* Location: ./upload/system/68kb/libraries/parsers/Site_parser.php */