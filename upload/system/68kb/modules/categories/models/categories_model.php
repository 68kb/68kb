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
 * Categories Model
 *
 * @subpackage	Models
 * @link		http://68kb.com/user_guide/
 */
class Categories_model extends CI_Model
{	
	/**
	 * Constructor
	 *
	 * @return 	void
	 */
	public function __construct()
	{
		parent::__construct();
		log_message('debug', 'Categories Model Initialized');
	}
	
	// ------------------------------------------------------------------------
		
	/**
	* Delete Category
	* 
	* @param	int
	* @uses		remove_cat_image
	* @uses		_delete_category_product_rel
	* @return	bool
	*/
	public function delete_category($cat_id)
	{
		$cat_id = (int) $cat_id;
		
		$this->db->delete('categories', array('cat_id' => $cat_id)); 
		
		if ($this->db->affected_rows() == 0) 
		{
			return FALSE;
		} 
		
		$this->remove_cat_image($cat_id);
		
		$this->_delete_category_product_rel($cat_id);

		$this->events->trigger('categories_model/delete_category', $cat_id);

		$this->cache->delete_all('categories_model');
		
		return TRUE;
	}
	
	// ------------------------------------------------------------------------
	
	/**
 	* Delete Product to Categories Relationship
 	* 
	* @param	int
 	* @return 	bool
 	*/
	private function _delete_category_product_rel($cat_id)
	{
		if ( ! is_numeric($cat_id))
		{
			return FALSE;
		}
		
		$this->db->delete('product_category_rel', array('rel_cat_id' => $cat_id));
		
		if ($this->db->affected_rows() == 0) 
		{
			return FALSE;
		}
		
		$this->events->trigger('categories_model/_delete_category_product_rel', $cat_id);
		
		return TRUE;
	}
	
	// ------------------------------------------------------------------------

	/**
	* Delete Category Image.
	* 
	* @access	public
	* @param	int
	* @return 	bool
	*/
	public function remove_cat_image($cat_id)
	{
		$cat_id = (int) $cat_id;
		
		$this->load->helper('file');
		
		$this->db->select('cat_id,cat_image')
				->from('categories')
				->where('cat_id', $cat_id);
		
		$query = $this->db->get();
		
		if ($query->num_rows() == 0)
		{
			return FALSE;
		}
		
		$row = $query->row(); 
		
		$cat_id = $row->cat_id;
		
		// Suppress Errors when trying to unlink.
		@unlink(ROOTPATH . $this->config->item('cat_image_path') . $row->cat_image);
		
		$data = array('cat_image' => '');
		
		$this->db->where('cat_id', $cat_id);
		$this->db->update('categories', $data);
		
		$this->events->trigger('categories_model/remove_cat_image', $cat_id);
		
		return TRUE;
	}
	
	// ------------------------------------------------------------------------
	
