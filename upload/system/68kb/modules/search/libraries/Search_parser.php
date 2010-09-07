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
 * Search Model
 *
 * @subpackage	Models
 * @link		http://68kb.com/user_guide/
 *
 */
class Search_parser
{
	private $_ci;

	private $_data = array();

	function __construct($data = array())
	{
		$this->_ci =& get_instance();
		$this->_data = $data;
	}

	// ------------------------------------------------------------------------

	/**
	 * Get search form
	 *
	 * @subpackage	Helpers
	 * @param	int
	 * @return 	string
	 */
	function form($data = '')
	{
		// Set the default options
		$defaults = array(
			'show_categories' 	=> 'no',
			'class'			=> 'search_form',
			'id'			=> '',
			'style'			=> ''
		);

		$options = $this->_ci->settings->get_params($data['attributes'], $defaults);

		$this->_ci->load->helper('form');

		// Should we load up the categories
		$cats = '';
		if ($options['show_categories'] == 'yes')
		{
			$this->_ci->load->library('categories/categories_library');
			$cats = $this->_ci->categories_model->get_categories();
			$this->_ci->categories_library->category_tree($cats);
			$cats = $this->_ci->categories_library->get_categories();

			$cat_options['0'] = lang('lang_search_all');
			foreach($cats as $row)
			{
				$indent = ($row['cat_parent'] != 0) ? repeater('&nbsp;&raquo;&nbsp;', $row['depth']) : '';
				$cat_options[$row['cat_id']] = $indent.$row['cat_name'];
			}
			$cats = form_dropdown('category', $cat_options, 'id="cats" class="category"');
		}

		$attributes = array('class' => $options['class'], 'id' => $options['id'], 'style' => $options['style']);

		$output = form_open('search/do_search', $attributes);

		$output .= str_replace('{kb:cats}', $cats, $data['content']);

		$output .= form_close();

		return $output;
	}
}

/* End of file Search_parser.php */
/* Location: ./upload/system/68kb/modules/search/libraries/Search_parser.php */