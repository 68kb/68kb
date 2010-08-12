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
 * RSS Controller
 *
 *
 * @subpackage	Controllers
 * @link		http://68kb.com/user_guide/
 *
 */
class Rss extends Front_Controller 
{
	
	public function __construct()
	{
		parent::__construct();
		log_message('debug', 'RSS Controller Initialized');
		$this->load->helper('date');
		$this->load->helper('xml');
		$this->load->model('kb/articles_model');
		
		$this->data['site_title'] = $this->settings->get_setting('site_name');
		$this->data['site_description'] = $this->settings->get_setting('site_description');
		$this->data['feed_url'] = base_url();
		$this->data['page_language'] = 'en-ca';
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Show the user over view page.
	 */
	public function index()
	{
		$this->data['items'] = $this->articles_model->get_latest();
		$this->_show_feed();
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Default RSS View
	 *
	 * Show the latest articles
	 *
	 * @param	string - The category uri
	 * @uses	show_feed
	 */
	function category($uri = '')
	{
		$this->load->model('categories/categories_model');
		if($uri <> '')
		{
			$uri = $this->security->xss_clean($uri);
			$cat = $this->categories_model->get_cat($uri);
			if ($cat)
			{
				$id = $cat['cat_id'];
				$this->data['items'] = $this->articles_model->get_articles_by_catid($id, 0, 0, FALSE);
				$this->_show_feed();
			}
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Display the template
	 *
	 * @access	public
	 */
	private function _show_feed()
	{
		$this->template->set_layout();
		header("Content-Type: application/rss+xml");
		$this->load->view('rss', $this->data);
	}
	
}

/* End of file rss.php */
/* Location: ./upload/system/68kb/modules/kb/controllers/rss.php */ 