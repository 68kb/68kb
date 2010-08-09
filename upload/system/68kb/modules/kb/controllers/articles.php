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
 * Index Controller
 *
 *
 * @subpackage	Controllers
 * @link		http://68kb.com/user_guide/
 *
 */
class Articles extends Front_Controller 
{
	private $_fields_active = FALSE;
	
	/**
	 * Constructor
	 *
	 * @return 	void
	 */
	public function __construct() 
	{
		parent::__construct();
		
		log_message('debug', 'Articles Controller Initialized');
		
		// Load the categories model
		$this->load->model('categories/categories_model');
		$this->load->model('users/users_model');
		$this->load->model('articles_model');
		$this->load->helper('date');
		
		if ($this->events->active('fields'))
		{
			$this->_fields_active = TRUE;
			$this->load->model('fields/fields_model');
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Index File
	*ˇ
	*/
	public function index($uri='') 
	{
		$uri = $this->security->xss_clean($uri);
		
		if ($article = $this->articles_model->get_article_by_uri($uri))
		{
			$data['article'] = $article;
			$this->articles_model->add_hit($data['article']['article_id']);
			
			//format description
			$data['article']['article_description'] = $this->articles_model->glossary($data['article']['article_description']);
			
			// call hooks
			$arr = array('article_id' => $data['article']['article_id'], 'article_title' => $data['article']['article_title']);
			if($this->events->trigger('article/title', $arr) != '')
			{
				$data['article']['article_description'] = $this->events->trigger('article/title', $arr);
			}
			$arr = array('article_id' => $data['article']['article_id'], 'article_description' => $data['article']['article_description']);
			if($this->events->trigger('article/description', $arr) != '')
			{
				$data['article']['article_description'] = $this->events->trigger('article/description', $arr);
			}

			// Format dates
			$data['article']['article_date'] = format_date($data['article']['article_date']);
			$data['article']['article_modified'] = format_date($data['article']['article_modified']);
			
			$data['article_cats'] = $this->categories_model->get_cats_by_article($data['article']['article_id']);
			$data['attach'] = $this->articles_model->get_attachments($data['article']['article_id']);
			$data['author'] = $this->users_model->get_user($data['article']['article_author']);
			
			$data['meta_keywords'] = $data['article']['article_keywords'];
			$data['meta_description'] = $data['article']['article_short_desc'];
			
			$this->template->title($data['article']['article_title']);
			$this->template->meta_keywords($data['article']['article_keywords']);
			
			$this->_show_page($data);
		}
		else 
		{
			show_404();
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Show a page based off uri
	 *
	 * @param	array Data
	 */
	private function _show_page($data)
	{
		$uri = '';
		if (isset($data['article']['article_uri']))
		{
			$uri = $data['article']['article_uri'];
		}

		$theme = $this->settings->get_setting('site_theme');
		if ($theme && file_exists(ROOTPATH . 'themes/' . $theme .'/articles/'. $uri . EXT))
		{
			$this->template->build($uri, $data);
		}
		else
		{
			$this->template->build('details', $data);
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Remap
	*
	* Redirect everything to index
	*
	* @link http://codeigniter.com/user_guide/general/controllers.html#remapping
	* @access	private
	* @param	string	the unique uri
	* @return	array
	*/
	public function _remap($method)
	{
		$this->index($method);	
	}

}
/* End of file articles.php */
/* Location: ./upload/system/68kb/modules/kb/controllers/articles.php */ 