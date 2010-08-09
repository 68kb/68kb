<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Extends CI's pagination class (http://codeigniter.com/user_guide/libraries/pagination.html)
* It sets some variables for configuration of the pagination class dynamically,
* depending on the URI, so we don't have to substract the offset from the URI,
* or set $config['base_url'] and $config['uri_segment'] manually in the controller
* 
* Here is what is set by this extension class:
* 1. $this->offset - the current offset
* 2. $this->uri_segment - the URI segment to be used for pagination
* 3. $this->base_url - the base url to be used for pagination
* (where $this refers to the pagination class)
*
* The way this works is simple:
* Drop this library in folder application/libraries
* If we use pagination, it must ALWAYS follow the following syntax and be
* located at the END of the URI:
* PAGINATION_SELECTOR/offset
* E.g. http://www.example.com/controller/action/Page/2
*
* The PAGINATION_SELECTOR is a special string which we know will ONLY be in the
* URI when paging is set. Let's say the PAGINATION_SELECTOR is 'Page' (since most
* coders never use any capitals in the URI, most of the times any string with
* a single capital character in it will suffice). The PAGINATION_SELECTOR is
* set in the general config file,
* in the following index: $CI->config->item('pagination_selector')
*
* Example use (in controller):
* // Set pagination and get pagination HTML
* $this->data['pagination'] = $this->pagination->get_pagination($this->db->count_all_results('my_table'), 10);
*
* // Retrieve paginated results, using the dynamically determined offset
* $this->db->limit($config['per_page'], $this->pagination->offset);
* $query = $this->db->get('my_table');
* 
* @name MY_Pagination.php
* @version 1.0
* @author Joost van Veen
*/
class MY_Pagination extends CI_Pagination
{
	
	/**
	 * Pagination offset.
	 * @var integer
	 */
	public $offset = 0;
	
	/**
	 * Opening HTML tag for pagination string 
	 * @var string
	 */
	public $cur_tag_open = '&nbsp;<span class="current">';
	
	/**
	 * Opening HTML tag for pagination string
	 * @var unknown_type
	 */
	public $cur_tag_close = '</span>';
	
	/**
	 * Text for link to first page
	 * @var string
	 */
	public $first_link = 'First';
	
	/**
	 * Text for link to last page
	 * @var string
	 */
	public $last_link = 'Last';
	
	/**
	 * Number of links to show in pagination
	 * @var integer 
	 */
	public $num_links = 1;
	
	/**
	 * Pagination selector to be used in URI. Make sure to set this to a value 
	 * that is never used elsewhere in the URI.
	 * @var string
	 */
	public $pagination_selector = '';

	public function __construct ()
	{
		parent::__construct();
		
		$CI = & get_instance();
		
		log_message('debug', "MY custom Pagination Class Initialized");
		
		$this->first_link = lang('lang_first');
		$this->last_link = lang('lang_last');

		$this->pagination_selector = 'page';
		
		$this->_set_pagination_offset();
	}

	/**
	 * Rturn HTML for pagination, based on count ($total_rows) and limit ($per_page)
	 * @param integer $total_rows
	 * @param integer $per_page
	 * @return string
	 */
	public function get_pagination ($total_rows, $per_page)
	{
		if ($total_rows > $per_page) {
			$CI = & get_instance();
			$this->initialize(array('total_rows' => $total_rows, 'per_page' => $per_page));
			return $this->create_links();
		}
	}

