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
 * Admin Theme Controller
 *
 *
 * @subpackage	Controllers
 * @link		http://68kb.com/user_guide/
 *
 */
class Admin extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('theme_model');
		
		if ( ! $this->users_auth->check_role('can_manage_themes'))
		{
			show_error(lang('not_authorized'));
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Show the grid
	*/
	public function index()
	{
		$this->load->model('theme_model');
		
		$settings['theme'] = $this->settings->get_setting('site_theme');
		
		$data['site_title'] = lang('lang_theme_settings');
		
		$data['nav'] = 'settings';
		
		$data['active'] = $this->theme_model->load_active_theme($settings['theme']);
		
		$data['available_themes'] = $this->theme_model->load_themes($settings['theme']);
		
		$this->template->build('admin/template', $data);
	}
	
	// ------------------------------------------------------------------------
	
	public function activate($activate = '')
	{
		if ($activate !== '')
		{
			$msg = $this->theme_model->activate($activate);
			
			if ($msg === TRUE)
			{
				$this->session->set_flashdata('msg', lang('lang_settings_saved'));
				redirect('admin/themes/');
			}
			elseif ($msg != '')
			{
				$this->session->set_flashdata('msg', $msg);
				redirect('admin/themes/');
			}
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Show a theme admin file.
	 *
	 * @access	public
	 */
	public function edit_theme()
	{
		$this->load->model('theme_model');
		
		$nav = $this->events->trigger('admin/themes/show/nav');
		$data['nav'] = ( empty($nav) ) ? 'settings': $nav;
		
		$name = $this->uri->segment(4, 0);
		
		$this->load->library('form_validation');
		$this->load->helper('form');
		
		$file = $this->theme_model->show_admin($name);
		
		if ($file !== FALSE)
		{
			$data['header'] = '
				<script type="text/javascript" language="javascript" src="'.base_url().'js/jscolor/jscolor.js"></script>
			';
			$data['file'] = file_get_contents($file);
			$this->template->build('admin/show', $data);
		}
		else
		{
			redirect('admin');
		}
	}
	
}

/* End of file admin.php */
/* Location: ./upload/includes/68kb/modules/themes/controllers/admin.php */ 