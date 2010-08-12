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
 * Comments Add-on
 *
 * This is used to display debug information. 
 *
 * @subpackage	Add-Ons
 *
 */
class Comments_extension
{
	
	private $_ci;
	
	/**
	 * Setup events
	 *
	 * @access	public
	 */
	public function __construct($modules)
	{
		$this->_ci = CI_Base::get_instance();
		//$this->_ci->load->language('comments/comments');
		$modules->register('admin/home', $this, 'latest_comments');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Set error reporting
	 *
	 * @access	public
	 */	
	public function latest_comments()
	{
		$this->_ci->load->model('comments/comments_model');
		
		if ($comments = $this->_ci->comments_model->get_new_comments($this->_ci->session->userdata('user_last_login')))
		{
			$this->_ci->load->library('table');
			$table_template = array(
				'table_open'		=> '<table class="main" id="grid" width="100%" border="0" cellspacing="0" cellpadding="0">',
				'row_start'			=> '<tr class="second">',
				'row_alt_start'		=> '<tr class="first">'
			);
			$this->_ci->table->set_template($table_template);
			
			$this->_ci->table->set_heading(lang('lang_name'), lang('lang_comment'), lang('lang_article'), lang('lang_status'));
			
			foreach ($comments AS $item)
			{
				if($item['comment_approved'] == 'spam') {
					$status = '<span class="spam">'.lang('lang_spam').'</span>';
				} elseif($item['comment_approved'] == 0) {
					$status = '<span class="inactive">'.lang('lang_notactive').'</span>';
				} else {
					$status = '<span class="active">'.lang('lang_active').'</span>';
				}
				
				$this->_ci->table->add_row(
					'<div class="gravatar"><img class="gravatar" src="'.gravatar($item['comment_author_email'], "PG", "24", "wavatar").'" /></div>
					<strong>
						'.$item['comment_author'].'
					</strong>',
					word_limiter($item['comment_content'], 15) .'&nbsp;'.
					anchor('admin/comments/edit/'.$item['comment_ID'], lang('lang_edit')),
					anchor('article/'.$item['article_uri'].'/#comment-'.$item['comment_ID'], $item['article_title']),
					$status
				);
			}
			
			echo '<h2>'.lang('lang_recent_comments').'</h2>';
			echo $this->_ci->table->generate();
		}
	}
}

/* End of file comments_extension.php */
/* Location: ./upload/includes/addons/comments/comments_extension.php */ 