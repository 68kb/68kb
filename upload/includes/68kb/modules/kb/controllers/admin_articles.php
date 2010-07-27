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
 * Admin Users Controller
 *
 * @subpackage	Controllers
 * @link		http://68kb.com/user_guide/admin/users.html
 *
 */
class Admin_articles extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('users_model');
		$this->load->model('articles_model');
		
		if ( ! $this->users_auth->check_role('can_manage_users'))
		{
			show_error(lang('not_authorized'));
		}
	}
	
	// ------------------------------------------------------------------------
	
	/** 
	* Show table grid
	*/
	public function index()
	{
		$data['nav'] = 'users';
		
		$this->template->set_metadata('stylesheet', base_url() . 'themes/cp/css/smoothness/jquery-ui.css', 'link');
		$this->template->set_metadata('js', 'js/dataTables.min.js', 'js_include');
		
		$this->template->title = lang('lang_manage_users');
		
		$this->template->build('admin/articles/grid', $data); 
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Edit Category
	*
	*/
	public function add()
	{
		$data['nav'] = 'articles';
		
		$this->template->title(lang('lang_add_article'));
		
		// Get the categories
		$this->load->library('categories/categories_library');
		$this->load->model('categories/categories_model');
		$cats = $this->categories_model->get_categories(TRUE);
		$this->categories_library->category_tree($cats);
		$data['tree'] = $this->categories_library->get_categories();;
		
		$data['action']='add';
		
		$this->load->helper(array('form', 'url', 'html'));

		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('article_title', 'lang:kb_title', 'required');
		$this->form_validation->set_rules('article_uri', 'lang:kb_uri', 'alpha_dash');
		$this->form_validation->set_rules('article_keywords', 'lang:kb_keywords', 'trim|xss_clean');
		$this->form_validation->set_rules('article_short_desc', 'lang:kb_short_description', 'trim|xss_clean');
		$this->form_validation->set_rules('article_description', 'lang:kb_description', 'trim|xss_clean');
		$this->form_validation->set_rules('article_display', 'lang:kb_display', 'trim');
		$this->form_validation->set_rules('article_order', 'lang:kb_weight', 'numeric');
		$this->events->trigger('articles/validation');
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->template->build('admin/articles/form', $data);
		}
		else
		{
			$data = array(
				'article_uri' => $article_uri, 
				'article_author' => $this->session->userdata('userid'), 
				'article_title' => $this->input->post('article_title', TRUE),
				'article_keywords' => $this->input->post('article_keywords', TRUE),
				'article_short_desc' => $this->input->post('article_short_desc', TRUE),
				'article_description' => $this->input->post('article_description', TRUE),
				'article_display' => $this->input->post('article_display', TRUE),
				'article_order' => $this->input->post('article_order', TRUE)
			);
			
			$id = $this->article_model->add_article($data);
			$this->session->set_flashdata('msg', lang('lang_settings_saved'));
			if (is_int($id))
			{
				//$tags = $this->input->post('tags');
				//$this->tags_model->insert_tags($id, $tags);
				$this->category_model->insert_cats($id, $cats);
				$this->core_events->trigger('articles/add', $id);
				
				if ($_FILES['userfile']['name'] != "") 
				{
					$target = KBPATH .'uploads/'.$id;
					$this->_mkdir($target);
					$config['upload_path'] = $target;
					$config['allowed_types'] = $this->config->item('attachment_types');
					$this->load->library('upload', $config);
					if ( ! $this->upload->do_upload())
					{
						$this->session->set_flashdata('error', $this->upload->display_errors());
						redirect('admin/kb/articles/edit/'.$id);
					}
					else
					{
						$upload = array('upload_data' => $this->upload->data());
						$insert = array(
							'article_id' => $id, 
							'attach_name' => $upload['upload_data']['file_name'],
							'attach_type' => $upload['upload_data']['file_type'],
							'attach_size' => $upload['upload_data']['file_size']
						);
						$this->db->insert('attachments', $insert);
						$data['attach'] = $this->article_model->get_attachments($id);
					}
				}
			    if (isset($_POST['save']) && $_POST['save']<>"")
			    {
			    	redirect('admin/kb/articles/edit/'.$id);
			    }
			    else
			    {
			    	redirect('admin/kb/articles/');
			    }
			}
			redirect('admin/kb/articles/');
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Add Article
	* 
	* @access	public
	*/
	function add_old()
	{
		$this->load->library('form_validation');
		$data['nav'] = 'articles';
		if ( ! $this->auth->check_level(4))
		{
			$data['not_allowed'] = TRUE;
			$this->init_model->display_template('content', $data, 'admin'); 
			return FALSE;
		}
		$this->load->helper(array('form', 'url'));
		$data['options'] = $this->category_model->get_cats_for_select('',0, '', TRUE);
		$data['action'] = 'add';
		
		$this->form_validation->set_rules('article_title', 'lang:kb_title', 'required');
		$this->form_validation->set_rules('article_uri', 'lang:kb_uri', 'alpha_dash');
		$this->form_validation->set_rules('article_keywords', 'lang:kb_keywords', 'trim|xss_clean');
		$this->form_validation->set_rules('article_short_desc', 'lang:kb_short_description', 'trim|xss_clean');
		$this->form_validation->set_rules('article_description', 'lang:kb_description', 'trim|xss_clean');
		$this->form_validation->set_rules('article_display', 'lang:kb_display', 'trim');
		$this->form_validation->set_rules('article_order', 'lang:kb_weight', 'numeric');
		$this->core_events->trigger('articles/validation');
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->init_model->display_template('articles/add', $data, 'admin');
		}
		else
		{
			//success
			$cats = $this->input->post('cat');
			$article_uri = $this->input->post('article_uri', TRUE);
			$data = array(
				'article_uri' => $article_uri, 
				'article_author' => $this->session->userdata('userid'), 
				'article_title' => $this->input->post('article_title', TRUE),
				'article_keywords' => $this->input->post('article_keywords', TRUE),
				'article_short_desc' => $this->input->post('article_short_desc', TRUE),
				'article_description' => $this->input->post('article_description', TRUE),
				'article_display' => $this->input->post('article_display', TRUE),
				'article_order' => $this->input->post('article_order', TRUE)
			);
			$id = $this->article_model->add_article($data);
			if (is_int($id))
			{
				//$tags = $this->input->post('tags');
				//$this->tags_model->insert_tags($id, $tags);
				$this->category_model->insert_cats($id, $cats);
				$this->core_events->trigger('articles/add', $id);
				
				if ($_FILES['userfile']['name'] != "") 
				{
					$target = KBPATH .'uploads/'.$id;
					$this->_mkdir($target);
					$config['upload_path'] = $target;
					$config['allowed_types'] = $this->config->item('attachment_types');
					$this->load->library('upload', $config);
					if ( ! $this->upload->do_upload())
					{
						$this->session->set_flashdata('error', $this->upload->display_errors());
						redirect('admin/articles/edit/'.$id);
					}
					else
					{
						$upload = array('upload_data' => $this->upload->data());
						$insert = array(
							'article_id' => $id, 
							'attach_name' => $upload['upload_data']['file_name'],
							'attach_type' => $upload['upload_data']['file_type'],
							'attach_size' => $upload['upload_data']['file_size']
						);
						$this->db->insert('attachments', $insert);
						$data['attach'] = $this->article_model->get_attachments($id);
					}
				}
			    if (isset($_POST['save']) && $_POST['save']<>"")
			    {
			    	redirect('admin/articles/edit/'.$id);
			    }
			    else
			    {
			    	$this->revert('admin/articles/');
			    }
			}
			else
			{
				$this->revert('admin/articles/');
			}
		}
		
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Grid
	*
	* This is used by the data table js. 
	* 
	* @access	public
	* @return	string
	*/
	public function grid()
	{
		$iTotal = $this->db->count_all('articles');
		
		$this->db->start_cache();
		
		//$this->db->select('user_id, user_ip, user_first_name, user_last_name, user_email, user_username, user_group, user_join_date, user_last_login');
		$this->db->from('articles')
				->join('article2cat', 'articles.article_id = article2cat.article_id', 'left');
		
		// User Level
		if ($this->session->userdata('user_group') == 4)
		{
			$this->db->where('article_author', $this->session->userdata['userid']);
		}
		
		/* Searching */
		if($this->input->post('sSearch') != '')
		{
			$q = $this->input->post('sSearch', TRUE);
			$this->db->orlike('article_title', $q);
			$this->db->orlike('article_short_desc', $q);
			$this->db->orlike('article_description', $q);
			$this->db->orlike('article_uri', $q);
		}
		
		/* Sorting */
		if ($this->input->post('iSortCol_0'))
		{
			$sort_col = $this->input->post('iSortCol_0');
			for($i=0; $i < $sort_col; $i++)
			{
				$this->db->order_by($this->_column_to_field($this->input->post('iSortCol_'.$i)), $this->input->post('iSortDir_'.$i));
			}
		}
		
		$this->db->stop_cache();
		
		$iFilteredTotal = $this->db->count_all_results();
		
		$this->db->start_cache();
		
		/* Limit */
		if ($this->input->post('iDisplayStart') && $this->input->post('iDisplayLength') != '-1' )
		{
			$this->db->limit($this->input->post('iDisplayLength'), $this->input->post('iDisplayStart'));
		}
		elseif($this->input->post('iDisplayLength'))
		{
			$this->db->limit($this->input->post('iDisplayLength'));
		}
		
		$query = $this->db->get();
		
		$output = '{';
		$output .= '"sEcho": '.$this->input->post('sEcho').', ';
		$output .= '"iTotalRecords": '.$iTotal.', ';
		$output .= '"iTotalDisplayRecords": '.$iFilteredTotal.', ';
		$output .= '"aaData": [ ';
		
		foreach ($query->result() as $row)
		{
			$cat = '';
			$cats = $this->articles_model->get_cats_by_article($art_row['article_id']);
			foreach($cats->result() as $row)
			{
				$cat .= anchor('admin/categories/edit/'.$row->cat_id, $row->cat_name).',';
			}
			
			
			$output .= "[";
			$output .= '"'.addslashes($row->article_title).'",';
			$output .= '"'.addslashes($cat).'",';
			$output .= '"'.addslashes(date($this->config->item('short_date_format'), $row->article_date)).'",';
			$output .= '"'.addslashes(date($this->config->item('short_date_format'), $row->article_modified)).'",';
			$output .= '"'.addslashes($row->article_display).'"';
			$output .= "],";
		}
		
		$output = substr_replace( $output, "", -1 );
		$output .= '] }';

		echo $output;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Relate column to field
	*
	* This is used by the data table js. 
	* 
	* @param	string
	* @return	string
	*/
	private function _column_to_field($i)
	{
		if ($i == 0)
		{
			return "user_username";
		}
		elseif ($i == 1)
		{
			return "user_join_date";
		}
		elseif ($i == 2)
		{
			return "user_last_login";
		}
		elseif ($i == 3)
		{
			return 'group_name';
		}
		elseif ($i == 5)
		{
			return 'listing_modified';
		}
		elseif ($i == 6)
		{
			return "listing_price";
		}
	}
}
/* End of file admin_articles.php */
/* Location: ./upload/includes/68kb/modules/kb/controllers/admin_articles.php */ 