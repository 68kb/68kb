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
 * Themes Parser Library
 *
 * @subpackage	Libraries
 * @link		http://68kb.com/user_guide/
 *
 */
class Themes_parser
{
	private $_ci;

	private $_theme;

	public function __construct()
	{
		$this->_ci =& get_instance();
		$this->_theme = $this->_ci->settings->get_setting('site_theme');
	}

	// ------------------------------------------------------------------------

	/**
	* Load a view file
	*
	* @param	array
	* @return 	string
	*/
	public function embed($data)
	{
		if ($file = $this->_get_param('file', $data))
		{
			$this->_ci->template->set_layout('');
			return $this->_ci->template->load_view($file);
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Add CSS File
	 *
	 * Adds a css file relative to your path
	 *
	 * @param	array
	 * @return 	string
	 */
	public function css($data = array())
	{
		if ($file = $this->_get_param('file', $data))
		{
			if (file_exists('themes/'.$this->_theme.'/'.$file))
			{
				return '<link rel="stylesheet" href="'.base_url().'themes/'.$this->_theme.'/'.$file.'" type="text/css" />';
			}
		}
	}


	public function body($data = array())
	{
		return $this->_ci->template->get_body();
	}


	// ------------------------------------------------------------------------

	/**
	 * Get a single param
	 *
	 * @param	string  - The array key
	 * @return	mixed 	- The value
	 */
	private function _get_param($key, $data)
	{
		if (isset($data['attributes'][$key]))
		{
			return $data['attributes'][$key];
		}
		return FALSE;
	}
}

/* End of file Themes_parser.php */
/* Location: ./upload/system/68kb/modules/themes/libraries/Themes_parser.php */