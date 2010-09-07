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

class Search_model extends CI_Model
{
	/**
	 * Constructor
	 *
	 * @return 	void
	 */
	public function __construct()
	{
		parent::__construct();
		log_message('debug', 'Search_model Initialized');
	}

	// ------------------------------------------------------------------------

	/**
	 * Store Search Results
	 *
	 * @param	string - The where clause
	 * @param	int - The total results.
	 */
	public function store_search_results($keywords, $total)
	{
		$this->load->helper('string');

		$hash = random_string('unique');

		// First delete any old searches by the user
		$this->db->where('search_user_id', $this->session->userdata('user_id'));
		$this->db->where('search_ip', $this->input->ip_address());
		$this->db->delete('search');

		$data = array(
			'search_id'			=> $hash,
			'search_date'		=> time(),
			'search_user_id'	=> $this->session->userdata('user_id'),
			'search_keywords'	=> $keywords,
			'search_total'		=> $total,
			'search_ip'			=> $this->input->ip_address()
		);

		$this->db->insert('search', $data);

		return $hash;
	}

	// ------------------------------------------------------------------------

	private function _save_keywords($keywords)
	{
		$data = array(
			'searchlog_date'		=> time(),
			'searchlog_user_id'		=> $this->session->userdata('user_id'),
			'searchlog_term'		=> $keywords,
			'searchlog_ip'			=> $this->input->ip_address()
		);

		$this->db->insert('searchlog', $data);
	}

	// ------------------------------------------------------------------------

	/**
	 * Delete old search data.
	 *
	 * @return 	void
	 */
	public function clean_search_results()
	{
		// Set the expiration to one hour
		$expire = strtotime("-1 hour");

		$this->db->where('search_date <', $expire);
		$this->db->delete('search');
	}

	// ------------------------------------------------------------------------

