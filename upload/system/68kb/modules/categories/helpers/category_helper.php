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
 * Make a categories table
 *
 * @link 	http://68kb.com/user_guide/helpers/ice_category_table
 * @param	string
 * @return	string
 */
if ( ! function_exists('ice_category_table'))
{
	function ice_category_table($params = '')
	{
		$CI =& get_instance();
		
		// Set the default options
		$defaults = array(
				'cat_parent' 		=> 0, 
				'sort_column'		=> 'cat_order', 
				'sort_order' 		=> 'desc', 
				'show_total' 		=> 0, 
				'show_image' 		=> 0, 
				'show_description'	=> 0, 
				'cols' 				=> 2, 
				'table_attr' 		=> 'width="100%" class="cat_table"', 
				'row_start' 		=> '', 
				'row_alt_start' 	=> '', 
				'cell_start' 		=> '', 
				'cell_alt_start' 	=> '',
				'trail_pad'			=> '&nbsp;'
		);	
		
		$options = $CI->settings->parse_params($params, $defaults);
		
		$CI->load->model('categories/categories_model');
		
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
		$CI->load->library('table');

		// Set the template as defined above.
		$CI->table->set_template($tmpl);
	
		$CI->table->set_empty($options['trail_pad']); 

		// Make the columns
		$CI->db->select('cat_id,cat_uri,cat_name,cat_image,cat_description')
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
		$allowed_sort = $CI->db->list_fields('categories');

		if (in_array($options['sort_column'], $allowed_sort))
		{
			$CI->db->order_by($options['sort_column'], $sort_order); 
		}
		else
		{
			$CI->db->orderby('cat_order', 'DESC')->orderby('cat_parent', 'asc')->orderby('cat_name', 'asc');
		}
		
		$query = $CI->db->get();
		
		// echo $CI->db->last_query();
		
		$cats = array();
		
		if ($query->num_rows() == 0)
		{
			return FALSE;
		}
	
		foreach ($query->result_array() as $row)
		{
			$td = '';
			
			// Show the category image.
			if ($options['show_image'] == 1)
			{
				$CI->benchmark->mark('cat_pi_show_images_start');
				if ($row['cat_image'] <> '')
				{
					$image_properties = array(
						'src' => $CI->config->item('cat_image_path') . $row['cat_image'],
						'alt' => $row['cat_name'],
						'class' => 'cat_image '. $row['cat_uri'],
						'title' => $row['cat_name'],
					);
				}
				else
				{
					$image_properties = array(
						'src' => 'themes/'.$CI->settings->get_setting('site_theme') . '/images/folder.gif',
						'alt' => $row['cat_name'],
						'class' => 'cat_image '. $row['cat_uri'],
						'title' => $row['cat_name'],
					);
				}
			
				$td .= img($image_properties) .'&nbsp;'; 
				$CI->benchmark->mark('cat_pi_show_images_end');
			}
		
			$td .= '<a class="cat_name" href="'.site_url('categories/'.$row['cat_uri']).'">'.$row['cat_name'].'</a>';
			
			// Show the total listings in this category and all children
			if ($options['show_total'] == 1)
			{
				$CI->benchmark->mark('cat_pi_total_listings_start');
				$total = $CI->categories_model->total_listings($row['cat_id']);
				$td .= ' <span class="total">('.$total.')</span>';
				$CI->benchmark->mark('cat_pi_total_listings_end');
			}
			
			// Show the description
			if ($options['show_description'] == 1) 
			{
				$td .= '<br />'. $row['cat_description'];
			}
			
			// Make it into an array
			$td_arr = array($td);
	
			// Now merge this with others
			$cats = array_merge($cats, $td_arr);
		}
	
		$new_list = $CI->table->make_columns($cats, $options['cols']);
		
		// echo it to the browser
		echo $CI->table->generate($new_list);

		// finally clear the template incase it is used twice.
		$CI->table->clear();
	}
}

