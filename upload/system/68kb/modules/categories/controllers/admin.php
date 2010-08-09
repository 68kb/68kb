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
 * Admin Categories Controller
 *
 *
 * @subpackage	Controllers
 * @link		http://68kb.com/user_guide/
 */
class Admin extends Admin_Controller {
	
	protected $_cat_data = array();
	
	// ------------------------------------------------------------------------
	
	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('categories_model');
		
		if ( ! $this->users_auth->check_role('can_manage_categories'))
		{
			show_error(lang('not_authorized'));
		}
		
		$this->load->library('categories/categories_library');
		
		$cats = $this->categories_model->get_categories(TRUE);
		$this->categories_library->category_tree($cats);
		$this->_cat_data = $this->categories_library->get_categories();
		
		$this->data->nav = 'articles';
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Show the main categories grid
	*
	*/
	public function index()
	{
		$this->template->title(lang('lang_browsecats')); // Set site title
		
		$this->load->library('table');
		
		$this->data->header = '<script type="text/javascript" src="'.site_url('admin/js/load/').'"></script>';
		
		$this->benchmark->mark('get_admin_tree_start');
	
		$this->data->categories = $this->_cat_data;
		
		$this->benchmark->mark('get_admin_tree_end');
		
		$this->template->build('admin/category_grid', $this->data); // Pass off to core_template for view
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Edit Category
	*
	*/
	public function edit()
	{
		$this->template->title(lang('lang_editcat'));
		
		$this->data->id = (int) $this->uri->segment(4, 0);
		
		$this->data->cat = $this->categories_model->get_cat($this->data->id, TRUE);
		
		// Remove uri string
		$uri = explode('/', $this->data->cat['cat_uri']);
		$this->data->cat['cat_uri'] = array_pop($uri);
		
		
		$this->benchmark->mark('get_cats_for_select_start');
		$this->data->tree = $this->_cat_data;
		$this->benchmark->mark('get_cats_for_select_end');
		
		$this->data->action = 'modify';
		
		$this->load->helper(array('form', 'url', 'html'));

		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('cat_name', 'lang:lang_title', 'required');
		$this->form_validation->set_rules('cat_uri', 'lang:lang_uri', 'alpha_dash');
		$this->form_validation->set_rules('cat_description', 'lang:lang_description', '');
		$this->form_validation->set_rules('cat_promo', 'lang:lang_promo', '');
		$this->form_validation->set_rules('cat_keywords', 'lang:lang_keywords', '');
		$this->form_validation->set_rules('cat_order', 'lang:lang_weight', 'required|integer');
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->template->build('admin/category_edit', $this->data);
		}
		else
		{
			$this->cache->delete_all('categories_model');
			$cat_id	 = (int) $this->input->post('cat_id', TRUE);
			
			$cat_image = $this->data->cat['cat_image'];
			
			if ($_FILES['userfile']['name'] != '') 
			{ 
				//we are uploading an image
				$this->categories_model->remove_cat_image($cat_id); //remove the original image
				$config['upload_path'] = ROOTPATH . $this->config->item('cat_image_path');
				$config['allowed_types'] = 'gif|jpg|png';
				$config['max_size']	= '100';
				$config['max_width']  = '1024';
				$config['max_height']  = '768';
				$config['remove_spaces'] = TRUE;
				$this->load->library('upload', $config);

				if ( ! $this->upload->do_upload()) 
				{
					$data['error'] = array('error' => $this->upload->display_errors());
					$this->template->build('admin/category_edit', $data);
				} 
				else 
				{
					$img = array('upload_data' => $this->upload->data());
					$cat_image = $img['upload_data']['file_name'];
				}
			}
			
			$data = array(
						'cat_name' 			=> $this->input->post('cat_name', TRUE),
						'cat_uri' 			=> $this->input->post('cat_uri', TRUE),
						'cat_description' 	=> $this->input->post('cat_description', TRUE),
						'cat_image' 		=> $cat_image,
						'cat_allowads' 		=> $this->input->post('cat_allowads', TRUE),
						'cat_parent' 		=> $this->input->post('cat_parent', TRUE),
						'cat_display' 		=> $this->input->post('cat_display', TRUE),
						'cat_order' 		=> $this->input->post('cat_order', TRUE),
						'cat_keywords' 		=> $this->input->post('cat_keywords', TRUE),
						'cat_promo' 		=> $this->input->post('cat_promo', TRUE)
					);
			
			$this->categories_model->edit_category($cat_id, $data);
			$this->session->set_flashdata('msg', lang('lang_settings_saved'));
			redirect('admin/categories/');
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Add Category
	*
	*/
	public function add()
	{
		$this->template->title(lang('lang_add_category'));
		
		$this->benchmark->mark('get_cats_for_select_start');
		$this->data->tree = $this->_cat_data;
		$this->benchmark->mark('get_cats_for_select_end');
		
		$this->data->action = 'add';
		
		$this->data->cat = array('cat_name' => '', 'cat_uri' => '', 'cat_description' => '', 'cat_image' => '', 'cat_allowads' => '','cat_parent' => '','cat_display' => '', 'cat_order' => '', 'cat_keywords' => '', 'cat_promo' => '',
		);
		
		$this->load->helper(array('form', 'url'));

		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('cat_name', 'lang:lang_title', 'required');
		$this->form_validation->set_rules('cat_uri', 'lang:lang_uri', 'alpha_dash');
		$this->form_validation->set_rules('cat_description', 'lang:lang_description', '');
		$this->form_validation->set_rules('cat_promo', 'lang:lang_promo', '');
		$this->form_validation->set_rules('cat_keywords', 'lang:lang_keywords', '');
		$this->form_validation->set_rules('cat_order', 'lang:lang_weight', '');
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->template->build('admin/category_add', $this->data);
		}
		else
		{
			$this->cache->delete_all('categories_model');
			
			$cat_image = '';
			
			if ($_FILES['userfile']['name'] != '') 
			{ 
				//we are uploading an image
				$config['upload_path'] 		= ROOTPATH . $this->config->item('cat_image_path');
				$config['allowed_types'] 	= 'gif|jpg|png';
				$config['max_size']			= '100';
				$config['max_width'] 		= '1024';
				$config['max_height'] 		= '768';
				$config['remove_spaces'] 	= TRUE;
				
				$this->load->library('upload', $config);

				if( ! $this->upload->do_upload()) 
				{
					$data['error'] = array('error' => $this->upload->display_errors());
					$this->template->build('admin/category_add', $data);
				} 
				else 
				{
					$img = array('upload_data' => $this->upload->data());
					$cat_image = $img['upload_data']['file_name'];
				}
			}
			
			if ( ! $cat_order = $this->input->post('cat_order', TRUE))
			{
				$cat_order = 0;
			}
			
			$data = array(
							'cat_name' 			=> $this->input->post('cat_name', TRUE),
							'cat_uri' 			=> $this->input->post('cat_uri', TRUE),
							'cat_description' 	=> $this->input->post('cat_description', TRUE),
							'cat_image' 		=> $cat_image,
							'cat_allowads' 		=> $this->input->post('cat_allowads', TRUE),
							'cat_parent' 		=> $this->input->post('cat_parent', TRUE),
							'cat_display' 		=> $this->input->post('cat_display', TRUE),
							'cat_order' 		=> $cat_order,
							'cat_keywords' 		=> $this->input->post('cat_keywords', TRUE),
							'cat_promo' 		=> $this->input->post('cat_promo', TRUE)
			);
			
			if ( ! $this->categories_model->add_category($data))
			{
				$this->session->set_flashdata('msg', lang('lang_error_record'));
				redirect('admin/categories/');
			}
			
			$this->session->set_flashdata('msg', lang('lang_settings_saved'));
			redirect('admin/categories/');
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Duplicate Category
	*
	*/
	public function duplicate($cat_id)
	{
		if ( ! ctype_digit($cat_id)) 
		{
			$this->session->set_flashdata('msg', lang('lang_error_record'));
			redirect('admin/categories/');
		}
		
		$this->data->cat = $this->categories_model->get_cat($cat_id, TRUE);
		
		// Remove uri string
		$uri = explode('/', $this->data->cat['cat_uri']);
		$this->data->cat['cat_uri'] = array_pop($uri);
		
		
		$this->benchmark->mark('get_cats_for_select_start');
		$this->data->tree = $this->_cat_data;
		$this->benchmark->mark('get_cats_for_select_end');
		
		$this->template->title(lang('lang_duplicate'));
		$this->template->build('admin/category_add', $this->data);
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Delete Category
	* 
	* 
	*/
	public function delete($cat_id)
	{
		if ( ! $this->users_auth->check_role('can_delete_categories'))
		{
			show_error(lang('not_authorized'));
		}
		
		$data['nav'] = 'listings';
		
		$data['site_title'] = lang('lang_deletecat');
		
		if ( ! ctype_digit($cat_id)) 
		{
			$this->session->set_flashdata('msg', lang('lang_error_record'));
			redirect('admin/categories/');
		}
		
		$this->load->model('listings/listings_model');
		
		$cat_id = (int) $cat_id;
		$data['id'] = $cat_id;
		
		// First find out the number of listings in the cat
		$this->db->from('listings')->where('listing_category', $cat_id);
		$data['total'] = $this->db->count_all_results();
		
		if ($data['total'] == 0) // no listings so delete it
		{
			$this->categories_model->delete_category($cat_id);
			$this->session->set_flashdata('msg', lang('lang_settings_saved'));
			redirect('admin/categories/');
		}
		
		$data['tree'] = $this->_cat_data;

		$this->load->helper(array('form', 'url'));

		$this->load->library('form_validation');

		$this->form_validation->set_rules('new_cat', 'lang:lang_title', 'required');

		if ($this->form_validation->run() == FALSE)
		{
			$this->template->build('admin/category_delete', $data);
		}
		else
		{
			$this->cache->delete_all('categories_model');
			
			$new_cat = (int) $this->input->post('new_cat');
			
			// Update the listings:
			$update_data = array('listing_category' => $new_cat);
			$this->db->where('listing_category', $data['id']);
			$this->db->update('listings', $update_data);
			
			// Now Delete it
			$this->categories_model->delete_category($cat_id);
			$this->session->set_flashdata('msg', lang('lang_settings_saved'));
			redirect('admin/categories/');
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Delete category image 
	* 
	* @param 	int
	* @return 	bool
	*/
	public function delete_image($cat_id)
	{
		if ( ! ctype_digit($cat_id)) // not a number
		{
			$this->session->set_flashdata('msg', lang('lang_error_record'));
			redirect('admin/categories/edit/'.$cat_id);
		}
		
		$this->categories_model->remove_cat_image($cat_id); //remove the original image
		$this->session->set_flashdata('msg', lang('lang_settings_saved'));
		redirect('admin/categories/edit/'.$cat_id);
	}
}

/* End of file admin.php */
/* Location: ./upload/includes/68kb/modules/categories/controllers/admin.php */ 