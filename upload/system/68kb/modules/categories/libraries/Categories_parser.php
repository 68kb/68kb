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
 * Categories Parser Library
 * 
 * @subpackage	Libraries
 * @link		http://68kb.com/user_guide/tags/
 *
 */
class Categories_parser
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
	 * Generate a category select list
	 *
	 * @param	string - The field name
	 */
	public function ice_category_select($data = array())
	{
		$name = $data['attributes']['name'];
		
		$this->_ci->load->library('categories/categories_library');
		$this->_ci->load->model('categories/categories_model');
		
		$cats = $this->_ci->categories_model->get_categories();
		$this->_ci->categories_library->category_tree($cats);
		
		$cats = $this->_ci->categories_library->get_categories();
		
		$cat_options['0'] = lang('lang_search_all');
		foreach($cats as $row)
		{
			$indent = ($row['cat_parent'] != 0) ? repeater('&nbsp;&raquo;&nbsp;', $row['depth']) : '';
			$cat_options[$row['cat_id']] = $indent.$row['cat_name']; 
		}	
		return form_dropdown($name, $cat_options, '', 'class="category_select"');
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Make categories list
	 *
	 * Recursive format categories in a list
	 *
	 * @link 	http://iclassengine.com/user_guide/tags/ice_categories_cat_list
	 * @param	string
	 * @return	string
	 */
	function cat_list($data = '')
	{
		// Set the default options
		$defaults = array('cat_parent' => 0, 'sort_column' => '', 'sort_order' => 'desc', 'depth' => 0, 'show_image' => 0, 'show_total' => 0, 'exclude' => '', 'cache' => 0);
		
		$options = $this->_ci->settings->get_params($data['attributes'], $defaults);
		
		$this->_ci->load->library('categories/categories_library');
		$this->_ci->load->model('categories/categories_model');
		
		if ($options['cache'] > 0 OR ! $cats = $this->_ci->cache->get('ice_category_list_'.implode('_', $options)))
		{
			$this->_ci->db->select('cat_id,cat_name,cat_allowads,cat_display,cat_parent,cat_uri')
						->from('categories')
						->where('cat_display', 'yes');
		
			if (is_numeric($options['cat_parent']) && $options['cat_parent'] > 0)
			{
				$this->_ci->db->where('cat_parent', (int) $options['cat_parent']);
			}
		
			if ($options['exclude'] != '')
			{
				$exclude = explode(',', $options['exclude']);
				$this->_ci->db->where_not_in('cat_id', $exclude);
			}
		
			// Allowed order by. This prevents some one from using something to produce an invalid query.
			$allowed_order_by = array('asc', 'desc');
		
			if ( ! in_array(strtolower($options['sort_order']), $allowed_order_by)) 
			{
				$sort_order = 'desc';
			}
			else
			{
				$sort_order = $options['sort_order'];
			}
		
			// This sets the allowed order by clauses. Prevents invalid query.
			$allowed_sort = $this->_ci->db->list_fields('categories');
		
			if (in_array($options['sort_column'], $allowed_sort))
			{
				$this->_ci->db->order_by($options['sort_column'], $sort_order); 
			}
			else
			{
				$this->_ci->db->orderby('cat_order', 'DESC')->orderby('cat_parent', 'asc')->orderby('cat_name', 'asc');
			}
		
			$query = $this->_ci->db->get();
		
			if ($query->num_rows() == 0) 
			{
				
				return FALSE;
			} 
		
			$cats = $query->result_array();
			$this->_ci->cache->write($cats, 'ice_category_list_'.implode('_', $options), $options['cache']);
			$query->free_result();
		}
		
		$i = 0;
		foreach ($cats as $row)
		{
			if ($options['show_total'] == 1)
			{
				$this->_ci->benchmark->mark('cat_pi_total_listings_start');
				$total = $this->_ci->cache->model('categories_model', 'total_listings', array($row['cat_id']), $options['cache']);
				$cats[$i]['total'] = ' <span class="total">('.$total.')</span>';
				$this->_ci->benchmark->mark('cat_pi_total_listings_end');
			}
			else
			{
				$cats[$i]['total'] = '';
			}
			$i++;
		}
		
		$options['module'] = 'categories';
		
		$list = $this->_ci->categories_library->make_list($cats, $options);
		
		return $list;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Categories Table
	 *
	 * @param	array
	 * @return 	mixed
	 * @link 	http://iclassengine.com/user_guide/tags/ice_categories_table
	 */
	function table($data = '')
	{
		// Set the default options
		$defaults = array(
				'cat_parent' 		=> 0, 
				'sort_column'		=> 'cat_order', 
				'sort_order' 		=> 'desc', 
				'show_total' 		=> 'no', 
				'show_image' 		=> 'yes', 
				'show_description'	=> 'no', 
				'cols' 				=> 2, 
				'table_attr' 		=> 'width="100%" class="cat_table"', 
				'row_start' 		=> '', 
				'row_alt_start' 	=> '', 
				'cell_start' 		=> '', 
				'cell_alt_start' 	=> '',
				'trail_pad'			=> '&nbsp;'
		);	
		
		$options = $this->_ci->settings->get_params($data['attributes'], $defaults);
		
		$this->_ci->load->model('categories/categories_model');
		$this->_ci->load->helper('html');
		
		// setup table template
		$tmpl = array (
			'table_open'          => '<table '. $options['table_attr'] .'>',
			'row_start'           => '<tr '. $options['row_start'] .'>',
			'row_end'             => '</tr>',
			'cell_start'          => '<td '. $options['cell_start'] .'>',
			'cell_end'            => '</td>',
			'row_alt_start'       => '<tr '. $options['row_alt_start'] .'>',
			'row_alt_end'         => '</tr>',
			'cell_alt_start'      => '<td '. $options['cell_alt_start'] .'>',
			'cell_alt_end'        => '</td>',
			'table_close'         => '</table>'
		);
	
		// Load the table library
		$this->_ci->load->library('table');

		// Set the template as defined above.
		$this->_ci->table->set_template($tmpl);
	
		$this->_ci->table->set_empty($options['trail_pad']); 

		// Make the columns
		$this->_ci->db->select('cat_id,cat_uri,cat_name,cat_image,cat_description')
					->from('categories')
					->where('cat_parent', (int) $options['cat_parent'])
					->where('cat_display', 'yes');	

		// Allowed order by. This prevents some one from using something to produce an invalid query.
		$allowed_order_by = array('asc', 'desc');

		if ( ! in_array(strtolower($options['sort_order']), $allowed_order_by)) 
		{
			$sort_order = 'desc';
		}
		else
		{
			$sort_order = $options['sort_order'];
		}

		// This sets the allowed order by clauses. Prevents invalid query.
		$allowed_sort = $this->_ci->db->list_fields('categories');

		if (in_array($options['sort_column'], $allowed_sort))
		{
			$this->_ci->db->order_by($options['sort_column'], $sort_order); 
		}
		else
		{
			$this->_ci->db->orderby('cat_order', 'DESC')->orderby('cat_parent', 'asc')->orderby('cat_name', 'asc');
		}
		
		$query = $this->_ci->db->get();
		
		$cats = array();
		
		if ($query->num_rows() == 0)
		{
			return FALSE;
		}
	
		foreach ($query->result_array() as $row)
		{
			$td = '';
			
			// Show the category image.
			if ($options['show_image'] == 'yes')
			{
				$this->_ci->benchmark->mark('cat_pi_show_images_start');
				if ($row['cat_image'] <> '')
				{
					$image_properties = array(
						'src' => $this->_ci->config->item('cat_image_path') . $row['cat_image'],
						'alt' => $row['cat_name'],
						'class' => 'cat_image '. $row['cat_uri'],
						'title' => $row['cat_name'],
					);
				}
				else
				{
					$image_properties = array(
						'src' => 'themes/'.$this->_ci->settings->get_setting('site_theme') . '/images/folder.gif',
						'alt' => $row['cat_name'],
						'class' => 'cat_image '. $row['cat_uri'],
						'title' => $row['cat_name'],
					);
				}
			
				$td .= img($image_properties) .'&nbsp;'; 
				$this->_ci->benchmark->mark('cat_pi_show_images_end');
			}
		
			$td .= '<a class="cat_name" href="'.site_url('categories/'.$row['cat_uri']).'">'.$row['cat_name'].'</a>';
			
			// Show the total listings in this category and all children
			if ($options['show_total'] == 'yes')
			{
				$this->_ci->benchmark->mark('cat_pi_total_listings_start');
				$total = $this->_ci->categories_model->total_listings($row['cat_id']);
				$td .= ' <span class="total">('.$total.')</span>';
				$this->_ci->benchmark->mark('cat_pi_total_listings_end');
			}
			
			// Show the description
			if ($options['show_description'] == 'yes') 
			{
				$td .= '<br />'. $row['cat_description'];
			}
			
			// Make it into an array
			$td_arr = array($td);
	
			// Now merge this with others
			$cats = array_merge($cats, $td_arr);
		}
	
		$new_list = $this->_ci->table->make_columns($cats, $options['cols']);

 		$table = $this->_ci->table->generate($new_list);

		// finally clear the template incase it is used twice.
		$this->_ci->table->clear();
		
		return $table;
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

/* End of file Categories_parser.php */
/* Location: ./upload/system/68kb/modules/categories/libraries/Categories_parser.php */ 