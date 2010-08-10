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
 * Articles Parser Library
 * 
 * @subpackage	Libraries
 * @link		http://68kb.com/user_guide/tags/
 *
 */
class Articles_parser
{	
	private $_ci;
	
	private $_data = array();
	
	private $_paging = array();
	
	// ------------------------------------------------------------------------
	
	function __construct($data = array())
	{
		$this->_ci =& get_instance();
		$this->_data = $data;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Listings Helper
	*
	* @param	array
	* @return 	mixed
	*/
	function get($data = array())
	{
		// Set the default options
		$defaults = array(
			'limit' => '', 
			'author' => '', 
			'category' => '', 
			'class' => '', 
			'extra_field' => '', 
			'sort_order' => 'asc', 
			'sort_column' => 'article_title', 
			'cache' => 0, 
			'exclude' => ''
		);	
		
		$options = $this->_ci->settings->get_params($data['attributes'], $defaults);

		// Do the listings query
		$this->_ci->db->start_cache();
		$this->_ci->db->from('articles')
				->join('article_fields', 'article_id = article_field_id', 'inner');
		
		if ($options['category'] != '')
		{
			$this->_ci->db->join('article2cat', 'article_id = article_id_rel', 'right');
		}
				
		$where = 'article_display = "y"';
		
		// Call any hooks and add them to the where clause.
		if ($this->_ci->events->active_hook('get_article_where'))
		{
			$where .= $this->_ci->events->trigger('get_article_where');
		}
		
		$this->_ci->db->where($where);
		
		// Searching for a single users listings
		if ($options['author'] != '' && is_numeric($options['author']))
		{
			$this->_ci->db->where('article_author', (int) $options['author']);
		}
		
		// Searching off a listings class
		if ($options['class'] != '')
		{
			$this->_ci->db->like('listing_class', $options['class']); 
		}
		
		// Searching in a specific category
		if ($options['category'] !== '' && is_numeric($options['category'])) // Single category
		{
			$this->_ci->db->where('category_id_rel', (int) $options['category']);
		}
		elseif ($options['category'] !== '' && strpos($options['category'], ',') !== FALSE) // Passing multiple categories (1,2,3) Must have a comma as seperator
		{
			$this->_ci->db->where_in('category_id_rel', $options['category']);
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
		if (in_array(strtolower($options['sort_order']), $allowed_order_by)) 
		{
			$sort_order = $options['sort_order'];
		}
		
		// This sets the allowed order by clauses. Prevents invalid query.
		$listing_cols = $this->_ci->db->list_fields('articles');
		$fields_cols = $this->_ci->db->list_fields('article_fields');
		$allowed_sort = array_merge($listing_cols, $fields_cols);
		
		// Order By
		if (in_array($options['sort_column'], $allowed_sort))
		{
			$this->_ci->db->order_by($options['sort_column'], $sort_order); 
		}
		else
		{
			$this->_ci->db->order_by('article_title', $sort_order); 
		}
		
		// Start and stop cache to get the total
		$this->_ci->db->stop_cache();
		
		$config['total_rows'] = $this->_ci->db->count_all_results();

		$this->_ci->db->start_cache();
		
		if ($options['limit'] == '')
		{
			$this->_ci->load->library('pagination');

			$config['per_page'] = $this->_ci->settings->get_setting('site_max_search');
			$config['num_links'] = 5;
			
			$this->_paging = $this->_ci->pagination->get_pagination($config['total_rows'], $config['per_page']);
			$offset = $this->_ci->pagination->offset;
			$this->_ci->db->limit($config['per_page'], $offset);
		}
		else
		{
			$this->_ci->db->limit($options['limit']);
		}
		
		$query = $this->_ci->db->get();

		// no records so we can't continue
		if ($query->num_rows() == 0) 
		{
			$this->_ci->db->flush_cache();
			return FALSE;
		}
	
		$data = $query->result_array();
		
		// Get any other thing they may want.
		$i = 0;
		foreach ($data AS $row)
		{
			// Generate the URL
			$data[$i]['article_url'] = site_url('article/'.$row['article_uri']);
			$i++;
		}
		
		$query->free_result();
		
		$this->_ci->db->stop_cache();
		$this->_ci->db->flush_cache();
		
		return $data;
	}
	
	function paging($data = array())
	{
		return $this->_paging;
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

/* End of file Articles_parser.php */
/* Location: ./upload/system/68kb/modules/kb/libraries/Articles_parser.php */ 