	/**
 	* Edit Category
 	* 
	* @param	int
 	* @param	array $data An array of data.
	* @uses 	format_uri
 	* @return	bool
 	*/
	public function edit_category($cat_id, $data)
	{
		$cat_id = (int) $cat_id;
		
		if (isset($data['cat_uri']) && $data['cat_uri'] != '') 
		{
			$data['cat_uri'] = create_slug($data['cat_uri']);
		}
		else
		{
			$data['cat_uri'] = create_slug($data['cat_name']);
		}
		
		if (isset($data['cat_parent']) && $data['cat_parent'] > 0)
		{
			$uri = $this->_get_cat_path($data['cat_parent']); 
			$data['cat_uri'] = implode('/', $uri).'/'.$data['cat_uri'];
		}
		
		$data['cat_uri'] = $this->_check_uri($data['cat_uri'], $cat_id);
		
		
		
		$this->db->where('cat_id', $cat_id);
		$this->db->update('categories', $data);
		
		if ($this->db->affected_rows() == 0) 
		{
			return FALSE;
		} 
		
		$this->events->trigger('categories_model/edit_category', $cat_id);
		
		$this->cache->delete_all('categories_model');
		
		return TRUE;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Get the category path by going from current to top parent.
	 *
	 * This is used to generate the uri. 
	 *
	 * @param	int cat id
	 * @param	int level
	 * @return 	array
	 */
	private function _get_cat_path($cat_id, $lev = 0)
	{
		$cat_id = (int) $cat_id;
		
		$this->db->select('cat_id,cat_uri,cat_name,cat_parent')
					->from('categories')
					->where('cat_id', $cat_id);
		
		$query = $this->db->get();
		
		if ($query->num_rows() == 0) 
		{
			return array();
		}
		
		$cat = $query->row_array();
		
		$path = array();
		
		$path[] = $cat['cat_uri'];
		
		// Check to make sure we have a parent and that the / isn't applied.
		if ($cat['cat_parent'] != 0 && ! strstr($cat['cat_uri'], '/')) 
		{
			$path = array_merge($this->_get_cat_path($cat['cat_parent'], $lev+1), $path);
		}
		
		return $path;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Check URI
	 * 
	 * Checks other pages for the same uri.
	 * 
	 * @param	string 	
	 * @param	int
	 * @param	string
	 * @return	boolean TRUE if checks out ok, FALSE otherwise
	 */
	private function _check_uri($uri, $id = FALSE, $count = 0)
	{
		$uri = $this->security->xss_clean($uri);

		if ($count > 0)
		{
			$uri = $uri.'_'.$count;
		}
		
		$count++;
		
		if ($id !== FALSE) 
		{
			$id = (int) $id;
			$this->db->select('cat_uri')->from('categories')->where('cat_uri', $uri)->where('cat_id !=', $id);
		} 
		else 
		{
			$this->db->select('cat_uri')->from('categories')->where('cat_uri', $uri);
		}
		
		$query = $this->db->get();

		if ($query->num_rows() > 0) 
		{
			return $this->_check_uri($uri, $id, $count);
		} 
		else
		{
			return $uri;
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
 	* Add Category
 	* 
 	* @param	array 	$data An array of data.
	* @uses 	format_uri
 	* @return	mixed 	Id on success.
 	*/
	public function add_category($data)
	{	
		if (isset($data['cat_uri']) && $data['cat_uri'] != '') 
		{
			$data['cat_uri'] = create_slug($data['cat_uri']);
		}
		else
		{
			$data['cat_uri'] = create_slug($data['cat_name']);
		}
		
		if (isset($data['cat_parent']) && $data['cat_parent'] > 0)
		{
			$uri = $this->_get_cat_path($data['cat_parent']); 
			$data['cat_uri'] = implode('/', $uri).'/'.$data['cat_uri'];
		}
		
		$data['cat_uri'] = $this->_check_uri($data['cat_uri']);
		
		$this->db->insert('categories', $data);
		
		if ($this->db->affected_rows() == 0) 
		{
			return FALSE;
		} 
		
		$cat_id = $this->db->insert_id();
		
		$this->events->trigger('categories_model/add_category', $cat_id);
		
		$this->cache->delete_all('categories_model');
		
		return $cat_id;
	}
	
	/**
	 * Walk Categories
	 *
	 * Recursive format categories in a list
	 *
	 * @param	int
	 * @param	int
	 * @param	string
	 * @param	int
	 * @param	array
	 * @param	bool
	 * @return	string
	 */
	public function walk_categories($parent = 0, $depth = 2, $type = 'list', $level = 0, $selected = '', $in_admin = FALSE)
	{
		$indent = '';
		$children = FALSE;
		$tree = '';
		$checked = '';
		
		// Casting to int because this is used in a plugin. Plus you never know ;-)
		$parent = (int) $parent;
		$depth = (int) $depth;
		$level = (int) $level;
		
		$this->db->select('cat_id,cat_uri,cat_name,cat_description')
					->from('categories')
					->orderby('cat_order', 'DESC')
					->orderby('cat_name', 'asc')
					->where('cat_parent', $parent); 
					
		if ( ! $in_admin)
		{
			$this->db->where('cat_display', 'yes');	
		}
		
		$query = $this->db->get(); 

		if ($parent != 0) 
		{
			$indent = str_repeat(' ', $level*2);
		}

		if ($query->num_rows() > 0) 
		{
			$children = TRUE;
		}

		// display each child
		if ($children)
		{
			$tree .= $indent."<ul class='categories'>\n";
		}
		
		$results = $query->result_array();
		
		foreach ($results as $row)
		{
			if ($type == 'checkbox')
			{
				if (is_array($selected))
				{
					$checked = '';
					if (in_array($row['cat_id'], $selected, TRUE))
					{
						$checked = 'checked="checked"';
					}
				}
				
				$tree .= $indent.'<li class="cat_'.$row['cat_id'].'"><label><input type="checkbox" name="cats[]" value="'.$row['cat_id'].'" '.$checked.' />'.$row['cat_name'].'</label>'; 
			}
			else
			{
				$tree .= $indent.'<li class="cat_'.$row['cat_id'].' '.$row['cat_uri'].'"><a href="'.site_url('category/'.$row['cat_uri']).'">'.$row['cat_name'].'</a>'; 
			}
			
			// call this function again to display this child's children
			if($depth == 0 OR ($level < $depth-1))
			{
				$tree .= $this->walk_categories($row['cat_id'], $depth, $type, $level+1, $selected, $in_admin);
			}
			
			$tree .= "</li>\n";
		}
		
		$tree.= ($children) ? $indent."</ul>\n" : '';
		
		unset($row);
		$query->free_result();
		
		return $tree;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Get a single category
	*
	* @param	mixed
	* @param	bool
	* @return 	mixed
	* @uses		_get_cat_by_id
	* @uses		_get_cat_by_uri
	*/
	public function get_cat($query, $in_admin = FALSE)
	{
		if (is_numeric($query)) 
		{
			return $this->_get_cat_by_id($query, $in_admin);
        } 
		else 
		{
			return $this->_get_cat_by_uri($query, $in_admin);
        }
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Get Category By URI.
	 *
	 * Get a single category from its cat_uri
	 *
	 * @access	public
	 * @param	string	the unique uri
	 * @param	bool
	 * @return	array
	 */
	private function _get_cat_by_uri($cat_uri = '', $in_admin = FALSE)
	{
		if ($cat_uri == '')
		{
			return FALSE;
		}
		
		$cat_uri = (string) $cat_uri;
		
		$this->db->from('categories')->where('cat_uri', $cat_uri)->limit(1);
		
		if ( ! $in_admin)
		{
			$this->db->where('cat_display', 'yes');	
		}
		
		$query = $this->db->get();
		
		if ($query->num_rows() == 0)
		{
			return FALSE;
		}
		
		$data = $query->row_array();
		
		$query->free_result();
		
		return  $data;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Get Category By ID.
	 *
	 * Get a single category from its id
	 *
	 * @access	public
	 * @param	int	the unique id
	 * @param	bool
	 * @return	array
	 */
	private function _get_cat_by_id($cat_id, $in_admin = FALSE)
	{
		$cat_id = (int) $cat_id;
		
		$this->db->from('categories')->where('cat_id', $cat_id)->limit(1);
		
		if ( ! $in_admin)
		{
			$this->db->where('cat_display', 'yes');	
		}
		
		$query = $this->db->get();
		
		if ($query->num_rows() == 0)
		{
			return FALSE;
		}
		
		$data = $query->row_array();
		
		$query->free_result();
		
		return $data;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Breadcrumb
	* 
	* Generates a breadcrumb nav for categories
	* 
	* @param	int $node The current category id
	* @param	int $lev The current level
	* @return	array
	*/
	public function breadcrumb($cat_id, $lev = 0) 
	{
		$cat_id = (int) $cat_id;
		
		$this->db->select('cat_id,cat_uri,cat_name,cat_parent')
					->from('categories')
					->where('cat_id', $cat_id)
					->where('cat_display', 'yes')
					->order_by('cat_order DESC, cat_name ASC');
		
		$query = $this->db->get();

		if ($query->num_rows() == 0) 
		{
			return array();
		}
		
		$cat = $query->row_array();
		
		$path = array();
		
		$path[$lev]['cat_name'] = $cat['cat_name'];
		$path[$lev]['cat_uri'] = $cat['cat_uri'];
		
		if ($cat['cat_parent'] != 0) 
		{
			$path = array_merge($this->breadcrumb($cat['cat_parent'],$lev+1), $path);
		}
		
		return $path;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Total Listings
	* 
	* Get a count of total listings pre category
	* 
	* @param	int $node The current category id
	* @param	int $lev The current level
	* @return	array
	*/
	public function total_articles($cat_id, $lev = 0) 
	{
		$cat_id = (int) $cat_id;
		
		$this->load->library('categories_library');
		$this->categories_library->clear_ids();
		$this->categories_library->get_child_ids($cat_id);
		$search_cats = $this->categories_library->get_ids();
		
		if (empty($search_cats))
		{
			return 0; 
		}

		$this->db->select('article_id')
					->from('articles')
					->join('article2cat', 'article_id = article_id_rel', 'inner')
					->where_in('category_id_rel', $search_cats);

		$where = 'article_display = "y"';

		// Call any hooks and add them to the where clause.
		if ($this->events->active_hook('get_articles_where'))
		{
			$where .= $this->events->trigger('get_articles_where');
		}

		$this->db->where($where);
		
		$total = $this->db->count_all_results();
		
		return $total;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Get Categories
	* 
	* @param	bool	in_admin
	* @return	mixed
	*/
	public function get_categories($in_admin = FALSE)
	{
		$this->db->select('cat_id,cat_name,cat_allowads,cat_display,cat_parent,cat_uri')
					->from('categories')
					->orderby('cat_order', 'DESC')
					->orderby('cat_parent', 'asc')
					->orderby('cat_name', 'asc');
		
		if ($in_admin)
		{
			$this->db->where('cat_display', 'yes');
		}
		
		$query = $this->db->get();
		
		if ($query->num_rows() == 0) 
		{
			return FALSE;
		} 
		
		$cat = $query->result_array();
		
		$query->free_result();
		
		return $cat;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Get Sub Categories
	* 
	* @param	int		parent id
	* @return	mixed
	*/
	public function get_sub_categories($cat_parent)
	{
		$cat_parent = (int) $cat_parent;
		
		$this->db->select('cat_id,cat_uri,cat_image,cat_description,cat_name,cat_parent,cat_allowads')
					->from('categories')
					->where('cat_parent', $cat_parent)
					->where('cat_display', 'yes')
					->order_by('cat_order DESC, cat_name ASC');
					
		$query = $this->db->get();
		
		if ($query->num_rows() == 0) 
		{
			return FALSE;
		} 
		
		$cat = $query->result_array();
		
		$query->free_result();
		
		return $cat;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Get Category By Article.
	 *
	 * Get a list of categories an article is associated with.
	 *
	 * @access	public
	 * @param	int	the unique id
	 * @return	array
	 */
	function get_cats_by_article($id)
	{
		$this->db->from('article2cat');
		$this->db->join('categories', 'category_id_rel = cat_id', 'left');
		$this->db->where('article_id_rel', (int) $id);
		$this->db->where('cat_display', 'yes');
		$query = $this->db->get();
		
		if ($query->num_rows() == 0)
		{
			return FALSE;
		}
		$this->load->helper('string');
		
		$output = '';
		foreach($query->result_array() as $row) 
		{ 
			$output .= ' '. anchor('categories/'.$row['cat_uri'], $row['cat_name']).',';
		}
		
		return reduce_multiples($output, ',', TRUE);
	}
}
	
/* End of file categories_model.php */
/* Location: ./upload/includes/68kb/modules/categories/models/categories_model.php */ 