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
 * Search Controller
 *
 * @subpackage	Controllers
 * @link		http://68kb.com/user_guide/
 *
 */
class Search extends Front_Controller 
{
	
	/**
	 * Constructor
	 *
	 * @return 	void
	 */
	public function __construct() 
	{
		parent::__construct();
		
		log_message('debug', 'Search Controller Initialized');
		
		// Load the needed libs
		$this->load->model('search_model');
		$this->load->model('categories/categories_model');
		$this->load->model('kb/articles_model');
		$this->load->helper(array('form', 'url', 'html', 'date'));
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Show the search form.
	 */
	public function index() 
	{
		$this->events->trigger('search/index');
		
		$this->benchmark->mark('get_cats_for_select_start');
		
		$this->load->library('categories/categories_library');
		$cats = $this->categories_model->get_categories();
		$this->categories_library->category_tree($cats);
		$data['cats'] = $this->categories_library->get_categories();
		
		$this->benchmark->mark('get_cats_for_select_end');
		
		// Set parent breadcrumb
		$this->template->set_breadcrumb(lang('lang_search'), 'search');
		
		$this->template->build('search', $data);
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Perform the search and store it.
	 */
	public function do_search()
	{
		$this->load->model('kb/articles_model');
		
		$this->load->library('pagination');
		
		if ( ! $this->users_auth->check_role('can_search')) 
		{
			$this->session->set_flashdata('error', lang('lang_search_not_allowed'));
			redirect('search/no_results');
		}
		
		$options = array();
		
		// Setup the categories first.
		if ($category = $this->input->post('category'))
		{
			$category = (int) $category;
			
			// Set up the parent cat. 
			$options['category'] = $category;
			
			// Now any children
			$this->load->library('categories/categories_library');
			$this->categories_library->clear_ids();
			$this->categories_library->get_child_ids($category);
			$search_cats = $this->categories_library->get_ids();
			
			if ( ! empty($search_cats))
			{
				$options['article_category'] = $search_cats;
			}
		}
		
		// Now keywords
		if ($keywords = $this->input->post('keywords'))
		{
			$options['keywords'] = $keywords;
		}

		// If hash is false we have nothing to show them.
		if ( ! $hash = $this->search_model->filter_listings($options, $category))
		{
			$this->session->set_flashdata('error', lang('lang_no_results'));
			redirect('search/no_results');
		}
		
		// Bing Bang Done.. Off to search results we go!
		redirect('search/results/'.$hash);
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Filter results from category page.
	 */
	public function do_filter()
	{	
		// Make sure we have a category
		if ( ! $category = $this->input->post('category'))
		{
			$this->session->set_flashdata('error', lang('lang_no_results'));
			redirect('search/no_results');
		}
		
		// Make sure we have some fields or else no filtering can be done.
		if ($this->events->active('fields'))
		{
			$this->load->model('fields/fields_model');
			if ( ! $this->fields_model->get_fields_for_category($category))
			{
				$this->session->set_flashdata('error', lang('lang_no_results'));
				redirect('search/no_results');
			}
		}

		// Remove any previous filter session data
		$this->search_model->remove_session();
		
		// Here we assign the whole post global. 
		// This is secured in the filter listings 
		// because it checks the posted data matches
		// searchable fields.
		$options = $_POST;
		
		if ( ! $hash = $hash = $this->search_model->filter_listings($options, $category))
		{
			$this->session->set_flashdata('error', lang('lang_no_results'));
			redirect('search/no_results');
		}
		
		// Get the category uri
		$this->load->model('categories/categories_model');
		if ( ! $cat_data = $this->categories_model->get_cat($category))
		{
			$this->session->set_flashdata('error', 'Category not found');
			redirect('search/no_results');
		}
		
		$uri = $cat_data['cat_uri'];

		redirect('categories/'.$uri.'/'.$hash);
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Show the search results. 
	 * 
	 * Must have a valid md5 hash. 
	 */
	public function results($hash = '')
	{
		// Confirm the hash is 32 chars long
		if (strlen($hash) != 32)
		{
			$this->session->set_flashdata('message', 'Hash Error');
			redirect('search/no_results');
		}
		
		// Parse out old searchs
		$this->search_model->clean_search_results();
		
		// Confirm we have a search result
		if ( ! $search = $this->search_model->get_search($hash))
		{
			$this->session->set_flashdata('error', lang('lang_no_results'));
			redirect('search/no_results');
		}

		$search_where = unserialize(base64_decode($search['search_keywords']));
		$search_total = $search['search_total'];
		
		// Setup pagination
		$this->load->library('pagination');
		$config['total_rows'] = $search_total;
		$config['per_page'] = $this->settings->get_setting('site_max_search');
		$config['num_links'] = 5;
		$data['paging'] = $this->pagination->get_pagination($config['total_rows'], $config['per_page']);
		$offset = $this->pagination->offset;
		
		// Do the query
		$this->db->from('articles')
				->join('article_fields', 'article_id = article_field_id', 'inner')
				->where($search_where)
				->limit($config['per_page'], $offset);
				
		$query = $this->db->get();
		
		if ($query->num_rows() == 0) 
		{
			return FALSE;
		}
		
		$data['articles'] = $query->result_array();
		// Get any other thing they may want.
		$i = 0;
		foreach ($data['articles'] AS $row)
		{
			// Generate the URL
			$data['articles'][$i]['article_url'] = site_url('article/'.$row['article_uri']);
			$i++;
		}
		
		$this->template->set_breadcrumb(lang('lang_search'), 'search');
		
		// Finally pass it to the template.
		$this->template->build('results', $data);
	}
	
	// ------------------------------------------------------------------------
	
	public function no_results()
	{
		// Set parent breadcrumb
		$this->template->set_breadcrumb(lang('lang_search'), 'search');
		
		$data = array();
		if ($this->session->flashdata('message'))
		{
			$this->session->keep_flashdata('message');
			$data['message'] = $this->session->flashdata('message');
			$data['link'] = site_url('search');
			$data['link_text'] = 'test';
		}
		elseif ($this->session->flashdata('error'))
		{
			$this->session->keep_flashdata('error');
			$data['message'] = $this->session->flashdata('error');
			$data['link'] = 'javascript:history.go(-1)';
			$data['link_text'] = lang('lang_search_again');
		}
		
		$this->template->build('message', $data);
	}
}
/* End of file search.php */
/* Location: ./upload/includes/68kb/modules/search/controllers/search.php */ 