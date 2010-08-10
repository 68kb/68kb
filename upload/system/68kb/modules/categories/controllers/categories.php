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
 * Index Controller
 *
 *
 * @subpackage	Controllers
 * @link		http://68kb.com/user_guide/
 *
 */
class Categories extends Front_Controller 
{
	private $_fields_active = FALSE;
	
	/**
	 * Constructor
	 *
	 * @return 	void
	 */
	public function __construct() 
	{
		parent::__construct();
		
		log_message('debug', 'Categories Controller Initialized');
		
		// Load the categories model
		$this->load->model('categories_model');
		$this->load->model('search/search_model');
		$this->load->helper('form');
		
		if ($this->events->active('fields'))
		{
			$this->_fields_active = TRUE;
			$this->load->model('fields/fields_model');
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Index File
	*ˇ
	*/
	public function index($uri='') 
	{
		$this->events->trigger('categories_controller');
		
		$this->benchmark->mark('category_segments_start');
		// Get an array of all segments
		$segs = $this->uri->segment_array();
		
		// Remove the module
		array_shift($segs);
		
		$uri = array();
		foreach($segs AS $val)
		{
			if (is_numeric($val) OR strpos($val, 'page') !== FALSE)
			{
				continue;
			}
			if (strlen($val) == 32)
			{
				$hash = $val; 
				continue;
			}
			$uri[] = $val;
		}
		$uri = implode('/', $uri);
		
		$this->benchmark->mark('category_segments_end');
		
		$data['category_id'] = 0;
		
		// Set parent breadcrumb
		$this->template->set_breadcrumb(lang('lang_categories'), 'categories');
		
		if ($uri <> '' && $uri <> 'index') 
		{
			$uri = $this->security->xss_clean($uri);
			
			$data['category'] = $this->categories_model->get_cat($uri);
			
			// Be sure we have some data to work with
			if ( ! $data['category'])
			{
				redirect('categories');
			}
			
			// Assign out the cat details
			$data['cat_name'] = $data['category']['cat_name'];
			
			$data['category_id'] = $data['category']['cat_id'];
			
			$this->load->model('kb/articles_model');
			$data['has_articles'] = $this->articles_model->get_articles_by_catid($data['category_id']);
			
			$data['cat_description'] = $data['category']['cat_description'];
			
			$data['sub_cats'] = $this->categories_model->get_sub_categories($data['category_id']);
			
			if ($this->_fields_active)
			{
				$data['fields'] = $this->fields_model->get_fields_for_category($data['category_id']);
			}
			
			// Breadcrumbs
			$crumbs = $this->cache->model('categories_model', 'breadcrumb', array($data['category_id']), 3600); // keep for 1 hour
			foreach ($crumbs as $value) 
			{
				$this->template->set_breadcrumb($value['cat_name'], 'categories/'.$value['cat_uri']);
			}
			
			$this->template->title($data['category']['cat_name']);
		}
		else 
		{
			$this->template->title(lang('lang_categories'));
			$data['cat_name'] = lang('lang_categories');
			$data['breadcrumb'] = $this->categories_model->breadcrumb(0);
			$data['has_articles'] = 0;
			$data['cat_allowads'] = 'no';
		}
		
		$this->_show_page($data);
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Show a page based off uri
	 *
	 * @param	array Data
	 */
	private function _show_page($data)
	{
		$uri = '';
		if (isset($data['category']['cat_uri']))
		{
			$uri = $data['category']['cat_uri'];
		}
		
		$theme = $this->settings->get_setting('site_theme');
		if ($theme && file_exists(ROOTPATH . 'themes/' . $theme .'/categories/'. $uri . EXT))
		{
			$this->template->build($uri, $data);
		}
		else
		{
			$this->template->build('browse', $data);
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Remap
	*
	* Redirect everything to index
	*
	* @link http://codeigniter.com/user_guide/general/controllers.html#remapping
	* @access	private
	* @param	string	the unique uri
	* @return	array
	*/
	public function _remap($method)
	{
		$this->index($method);	
	}

}
/* End of file categories.php */
/* Location: ./upload/includes/68kb/modules/categories/controllers/categories.php */ 