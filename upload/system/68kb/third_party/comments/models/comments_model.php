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
 * Comments Model
 *
 * This class is used to handle the comments data.
 *
 * @package		68kb
 * @subpackage	Models
 */
class Comments_model extends CI_model
{	
	/**
	 * Constructor
	 *
	 * @uses 	get_settings
	 * @return 	void
	 */
	function __construct()
	{
		parent::__construct();
		log_message('debug', 'Comments Model Initialized');
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Get a list of all new orders since a certain timestamp
	 *
	 * @param	int time();
	 * @return 	array
	 */
	public function get_new_comments($date) 
	{
		$this->db->from('comments')
			->join('articles', 'comment_article_ID = article_id', 'inner')
			->where('comment_date <> ', $date)
			->orderby('comment_date', 'desc'); 
		
		$query = $this->db->get();

		if ($query->num_rows() == 0) 
		{
			return FALSE;
		}
		
		$row = $query->result_array();
		
		$query->free_result();
		
		return $row;
	}
	
	// ------------------------------------------------------------------------
		
	/**
	* Delete Category
	* 
	* @param	int $cat_id The id of the category to delete.
	* @return	true on success.
	*/
	function delete_comment($id)
	{
		$id=(int)trim($id);
		$this->db->delete('comments', array('comment_ID' => $id)); 
		if ($this->db->affected_rows() > 0) 
		{
			$this->core_events->trigger('comments/delete', $id);
			$this->db->cache_delete_all();
			return true;
		} 
		else 
		{
			return false;
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
 	* Edit Category
 	* 
 	* @param	array $data An array of data.
	* @uses 	format_uri
 	* @return	true on success.
 	*/
	function edit_comment($id, $data)
	{
		$id = (int)$id;
		$this->db->where('comment_ID', $id);
		$this->db->update('comments', $data);
		
		if ($this->db->affected_rows() > 0) 
		{
			$this->core_events->trigger('comments/edit', $id);
			$this->db->cache_delete_all();
			return true;
		} 
		else
		{
			log_message('info', 'Could not edit the comment id '. $id);
			return false;
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
 	* Add Comment
 	* 
 	* @param	array 	$data An array of data.
 	* @return	mixed 	Id on success.
 	*/
	function add_comment($data)
	{
		$data['comment_date'] = time();
		$data['comment_approved'] = $this->spam_check($data['comment_author_email'], $data['comment_content']);
		
		$this->db->insert('comments', $data);
		
		if ($this->db->affected_rows() > 0) 
		{
			$id = $this->db->insert_id();
			$this->core_events->trigger('comments/add', $id);
			$this->db->cache_delete_all();
			return $id;
		} 
		else 
		{
			return false;
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
 	* Spam Check
	*
	* Very simple spam checker. Checks the comment for links and if they 
	* already have one approved comment. I didn't add any hooks here 
	* because I thought it would be better to check after it is submitted
	* so you can do a query and get everything about the comment. 
 	* 
 	* @param	string
	* @param	string
 	* @return	mixed 
 	*/
	function spam_check($email, $comment)
	{
		$array = array('email' => $email, 'comment' => $comment);
		$this->core_events->trigger('comment/spam', $array);
		
		$this->db->from('comments')->where('comment_author_email', $email);
		$query = $this->db->get();
		
		/*
		 * Check the number of links included in the comment.  If more that 2 is 90% spam. Idea from wp.
		 */
		if ( preg_match_all("|(href\t*?=\t*?['\"]?)?(https?:)?//|i", $comment, $out) >= 2 )
		{
			return 'spam';
		}
		elseif ($query->num_rows() > 0)
		{
			return '1'; //active
		}
		else 
		{
			return '0'; //hold for review
		}
	}
	// ------------------------------------------------------------------------
	
	/**
	* Chagne Display
	* 
	* @param string The new status
	* @param int The comment id.
	* @access	private
	*/
	function change_display($status, $id)
	{
		$id = (int)$id;
		if ($status==5)
		{
			$this->db->delete('comments', array('comment_ID' => $id));
			$this->core_events->trigger('comments/delete', $id);
			$this->db->cache_delete_all();
		}
		else
		{
			$data = array(
			    'comment_approved' => $status
			);
			$this->db->where('comment_ID', $id);
			$this->db->update('comments', $data);
			$this->core_events->trigger('comments/changestatus', $id);
			$this->db->cache_delete_all();
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Get approved comments
	 *
	 * Get approved comments based of article id
	 *
	 * @access	public
	 * @param	int	the unique id
	 * @return	array
	 */
	function get_article_comments($id)
	{
		$id=(int)$id;
		
		$this->db->from('comments')->where('comment_article_ID', $id)->where('comment_approved', '1')->orderby('comment_date', 'ASC');
		$query = $this->db->get();
		return  $query;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Get approved comments count
	 *
	 * Get approved comments count based of article id
	 *
	 * @access	public
	 * @param	int	the unique id
	 * @return	int
	 */
	function get_article_comments_count($id)
	{
		$id = (int)$id;
		$this->db->where('comment_article_ID', $id)->where('comment_approved', '1');
		$this->db->from('comments');
		return $this->db->count_all_results();
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Email the admin about the comment
	 *
	 * @access	public
	 * @param	int	the unique id
	 * @return	int
	 */
	function email_admin($id, $data = '')
	{
		if ($data)
		{
			$this->load->library('email');
			$site_email = $this->settings->get_setting('site_email');
			$title = $this->settings->get_setting('site_name');
			
			// first get the article info
			$this->db->select('article_author, article_title, article_uri')->from('articles')->where('article_id', $data['comment_article_ID']);
			$query = $this->db->get();
			
			if ($query->num_rows() > 0)
			{
				$row = $query->row(); 
				$subject = $title .' '. lang('kb_new_comment') .' '. $row->article_title;
				
				// now get the author
				$this->db->select('email')->from('users')->where('id', $row->article_author);
				$query2 = $this->db->get();
				if($query2->num_rows() > 0)
				{
					$user = $query2->row(); 
					$this->email->cc($user->email);
				}
				
				// now lets email it
				$message = lang('kb_name').': '.$data['comment_author'] ."\n";
				$message .= lang('kb_email').': '. $data['comment_author_email'] ."\n";
				$message .= lang('kb_ip').': '. $data['comment_author_IP'] ."\n";
				$message .= lang('kb_comments').": \n";
				$message .= $data['comment_content'] ."\n\n";
				$message .= lang('kb_comment_link')."\n";
				$message .= '{unwrap}'.site_url('admin/comments/edit/'.$id).'{/unwrap}';
				
				$this->email->set_newline("\r\n");
				$this->email->from($site_email, $title);
				$this->email->to($site_email); 
				$this->email->subject($subject);
				$this->email->message($message);	
				if ( ! $this->email->send())
				{
					echo $this->email->print_debugger();
				}
			}
		}
	}
}
	
/* End of file comments_model.php */
/* Location: ./upload/includes/68kb/modules/kb/models/comments_model.php */