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
 * Admin Settings Controller
 *
 * @subpackage	Controllers
 * @link		http://68kb.com/user_guide/admin/settings.html
 *
 */
class Admin extends Admin_Controller
{
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
		
		$this->load->helper('cookie');
		
		$this->load->dbutil();
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Index Controller
	 *
	 * @access	public
	 */
	public function index()
	{
		$data['nav'] = 'settings';
		
		$this->template->title = lang('lang_settings');
		
		$this->template->build('admin/settings_main', $data); 
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Save Settings
	 * 
	 * @access	public
	 */
	public function general()
	{
		if ( ! $this->users_auth->check_role('can_manage_settings'))
		{
			show_error(lang('not_authorized'));
		}
		
		$this->load->library('form_validation');
		
		$this->load->helper('form');
		
		$data['nav'] = 'settings';
		
		$this->db->select('option_name,option_value')
					->from('settings')
					->where('option_group', 'site');
					
		$query = $this->db->get();
		
		// Assign the value to the name. 
		foreach ($query->result() as $k => $row)
		{	
			$data[$row->option_name] = $row->option_value;
		}
		
		$this->form_validation->set_rules('site_name', 'lang:lang_title', 'required');
		$this->form_validation->set_rules('site_email', 'lang:lang_email', 'required|valid_email');
		$this->form_validation->set_rules('site_keywords', 'lang:lang_site_meta_keywords', '');
		$this->form_validation->set_rules('site_description', 'lang:lang_description', '');
		$this->form_validation->set_rules('site_max_search', 'lang:lang_max_search', 'required|numeric');
		$this->form_validation->set_rules('site_cache_time', 'lang:lang_cache_time', 'numeric');
		$this->form_validation->set_rules('site_bad_words', 'lang:lang_badwords', 'trim');
		
		// Call any events
		$this->events->trigger('settings/validation');
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->template->build('admin/general', $data); 
		}
		else
		{
			$this->events->trigger('settings/save', $_POST);
			
			foreach ($_POST as $key => $value)
			{
				if ($key == 'site_bad_words')
				{
					$value = str_replace(' ', '', $value);
				}
				$data = array( 
					'option_value' => $this->security->xss_clean($value)
				);
				$this->db->where('option_name', $key);
				$this->db->update('settings', $data);
			}
			
			$this->session->set_flashdata('msg', lang('lang_settings_saved'));
			redirect('admin/settings/general');
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Repair DB
	 *
	 * @access	public
	 */
	public function repair()
	{
		$data['nav'] = 'settings';
		
		$tables = $this->db->list_tables();
		
		echo '<div id="facebox" style="width: 400px;"><h2 class="icon_success">'.lang('lang_repair_success').'</h2>';
		$i=0;
		
		foreach ($tables as $table)
		{
			if ($this->dbutil->repair_table($table))
			{
				$i++;
				$tb[] = $table;
			}
		} 
		
		echo 'Successfully repaired '. $i.' tables.</div>';
		
		$data['table'] = $tb;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Optimize the db
	 *
	 * @access	public
	 */
	public function optimize()
	{
		$data['nav'] = 'settings';
		
		$tables = $this->db->list_tables();
		
		echo '<div id="facebox" style="width: 400px;"><h2 class="icon_success">'.lang('lang_optimize_success').'</h2>';
		$i=0;
		foreach ($tables as $table)
		{
			if ($this->dbutil->optimize_table($table))
			{
				$i++;
				$tb[] = $table;
				//echo '<li>'.$table.'</li>';
			}
		} 
		echo 'Successfully optimized '. $i.' tables.</div>';
	}
	
	
	// ------------------------------------------------------------------------
	
	/**
	 * Remove cache files
	 *
	 * @access	public
	 */
	public function delete_cache()
	{
		$this->load->helper('file');
		
		delete_files($this->config->item('cache_path'), TRUE);
		$this->cache->delete_all();
		
		echo '<div id="facebox" style="width: 400px;"><h2 class="icon_success">'.lang('lang_cache_deleted').'</h2></div>';
	}
	
	
	// ------------------------------------------------------------------------
	
	/**
	 * Backup the databse
	 *
	 * @access	public
	 */
	public function backup()
	{
		$backup =& $this->dbutil->backup();
		
		$name = '68kb-'.time().'.gz';
		
		// Load the file helper and write the file to your server
		$this->load->helper('file');
		write_file(ROOTPATH .'uploads/'.$name, $backup);

		// Load the download helper and send the file to your desktop
		$this->load->helper('download');
		force_download($name, $backup);
	}
	
}

/* End of file admin.php */
/* Location: ./upload/includes/68kb/modules/settings/controllers/admin.php */ 