	/**
	 * Remove the search session data
	 */
	public function remove_session()
	{
		if ($sess_data = get_cookie($this->config->item('sess_cookie_name'), TRUE))
		{
			$sess_data = unserialize($sess_data);
			foreach ($sess_data as $key => $value)
			{
				if (strpos($key, 'extra_field_') !== FALSE)
				{
					$this->session->unset_userdata($key);
				}
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Get any extra fields that can be searched
	 *
	 * @param	int Category id
	 * @return 	array
	 */
	public function get_fields($cat_id)
	{
		$cat_id = (int) $cat_id;

		$this->db->select('field_id,field_type,field_internal,field_validation')
					->distinct()
					->from('fields')
					->join('fields_bindings', 'field_id = rel_field_id', 'left')
					->where('rel_cat_id', $cat_id)
					->where('field_table', 'listing_fields')
					->where('field_search', 'yes');

		$query = $this->db->get();

		if ($query->num_rows() == 0)
		{
			return FALSE;
		}

		$data = $query->result_array();

		$query->free_result();

		return $data;
	}

	// ------------------------------------------------------------------------

	/**
	 * Get a stored search
	 *
	 * @return 	array
	 */
	public function get_search($hash)
	{
		$this->db->from('search')
				->where('search_id', $hash)
				->where('search_ip', $this->input->ip_address());

		$query = $this->db->get();

		if ($query->num_rows() == 0)
		{
			return FALSE;
		}

		$data = $query->row_array();

		return $data;
	}

	// ------------------------------------------------------------------------

	/**
	 * Filter the listings by options
	 *
	 * @param	array
	 * @return 	string
	 */
	public function filter_listings($search_options = array())
	{
		if (isset($search_options['category']))
		{
			$category = $search_options['category'];
			if ($this->events->active('fields'))
			{
				$fields = $this->get_fields($category);
			}
		}

		$this->db->from('articles')
				->join('article_fields', 'article_id = article_field_id', 'inner');

		$where = 'article_display = "y"';

		if (isset($search_options['category']) OR isset($search_options['article_category']))
		{
			$this->db->join('article2cat', 'article_id = article_id_rel', 'right');
		}

		// Call any hooks and add them to the where clause.
		if ($this->events->active_hook('get_articles_where'))
		{
			$where .= $this->events->trigger('get_articles_where', $search_options);
		}

		// Validate that the search_options can be searched.
		if ( ! empty($search_options))
		{
			if (isset($fields) && ! empty($fields))
			{
				foreach ($fields AS $extra)
				{
					$field_name = $extra['field_internal'];
					$listing_field = 'extra_field_'.$field_name;

					// Check if it is a min/max field
					if ($extra['field_type'] == 'price' OR $extra['field_type'] == 'range' OR strpos($extra['field_validation'], 'numeric') !== FALSE)
					{
						$min = 0;

						if (isset($search_options[$field_name.'_min']) && is_numeric($search_options[$field_name.'_min']))
						{
							$min = (int) $search_options[$field_name.'_min'];
							$this->session->set_userdata($listing_field.'_min', $min);
						}

						$max = 999999999;

						if (isset($search_options[$field_name.'_max']) && is_numeric($search_options[$field_name.'_max']))
						{
							$max = (int) $search_options[$field_name.'_max'];
							$this->session->set_userdata($listing_field.'_max', $max);
						}

						if ($min > $max OR $max == 0)
						{
							$max = 999999999;
						}

						$where .= ' AND '.$listing_field.' BETWEEN '. $min ." AND ". $max;
					}
					elseif ($extra['field_type'] == 'date') // Searching dates
					{
						$min = strtotime("-10 years");

						if (isset($search_options[$field_name.'_min']) && $search_options[$field_name.'_min'] != 'min')
						{
							$value = $search_options[$field_name.'_min'] .' 00:00:00';
							$min = human_to_unix($value);
							$this->session->set_userdata($listing_field.'_min', $search_options[$field_name.'_min']);
						}

						$max = strtotime("+10 years");

						if (isset($search_options[$field_name.'_max']) && $search_options[$field_name.'_max'] != 'max')
						{
							$value = $search_options[$field_name.'_max'] .' 00:00:00';
							$max = human_to_unix($value);
							$this->session->set_userdata($listing_field.'_max', $search_options[$field_name.'_max']);
						}

						if ($min > $max)
						{
							$max = strtotime("+10 years");
						}

						$where .= ' AND '.$listing_field.' BETWEEN '. (int) $min ." AND ". (int) $max;

					}
					elseif (isset($search_options[$field_name]) && $search_options[$field_name] != '')
					{
						$this->session->set_userdata($listing_field, $search_options[$field_name]);
						$where .= ' AND '. $listing_field .' LIKE "%'. $this->db->escape($search_options[$field_name]) .'%"';
					}
				}
			}

			if (isset($search_options['keywords'])) // Keyword searching
			{
				$keywords = $this->security->xss_clean($search_options['keywords']);
				$keywords = strip_tags(stripslashes($keywords));
				$words_array = explode(" ", $keywords);

				foreach ($words_array as $trimm)
				{
					$where .= " AND (
						article_title LIKE '%".$this->db->escape_like_str($trimm)."%' OR
						article_short_desc LIKE '%".$this->db->escape_like_str($trimm)."%' OR
						article_description LIKE '%".$this->db->escape_like_str($trimm)."%'
					)";
				}
			}

			if (isset($search_options['article_category']))
			{
				if (is_array($search_options['article_category']))
				{
					$where .= ' AND category_id_rel IN('.implode($search_options['article_category'],',').')';
				}
			}
			elseif (isset($category))
			{
				if (is_numeric($category))
				{
					$where .= ' AND category_id_rel = '. (int) $category;
				}
			}
		}

		$this->db->where($where);

		$query = $this->db->get();

		if ($query->num_rows() == 0)
		{
			return FALSE;
		}

		$data = $query->result_array();

		// Save the keywords
		if (isset($search_options['keywords']))
		{
			$this->_save_keywords($search_options['keywords']);
		}

		// Save the search
		$hash = $this->store_search_results(base64_encode(serialize($where)), $query->num_rows());

		$query->free_result();

		return $hash;
	}
}
/* End of file search_model.php */
/* Location: ./upload/includes/68kb/modules/search/models/search_model.php */