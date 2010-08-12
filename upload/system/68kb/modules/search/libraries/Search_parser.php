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
			$cats = form_dropdown('category', $cat_options, 'class="category"');
		}
		
		$attributes = array('class' => $options['class'], 'id' => $options['id'], 'style' => $options['style']);
		
		$output = form_open('search/do_search', $attributes);
		
		$output .= str_replace('{kb:cats}', $cats, $data['content']);
		
		$output .= form_close();
		
		return $output;
		
		
		
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Listings Helper
	*
	* @param	array
	* @return 	mixed
	*/
	function get()
	{
		$this->_ci->benchmark->mark('get_listings_start');
		
		// Set the default options
		$defaults = array('limit' => 10, 'owner' => '', 'category' => '', 'class' => '', 'extra_field' => '', 'sort_order' => 'random', 'sort_column' => 'listing_title', 'cache' => 0, 'exclude' => '');	
		
		$options = $this->_ci->settings->get_params($this->_data['attributes'], $defaults);
		
		// Do the listings query
		$this->_ci->db->from('listings')
				->join('listing_status', 'listing_status = status_id', 'inner')
				->join('listing_fields', 'listing_id = listing_field_id', 'inner')
				->join('users', 'user_id = listing_owner_id', 'left');
		
		$where = 'status_show_listing = "y" AND listing_expiration > '. time();
		
		// Call any hooks and add them to the where clause.
		if ($this->_ci->events->active_hook('get_listings_where'))
		{
			$where .= $this->_ci->events->trigger('get_listings_where');
		}
		
		$this->_ci->db->where($where);
		
		// Searching for a single users listings
		if ($options['owner'] != '' && is_numeric($options['owner']))
		{
			$this->_ci->db->where('listing_owner_id', (int) $options['owner']);
		}
		
		// Searching off a listings class
		if ($options['class'] != '')
		{
			$this->_ci->db->like('listing_class', $options['class']); 
		}
		
		// Searching in a specific category
		if ($options['category'] !== '' && is_numeric($options['category'])) // Single category
		{
			$this->_ci->db->where('listing_category', (int) $options['category']);
		}
		elseif ($options['category'] !== '' && strpos($options['category'], ',') !== FALSE) // Passing multiple categories (1,2,3) Must have a comma as seperator
		{
			$this->_ci->db->where_in('listing_category', $options['category']);
		}
		
		// Check for excluded listings
		if ($options['exclude'] != '')
		{
			$exclude = explode(',', $options['exclude']);
			$this->_ci->db->where_not_in('listing_id', $exclude);
		}
		
		// Extra Field Searching
		if ($options['extra_field'] !== '' && strpos($options['extra_field'], '|') !== FALSE) // Must have a pipe as a seperator
		{
			$extra_field = explode('|', $options['extra_field']);
			
			// See if they used the field name or the table name
			if (strpos($extra_field[0], 'extra_field_') === FALSE)
			{
				$extra_field[0] = 'extra_field_'.$extra_field[0];
			}
			
			$this->_ci->db->where($extra_field[0], $extra_field[1]);
		}
		
		// Allowed order by. This prevents some one from using something to produce an invalid query.
		$allowed_order_by = array('asc', 'desc', 'random');
		
		$sort_order = 'asc';
		if ( ! in_array(strtolower($options['sort_order']), $allowed_order_by)) 
		{
			$sort_order = 'desc';
		}
		
		// This sets the allowed order by clauses. Prevents invalid query.
		$listing_cols = $this->_ci->db->list_fields('listings');
		$fields_cols = $this->_ci->db->list_fields('listing_fields');
		$allowed_sort = array_merge($listing_cols, $fields_cols);
		
		// Order By
		if (in_array($options['sort_column'], $allowed_sort))
		{
			$this->_ci->db->order_by($options['sort_column'], $sort_order); 
			$this->_ci->db->limit($options['limit']);
		}
		else
		{
			$this->_ci->db->order_by('listing_title', $sort_order); 
			$this->_ci->db->limit($options['limit']);
		}
		
		$query = $this->_ci->db->get();
		
		// Log the query in case some one wants to see it.
		log_message('debug', 'Listing Plugin Query: '. $this->_ci->db->last_query());
		
		// no records so we can't continue
		if ($query->num_rows() == 0) 
		{
			return array();
		}
	
		$data = $query->result_array();
		
		// load img helper
		$this->_ci->load->helper('html');
		
		// Get any other thing they may want.
		$i = 0;
		foreach ($data AS $row)
		{
			$this->_ci->db->select('image_thumb')->from('listings_images')->where('image_listing_id', $row['listing_id'])->limit(1);
			$img_query = $this->_ci->db->get();
			
			if ($img_query->num_rows() > 0) 
			{
				$img_data = $img_query->row_array();

				$image_properties = array(
					'src' => $this->_ci->config->item('listing_image_path') . $img_data['image_thumb'],
					'alt' => $row['listing_title'],
					'class' => 'listing_image',
				);
			}
			else
			{
				$image_properties = array(
					'src' => 'themes/'.$this->_ci->settings->get_setting('site_theme') . '/images/nophoto.gif',
					'alt' => $row['listing_title'],
					'class' => 'listing_image',
				);
			}
			
			$data[$i]['listing_images'] = $img_query->num_rows();
			$data[$i]['listing_upload_image'] = img($image_properties); 
			$img_query->free_result();
			
			// Generate the URL
			$data[$i]['listing_url'] = site_url('listings/'.$row['listing_id'].'/'.$row['listing_uri']);
			$i++;
		}
		
		$query->free_result();
		
		$this->_ci->benchmark->mark('get_listings_end');
		
		return $data;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Get a single param
	 *
	 * @param	string  - The array key
	 * @return	mixed 	- The value
	 */
	private function _get_param($key)
	{
		if (isset($this->_data['attributes'][$key])) 
		{
			return $this->_data['attributes'][$key];
		}
		return FALSE;
	}
}

/* End of file Search_parser.php */
/* Location: ./upload/system/68kb/modules/search/libraries/Search_parser.php */ 