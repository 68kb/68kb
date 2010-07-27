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
			
			$data['cat_allowads'] = $data['category']['cat_allowads'];
			
			$data['cat_promo'] = $data['category']['cat_promo'];
			
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
			
			// Are we going to show any listings? 
			if ($data['category']['cat_allowads'] == 'yes')
			{
				$this->template->set_metadata('js', 'js/jquery-ui.min.js', 'js_include');
				$this->template->set_metadata('stylesheet', base_url() . 'themes/cp/css/smoothness/jquery-ui.css', 'link');
				
				$this->load->model('listings/listings_model');
				
				$this->load->library('pagination');
				
				$config['per_page'] = $this->settings->get_setting('site_max_search');
				$config['num_links'] = 5;
				
				if (isset($hash))
				{
					// Parse out old searchs
					$this->search_model->clean_search_results();
					
					// Confirm we have a search result
					if ( ! $search = $this->search_model->get_search($hash))
					{
						show_error(lang('lang_no_results'));
					}
					
					$search_where = unserialize(base64_decode($search['search_keywords']));
					$search_total = $search['search_total'];
					
					$config['total_rows'] = $search_total;
					$data['paging'] = $this->pagination->get_pagination($config['total_rows'], $config['per_page']);
					$offset = $this->pagination->offset;
					
					$this->db->limit($config['per_page'], $offset);
					
					$data['listings'] = $this->listings_model->get_search_results($search_where, 'listing_added', 'desc', $offset, $config['per_page']);
				}
				else
				{
					$this->search_model->remove_session();
					
					if ($this->config->item('show_sub_category_listings'))
					{
						$this->load->library('categories_library');
						$this->categories_library->clear_ids();
						$this->categories_library->get_child_ids($data['category']['cat_id']);
						$search_options['listing_category'] = $this->categories_library->get_ids();
					}
					else
					{
						$search_options['listing_category'] = $data['category']['cat_id'];
					}
					
					$config['total_rows'] = $this->listings_model->get_search_results($search_options, 'listing_added', 'desc', 0, 0, TRUE);
					$data['paging'] = $this->pagination->get_pagination($config['total_rows'], $config['per_page']);
					$offset = $this->pagination->offset;
					
					$data['listings'] = $this->listings_model->get_search_results($search_options, 'listing_added', 'desc', $offset, $config['per_page']);
				}
			}
			
			$this->template->title($data['category']['cat_name']);
		}
		else 
		{
			$data['cat_name'] = lang('lang_categories');
			$data['breadcrumb'] = $this->categories_model->breadcrumb(0);
			$data['listings'] = '';
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