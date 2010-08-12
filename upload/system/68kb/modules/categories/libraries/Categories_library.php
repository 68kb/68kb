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
 * Categories Library
 * 
 *
 * @subpackage	Libraries
 * @link		http://68kb.com/user_guide/
 *
 */
class Categories_library
{
	protected $_ci; 
	
	protected $_categories = array();
	
	protected $_cat_ids = array();
	
	protected $_all_cats = array();
	
	
	/**
	 * Constructor
	 */
	function __construct() 
	{
		log_message('debug', 'Categories Library Initialized');
		$this->_ci = CI_Base::get_instance();
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Category Tree
	 *
	 * Get categories in an array
	 *
	 * @uses category_subtree
	 */
	public function category_tree($data = array(), $id = 'cat_id', $parent = 'cat_parent')
	{
		if ( ! is_array($data) OR count($data) == 0)
		{
			return FALSE;
		}
		
		foreach ($data as $row)
		{
			// This assigns all the select fields to the array.
			foreach ($row AS $key => $val)
			{
				$arr[$key] = $val;
			}
			
			$menu_array[$row[$id]] = $arr;
		}
		
		unset($arr);
		
		foreach($menu_array as $key => $val)
		{
			if (0 == $val[$parent])
			{
				$depth = 0;
				foreach ($val AS $the_key => $the_val)
				{
					$arr[$the_key] = $the_val;
				}
				$this->_categories[$key] = $arr;
				$this->_category_subtree($key, $menu_array, $depth);
			}
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Category Sub Tree
	 *
	 * Gets all the child categories for a parent.
	 *
	 * @param	int
	 * @param	array
	 * @param	int
	 */
	private function _category_subtree($cat_id, $cat_array, $depth)
	{
		$catarray = array();

		$depth++;

		foreach ($cat_array as $key => $val)
		{
			if ($cat_id == $val['cat_parent'])
			{
				foreach ($val AS $the_key => $the_val)
				{
					$arr[$the_key] = $the_val;
				}
				$arr = array_merge($arr, array('depth' => $depth));
				
				$this->_categories[$key] = $arr;
				
				$this->_category_subtree($key, $cat_array, $depth);
			}
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Make a list of categories
	 *
	 * @link 	http://codeigniter.com/forums/viewreply/720661/
	 * @param	array 	Array of data
	 */
	function make_list($data, $options = array())
	{
		if (count($data) == 0)
		{
			return FALSE;
		}
		
		$menu_data = array(
			'items' => array(),
			'parents' => array()
		);
		
		foreach ($data as $menu_item)
		{
			$menu_data['items'][$menu_item['cat_id']] = $menu_item;
			$menu_data['parents'][$menu_item['cat_parent']][] = $menu_item['cat_id'];
		}
		
		$parent_id = 0;
		
		if (isset($options['parent_id']))
		{
			$parent_id = $options['parent_id'];
		}
		
		// Here we setup the bench mark just to be sure this doesn't runaway.
		$this->_ci->benchmark->mark('walk_categories_start');
		
		$cats = $this->_walk_categories($parent_id, $menu_data, $options);
		
		$this->_ci->benchmark->mark('walk_categories_end');
		
		return $cats;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Walk though the array
	 * 
	 * @param	int 	Parent id
	 * @param	array 	Array of data
	 */
	private function _walk_categories($parent_id, $menu_data, $options, $start = 0)
	{
	    $output = '';
		
		$depth = (int) $options['depth'];
		
		if (($depth == 0 OR $depth > $start) && isset($menu_data['parents'][$parent_id]))
	    {
	    	if ($start == 0)
			{
				$output = "<ul class='category_menu categories top'>\n";
			}
			else
			{
				$output = "<ul class='categories sub'>\n";
			}
			
			$start++;
			
	        foreach ($menu_data['parents'][$parent_id] as $item)
			{
				extract($menu_data['items'][$item]);
				
				$parent = ($cat_parent == 0) ? 'top' : 'child child_'.$cat_parent;
				$total = (isset($total) ? $total : '');
				$module = (isset($options['module']) ? $options['module'].'/' : 'categories/');
				
				$output .= '<li class="cat_'.$cat_id.' '.$parent.' category">'.anchor($module.$cat_uri, '<span>'.$cat_name.'</span>') . $total; 
				
				// find child items recursively
				$output .= $this->_walk_categories($item, $menu_data, $options, $start);

				$output .= '</li>'."\n";
			}
			$output .= '</ul>';
		}

		return $output;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Get Child Category Ids
	 *
	 * Get a list of all child categories in an array
	 *
	 * @uses _child_subtree
	 */
	public function get_child_ids($parent = 0)
	{
		// First we get all the categories and store it.
		if (empty($this->_all_cats))
		{
			$this->_ci->db->select('cat_id,cat_parent')
						->from('categories')
						->where('cat_display', 'yes');
			$query = $this->_ci->db->get();

			if ($query->num_rows() == 0) 
			{
				return FALSE;
			} 

			$this->_all_cats = $query->result_array();
		}
		
		foreach ($this->_all_cats as $row)
		{
			// This assigns all the fields to the array.
			foreach ($row AS $key => $val)
			{
				$arr[$key] = $val;
			}
			
			$menu_array[$row['cat_id']] = $arr;
		}
		
		unset($arr);
		
		foreach($menu_array as $key => $val)
		{
			// Now add any children.
			if ($parent == $val['cat_parent'] OR $parent == $val['cat_id'])
			{
				$depth = 0;
				if ( ! in_array($val['cat_id'], $this->_cat_ids, TRUE)) 
				{
					$this->_cat_ids = array_merge(array($val['cat_id']),$this->_cat_ids);
					$this->_child_subtree($key, $menu_array, $depth);
				}
				
			}
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Generate the child ids for the parent.
	 *
	 * This is used by get_child_ids to recursively find all the child categories.
	 *
	 * @param	int 	cat id
	 * @param	array 	categories
	 * @param	int		depth
	 */
	private function _child_subtree($cat_id, $cat_array, $depth)
	{
		$catarray = array();

		$depth++;

		foreach ($cat_array as $key => $val)
		{
			if ($cat_id == $val['cat_parent'])
			{
				// The following if statement is added to try and prevent the same category twice.
				if ( ! in_array($val['cat_id'], $this->_cat_ids, TRUE)) 
				{
					foreach ($val AS $the_key => $the_val)
					{
						$arr[$the_key] = $the_val;
					}

					$arr = array_merge($arr, array('depth' => $depth));

					$this->_cat_ids = array_merge(array($val['cat_id']),$this->_cat_ids);

					$this->_child_subtree($key, $cat_array, $depth);
				}
			}
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Get Categories
	 *
	 * Get the full array of categories
	 *
	 * @return 	array
	 */
	public function get_categories()
	{
		return $this->_categories;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Get ids
	 *
	 * Get the array of category ids
	 *
	 * @return 	array
	 */
	public function get_ids()
	{
		return $this->_cat_ids;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Clear ids
	 *
	 * Clear out the category ids so they do not overlap.
	 *
	 */
	public function clear_ids()
	{
		unset($this->_cat_ids);
		$this->_cat_ids = array();
	}
}
/* End of file Categories_library.php */
/* Location: ./upload/includes/68kb/modules/categories/libraries/Categories_library.php */ 