	/**
	 * Set dynamic pagination variables in $CI->data['pagvars']
	 * @return void
	 */
	private function _set_pagination_offset ()
	{
		
		// Instantiate the CI super object so we can use the uri class
		$CI = & get_instance();
		
		// Store pagination offset if it is set
		if (strstr($CI->uri->uri_string(), $this->pagination_selector)) {
			
			// Get the segment offset for the pagination selector
			$segments = $CI->uri->segment_array();
			
			// Loop through segments to retrieve pagination offset
			foreach ($segments as $key => $value) {
				
				// Find the pagination_selector and work from there
				if ($value == $this->pagination_selector) {
					
					// Store pagination offset
					$this->offset = $CI->uri->segment($key + 1);
					
					// Store pagination segment
					$this->uri_segment = $key + 1;
					
					// Set base url for paging. This only works if the
					// pagination_selector and paging offset are AT THE END of
					// the URI!
					$uri = $CI->uri->uri_string();
					$pos = strpos($uri, $this->pagination_selector);
					$page = substr($uri, 0, $pos + strlen($this->pagination_selector));
					//echo $page;
					//$this->base_url = $CI->config->item('base_url') . substr($uri, 0, $pos + strlen($this->pagination_selector));
					//$this->base_url = site_url(str_replace($this->pagination_selector, '', $page));
					//echo '<pre>'.$this->offset.'</pre><BR>';
					if ( ! $CI->uri->segment($key + 1))
					{
						$this->base_url = site_url(str_replace($this->pagination_selector, '', $page));
					}
					else
					{
						$this->base_url = site_url($page);
					}
				}
			}
		}
		else {
			// Pagination selector was not found in URI string. So offset is 0
			$this->offset = 0;
			$this->uri_segment = 0;

			//$this->base_url = $CI->config->item('base_url') . substr($CI->uri->uri_string(), 1) . '/' . $this->pagination_selector;
			$this->base_url = site_url(substr($CI->uri->uri_string(), 1) . '/' . $this->pagination_selector);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Generate the pagination links
	 *
	 * @access	public
	 * @return	string
	 */
	public function create_links()
	{
		// If our item count or per-page total is zero there is no need to continue.
		if ($this->total_rows == 0 OR $this->per_page == 0)
		{
			return '';
		}

		// Calculate the total number of pages
		$num_pages = ceil($this->total_rows / $this->per_page);

		// Is there only one page? Hm... nothing more to do here then.
		if ($num_pages == 1)
		{
			return '';
		}

		// Determine the current page number.
		$CI =& get_instance();

		if ($CI->config->item('enable_query_strings') === TRUE OR $this->page_query_string === TRUE)
		{
			if ($CI->input->get($this->query_string_segment) != 0)
			{
				$this->cur_page = $CI->input->get($this->query_string_segment);

				// Prep the current page - no funny business!
				$this->cur_page = (int) $this->cur_page;
			}
		}
		else
		{
			if ($CI->uri->segment($this->uri_segment) != 0)
			{
				$this->cur_page = $CI->uri->segment($this->uri_segment);

				// Prep the current page - no funny business!
				$this->cur_page = (int) $this->cur_page;
			}
		}

		$this->num_links = (int)$this->num_links;

		if ($this->num_links < 1)
		{
			show_error('Your number of links must be a positive number.');
		}

		if ( ! is_numeric($this->cur_page))
		{
			$this->cur_page = 0;
		}

		// Is the page number beyond the result range?
		// If so we show the last page
		if ($this->cur_page > $this->total_rows)
		{
			$this->cur_page = ($num_pages - 1) * $this->per_page;
		}

		$uri_page_number = $this->cur_page;
		$this->cur_page = floor(($this->cur_page/$this->per_page) + 1);

		// Calculate the start and end numbers. These determine
		// which number to start and end the digit links with
		$start = (($this->cur_page - $this->num_links) > 0) ? $this->cur_page - ($this->num_links - 1) : 1;
		$end   = (($this->cur_page + $this->num_links) < $num_pages) ? $this->cur_page + $this->num_links : $num_pages;

		// Is pagination being used over GET or POST?  If get, add a per_page query
		// string. If post, add a trailing slash to the base URL if needed
		if ($CI->config->item('enable_query_strings') === TRUE OR $this->page_query_string === TRUE)
		{
			$this->base_url = rtrim($this->base_url).'&amp;'.$this->query_string_segment.'=';
		}
		else
		{
			$this->base_url = rtrim($this->base_url, '/') .'/';
		}

		// And here we go...
		$output = '<span class="pages">Page '. $this->cur_page .' of '. $num_pages.'</span>';
		
		// Render the "First" link
		if	($this->cur_page > ($this->num_links + 1))
		{
			$output .= $this->first_tag_open.'<a href="'.str_replace($this->pagination_selector, '', $this->base_url).'">'.$this->first_link.'</a>'.$this->first_tag_close;
		}

		// Render the "previous" link
		if	($this->cur_page != 1)
		{
			$i = $uri_page_number - $this->per_page;
			if ($i == 0)
			{
				$output .= $this->prev_tag_open.'<a href="'.str_replace($this->pagination_selector, '', $this->base_url).'">'.$this->prev_link.'</a>'.$this->prev_tag_close;
			}
			else
			{
				$output .= $this->prev_tag_open.'<a href="'.$this->base_url.$i.'">'.$this->prev_link.'</a>'.$this->prev_tag_close;
			}
		}

		// Write the digit links
		for ($loop = $start -1; $loop <= $end; $loop++)
		{
			$i = ($loop * $this->per_page) - $this->per_page;

			if ($i >= 0)
			{
				if ($this->cur_page == $loop)
				{
					$output .= $this->cur_tag_open.$loop.$this->cur_tag_close; // Current page
				}
				else
				{
					$n = ($i == 0) ? '' : $i;
					if ($i == 0)
					{
						$output .= $this->num_tag_open.'<a href="'.str_replace($this->pagination_selector, '', $this->base_url).'">'.$loop.'</a>'.$this->num_tag_close;
					}
					else
					{
						$output .= $this->num_tag_open.'<a href="'.$this->base_url.$n.'">'.$loop.'</a>'.$this->num_tag_close;
					}
				}
			}
		}

		// Render the "next" link
		if ($this->cur_page < $num_pages)
		{
			$output .= $this->next_tag_open.'<a href="'.$this->base_url.($this->cur_page * $this->per_page).'">'.$this->next_link.'</a>'.$this->next_tag_close;
		}

		// Render the "Last" link
		if (($this->cur_page + $this->num_links) < $num_pages)
		{
			$i = (($num_pages * $this->per_page) - $this->per_page);
			$output .= $this->last_tag_open.'<a href="'.$this->base_url.$i.'">'.$this->last_link.'</a>'.$this->last_tag_close;
		}

		// Kill double slashes.	 Note: Sometimes we can end up with a double slash
		// in the penultimate link so we'll kill all double slashes.
		$output = preg_replace("#([^:])//+#", "\\1/", $output);

		// Add the wrapper HTML if exists
		$output = $this->full_tag_open.$output.$this->full_tag_close;

		return $output;
	}
}