// ------------------------------------------------------------------------

/**
 * Make categories list
 *
 * Recursive format categories in a list
 *
 * @link 	http://68kb.com/user_guide/helpers/ice_category_list
 * @param	string
 * @return	string
 */
if ( ! function_exists('ice_category_list'))
{
	function ice_category_list($params = '')
	{
		// Set the default options
		$defaults = array('cat_parent' => 0, 'sort_column' => '', 'sort_order' => 'desc', 'depth' => 0, 'echo' => 1, 'show_image' => 0, 'show_total' => 0, 'exclude' => '', 'cache' => 0);
		
		$CI =& get_instance();
		
		$options = $CI->settings->parse_params($params, $defaults);
		
		$CI->load->library('categories/categories_library');
		$CI->load->model('categories/categories_model');
		
		if ($options['cache'] > 0 OR ! $cats = $CI->cache->get('ice_category_list_'.implode('_', $options)))
		{
			$CI->db->select('cat_id,cat_name,cat_allowads,cat_display,cat_parent,cat_uri')
						->from('categories')
						->where('cat_display', 'yes');
		
			if (is_numeric($options['cat_parent']) && $options['cat_parent'] > 0)
			{
				$CI->db->where('cat_parent', (int) $options['cat_parent']);
			}
		
			if ($options['exclude'] != '')
			{
				$exclude = explode(',', $options['exclude']);
				$CI->db->where_not_in('cat_id', $exclude);
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
			$allowed_sort = $CI->db->list_fields('categories');
		
			if (in_array($options['sort_column'], $allowed_sort))
			{
				$CI->db->order_by($options['sort_column'], $sort_order); 
			}
			else
			{
				$CI->db->orderby('cat_order', 'DESC')->orderby('cat_parent', 'asc')->orderby('cat_name', 'asc');
			}
		
			$query = $CI->db->get();
		
			if ($query->num_rows() == 0) 
			{
				
				return FALSE;
			} 
		
			$cats = $query->result_array();
			$CI->cache->write($cats, 'ice_category_list_'.implode('_', $options), $options['cache']);
			$query->free_result();
		}
		
		$i = 0;
		foreach ($cats as $row)
		{
			if ($options['show_total'] == 1)
			{
				$CI->benchmark->mark('cat_pi_total_listings_start');
				$total = $CI->cache->model('categories_model', 'total_listings', array($row['cat_id']), $options['cache']);
				$cats[$i]['total'] = ' <span class="total">('.$total.')</span>';
				$CI->benchmark->mark('cat_pi_total_listings_end');
			}
			else
			{
				$cats[$i]['total'] = '';
			}
			$i++;
		}
		
		$options['module'] = 'categories';
		
		$list = $CI->categories_library->make_list($cats, $options);
		
		if ($options['echo'] == 1)
		{
			echo $list;
		}
		else
		{
			return $list;
		}
	}
}

/**
 * Generate a category select list
 *
 * @param	string - The field name
 */
if ( ! function_exists('ice_category_select'))
{
	function ice_category_select($name = 'category')
	{
		$CI =& get_instance();
		
		
		$CI->load->library('categories/categories_library');
		$CI->load->model('categories/categories_model');
		
		$cats = $CI->categories_model->get_categories();
		$CI->categories_library->category_tree($cats);
		
		$cats = $CI->categories_library->get_categories();
		
		$cat_options['0'] = lang('lang_search_all');
		foreach($cats as $row)
		{
			$indent = ($row['cat_parent'] != 0) ? repeater('&nbsp;&raquo;&nbsp;', $row['depth']) : '';
			$cat_options[$row['cat_id']] = $indent.$row['cat_name']; 
		}	
		echo form_dropdown($name, $cat_options, '', 'class="category_select"');
	}
}
/* End of file category_helper.php */
/* Location: ./upload/includes/68kb/modules/categories/helpers/category_helper.php */ 