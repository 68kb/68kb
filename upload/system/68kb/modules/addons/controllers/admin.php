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
 * Admin Modules Controller
 *
 * @subpackage	Controllers
 * @link		http://68kb.com/user_guide/developer/modules.html
 *
 */
class Admin extends Admin_Controller {
	
	// ------------------------------------------------------------------------
	
	/**
	* Constructor
	*
	*/
	public function __construct()
	{
		parent::__construct();
		
		$this->load->helper(array('form', 'url'));

		$this->load->model('addons_model');
		
		if ( ! $this->users_auth->check_role('can_manage_modules'))
		{
			show_error(lang('not_authorized'));
		}
		
		$this->data->nav = 'addons';
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Show manage modules page
	*
	*/
	public function index()
	{	
		$this->load->library('table');
		
		$this->template->set_metadata('stylesheet', base_url() . 'themes/cp/css/smoothness/jquery-ui.css', 'link');
		$this->template->set_metadata('js', 'js/dataTables.min.js', 'js_include');
		
		$this->benchmark->mark('load_active_start');
		$this->data->active = $this->addons_model->load_active();
		$this->benchmark->mark('load_active_end');
		
		$this->benchmark->mark('load_inactive_start');
		$this->data->inactive = $this->addons_model->load_inactive();
		$this->benchmark->mark('load_inactive_end');
		
		$this->template->title(lang('lang_modules'));
		
		// Get rss feed
		//$this->load->model('pages/pages_model');
		$data['mod_news'] = ''; //$this->pages_model->get_rss_feed('http://classifiedmods.com/rss/');
		
		$this->template->build('admin/modules_grid', $this->data);
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Activate A Module
	*
	* @param	string
	*/
	public function activate($module_directory = '')
	{
		if ($module_directory == '')
		{
			redirect('admin/modules/');
		}
		
		$this->cache->delete('module_events');
		$msg = $this->addons_model->activate($module_directory);

		if ($msg === TRUE)
		{
			$msg = lang('lang_activated');
		}
		$this->session->set_flashdata('msg', $msg);
		
		redirect('admin/addons/');
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Upgrade Module
	*
	* @param	string
	*/
	public function upgrade($module_name = '')
	{
		if ($module_name != '')
		{
			$this->cache->delete('module_events');
			$module_name = (string) $module_name;
			$msg = $this->addons_model->init_module($module_name, 'upgrade');
			if ($msg === TRUE)
			{
				$msg = lang('lang_upgraded');
			}
			$this->session->set_flashdata('msg', $msg);
		}
		redirect('admin/addons/');
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Deactivate Module
	*
	* @param	string
	*/
	public function deactivate($module_name = '')
	{
		if ($module_name != '')
		{
			$this->cache->delete('module_events');
			$module_name = (string) $module_name;
			$msg = $this->addons_model->init_module($module_name, 'deactivate');
			if ($msg == TRUE)
			{
				$msg = lang('lang_deactivated');
			}
			$this->session->set_flashdata('msg', $msg);
		}
		redirect('admin/addons/');
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Uninstall Module
	*
	* @param	string
	*/
	public function uninstall($module_name = '')
	{
		if ($module_name !== '')
		{
			$this->cache->delete('module_events');
			$module_name = (string) $module_name;
			$this->addons_model->init_module($module_name, 'uninstall');
			$this->session->set_flashdata('msg', lang('lang_deactivated'));
		}
		redirect('admin/addons/');
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Try to remove the module directory
	 *
	 */
	public function delete()
	{
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		
		$this->template->title(lang('lang_modules'));
		
		$this->data->module_directory = $this->uri->rsegment(3);
		
		$this->form_validation->set_rules('module_directory', 'Module Directory', 'required|callback_module_directory_check');
		$this->form_validation->set_rules('delete', 'Delete', 'required');
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->template->build('admin/modules_delete', $this->data);
		}
		else
		{
			$delete = $this->input->post('delete');
			$module_directory = $this->input->post('module_directory');
			if ($delete == 'all')
			{
				if ($this->addons_model->remove_dir($module_directory))
				{
					$this->session->set_flashdata('msg', lang('lang_remove_success'));
				}
				else
				{
					$this->session->set_flashdata('msg', lang('lang_remove_error'));
				}
			}
			else
			{
				$msg = $this->addons_model->init_module($module_directory, 'uninstall');
				if ($msg === TRUE)
				{
					$msg = lang('lang_deleted');
				}
				$this->session->set_flashdata('msg', $msg);
			}
			
			redirect('admin/addons/');
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Used in form validation to check directory
	*/
	public function module_directory_check($module_directory)
	{
		if ( ! $this->addons_model->exists($module_directory))
		{
			$this->form_validation->set_message('module_directory_check', 'The Module Directory can not be found.');
			return FALSE;
		}
		return TRUE;
	}
	
	
	// ------------------------------------------------------------------------
	
	/**
	 * Show a modules help file.
	 *
	 * This is used to load the file incase includes folder is moved outside 
	 * the web root.
	 *
	 * @access	public
	 */
	public function docs()
	{	
		$nav = $this->events->trigger('admin/modules/docs/nav');
		$this->data->nav = ( empty($nav) ) ? 'settings': $nav;
		
		$name = $this->uri->segment(4,0);

		$file = $this->addons_model->show_docs($name);

		if ($file)
		{
			$this->load->helper('markdown');
			$this->data->file = file_get_contents($file);
			$this->template->build('admin/docs', $this->data);
		}
		else
		{
			redirect('admin/addons');
		}
	}
}
/* End of file admin.php */
/* Location: ./upload/includes/68kb/modules/addons/controllers/admin.php */ 