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
 * Admin Image Manager Controller
 *
 * @subpackage	Controllers
 * @link		http://68kb.com/user_guide/
 *
 */
class Admin extends Admin_Controller
{
	protected $path = '';
	
	protected $field = '';
	
	/**
	 * Constructor
	 *
	 * Requires needed models and helpers.
	 * 
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->load->helper('form');
		$this->load->library('image_lib');
		$this->template->set_layout('admin/layout');
		$this->path = $this->config->item('content_image_path');
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Index Controller
	 *
	 * @access	public
	 * @param	string - Textarea Field
	 */
	public function index($field = '')
	{
		if ($field == '')
		{
			show_error('You must supply a field.');
		}
		
		$data['nav'] = 'upload';
		$data['field'] = $field;
		$this->template->set_layout('admin/layout');
		$this->template->build('admin/main', $data); 
	}
	
	public function edit()
	{
		$image = $this->input->post('image');
		$field = $this->input->post('field');
		
		if ( ! $image || ! $field)
		{
			show_error('Image and field are required');
		}
		
		$full_name = str_replace('_thumb', '', $image);
		$full_name = str_replace('/thumbs', '', $full_name);
		$data['image'] = $full_name;
		
		$data['nav'] = 'browse';
		$data['field'] = $field;
		$data['thumb'] = $image;
		
		list($data['width'], $data['height'], $data['type'], $data['attr']) = getimagesize($data['image']);
		
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('width', 'Width', 'required|numeric');
		$this->form_validation->set_rules('height', 'Height', 'required|numeric');
				
		if ($this->form_validation->run() == FALSE)
		{
			$this->template->build('admin/edit', $data);
		}
		else
		{
			$width = $this->input->post('width');
			$height = $this->input->post('height');
			$orig_image = $this->input->post('orig_image');
			
			$config['image_library']	= $this->config->item('image_library');
			$config['source_image']		= $orig_image;
			$config['maintain_ratio'] 	= TRUE;
			$config['thumb_marker'] 	= '';
			$config['width']	 		= $width;
			$config['height']			= $height;

			$this->image_lib->initialize($config); 

			if ( ! $this->image_lib->resize())
			{
				$error =  $this->image_lib->display_errors();
				$this->image_lib->clear();
				show_error($error);
			}
			
			list($data['width'], $data['height'], $data['type'], $data['attr']) = getimagesize($orig_image);
			$this->template->build('admin/edit', $data);
		}
		
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Browse Images
	 *
	 * @param	string - Textarea Field
	 */
	public function browse($field = '')
	{
		$data['nav'] = 'browse';
		
		$data['field'] = $field;
		
		$images = array();
		
		$i = 0;
		
		$map = directory_map($this->path.'thumbs', TRUE);
		
		if ($map)
		{
			foreach ($map AS $item)
			{
				if (strpos($item, '_thumb') !== FALSE)
				{
					$full_name = str_replace('_thumb', '', $item);
					$images[$i]['src'] = $this->path.'thumbs/'.$item;
					$images[$i]['full'] = $this->path.$full_name;
					$images[$i]['name'] = $item;
				}
				$i++;
			}
			$data['images'] = $images;
		}
		else
		{
			$data['images'] = array();
		}
		
		$this->template->build('admin/browse', $data); 
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Upload Image
	*
	*/
	public function upload()
	{
		if ( ! $path = $this->input->post('folder'))
		{
			$path = $this->path;
		}
		
		$image['field'] = $this->input->post('field');
		
		$config['upload_path'] = ROOTPATH . $path;
		$config['allowed_types'] = $this->config->item('allowed_types');
		$config['max_size']	= $this->config->item('max_size');
		$config['max_width']  = $this->config->item('max_width');
		$config['max_height']  = $this->config->item('max_height');

		$this->load->library('upload', $config);
		
		if ( ! $this->upload->do_upload('userfile'))
		{
			$image['error'] = $this->upload->display_errors();
			$image['nav'] = 'upload';
			$this->template->build('admin/main', $image); 
		}
		else
		{
			$data = array('upload_data' => $this->upload->data());
			
			// Process the image and do the resizing
			$image = $this->_resize_image($data);
			$image['thumb'] = base_url().$path.'thumbs/'.$image['thumb'];
			$image['full'] = $path . $data['upload_data']['file_name'];
			$image['name'] = $data['upload_data']['raw_name'].$data['upload_data']['file_ext'];
			$image['nav'] = 'upload';
			$image['field'] = $this->input->post('field');
			$this->template->build('admin/main', $image); 
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Resize and make a thumbnail
	 *
	 * @param	data
	 *
	 */
	private function _resize_image($data)
	{
		$image = array();
		
		// First is thumbnail then main image.
		// This is the only way I could get it going for some reason.
		$width = '100';
		$height = '100';
		if ($data['upload_data']['image_width'] > $width && $data['upload_data']['image_height'] > $height)
		{
			$config['image_library']	= $this->config->item('image_library');
			$config['source_image']		= $data['upload_data']['full_path'];
			$config['create_thumb'] 	= TRUE;
			$config['maintain_ratio'] 	= TRUE;
			$config['thumb_marker'] 	= '_thumb';
			$config['new_image'] 		= $this->path .'thumbs/';
			$config['width']	 		= $width;
			$config['height']			= $height;

			$this->image_lib->initialize($config); 
			
			if ( ! $this->image_lib->resize())
			{
				$error =  $this->image_lib->display_errors();
				$this->image_lib->clear();
				return $error;
			}
			$image['thumb'] = $data['upload_data']['raw_name'].'_thumb'.$data['upload_data']['file_ext'];
		}
		else
		{
			$image['thumb'] = $data['upload_data']['raw_name'].$data['upload_data']['file_ext'];
		}
		
		return $image;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Remove an image from ajax call
	 */
	public function remove_image()
	{
		if (IS_AJAX)
		{
			$id = $this->input->post('id');
			if ($this->listings_model->delete_image($id))
			{
				return TRUE;
			}
		}
		return FALSE;
	}
	
}

/* End of file admin.php */
/* Location: ./upload/includes/68kb/modules/image_manager/controllers/admin.php */ 