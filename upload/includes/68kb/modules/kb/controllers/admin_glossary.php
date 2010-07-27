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
 * @link		http://68kb.com/user_guide/
 *
 */
class Admin_glossary extends Admin_Controller {

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
		$data['nav'] = 'glossary';
		
		$this->template->set_metadata('stylesheet', base_url() . 'themes/cp/css/smoothness/jquery-ui.css', 'link');
		$this->template->set_metadata('js', 'js/dataTables.min.js', 'js_include');
		
		$this->template->title = lang('lang_manage_glossary');
		
		$this->template->build('admin/glossary/grid', $data); 
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* add term
	*
	*/
	public function add()
	{
		$data['nav'] = 'glossary';
		
		$this->template->title(lang('lang_add_term'));
		$data['action'] = 'add';
		
		$this->load->helper(array('form', 'url', 'html'));
		$this->load->library('form_validation');
		$this->form_validation->set_rules('g_term', 'lang:lang_title', 'required');
		$this->form_validation->set_rules('g_definition', 'lang:lang_definition', 'required');
		$this->events->trigger('glossary/validation');
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->template->build('admin/glossary/form', $data);
		}
		else
		{
			$data = array(
				'g_term' => $this->input->post('g_term', TRUE),
				'g_definition' => $this->input->post('g_definition', TRUE)
			);
			
			if ($this->db->insert('glossary', $data)) 
			{
				$this->session->set_flashdata('msg', lang('lang_settings_saved'));
				redirect('admin/kb/glossary/');
			}
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* edit term
	*
	*/
	public function edit($id = '')
	{
		if ( ! is_numeric($id))
		{
			redirect('admin/kb/glossary');
		}
		$id = (int) $id;
		
		$data['nav'] = 'glossary';
		
		$this->db->from('glossary')->where('g_id', $id);
		$query = $this->db->get();
		$data['row'] = $query->row_array();
		$query->free_result();
		
		$this->template->title(lang('lang_edit_term'));
		
		$data['action'] = 'add';
		
		$this->load->helper(array('form', 'url', 'html'));
		$this->load->library('form_validation');
		$this->form_validation->set_rules('g_term', 'lang:lang_title', 'required');
		$this->form_validation->set_rules('g_definition', 'lang:lang_definition', 'required');
		$this->events->trigger('glossary/validation');
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->template->build('admin/glossary/form', $data);
		}
		else
		{
			$data = array(
				'g_term' => $this->input->post('g_term', TRUE),
				'g_definition' => $this->input->post('g_definition', TRUE)
			);
			$this->db->where('g_id', $id);
			if ($this->db->update('glossary', $data)) 
			{
				$this->session->set_flashdata('msg', lang('lang_settings_saved'));
				redirect('admin/kb/glossary/');
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
		$iTotal = $this->db->count_all('glossary');
		
		$this->db->start_cache();
		
		$this->db->from('glossary');
		
		/* Searching */
		if($this->input->post('sSearch') != '')
		{
			$q = $this->input->post('sSearch', TRUE);
			$this->db->orlike('g_term', $q);
			$this->db->orlike('g_definition', $q);
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
		else
		{
			$this->db->order_by('g_id', 'desc');
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

		foreach ($query->result_array() as $row)
		{
			
			$title = anchor('admin/kb/glossary/edit/'.$row['g_id'], $row['g_term']);
			$output .= "[";
			$output .= '"'.addslashes($title).'",';
			$output .= '"'.addslashes(character_limiter($row['g_definition'], 50)).'",';
			$output .= '"<input type=\"checkbox\" name=\"g_id[]\" value=\"'.$row['g_id'].'\" />"';
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
			return "g_term";
		}
		elseif ($i == 1)
		{
			return "g_definition";
		}
	}
}
/* End of file admin_glossary.php */
/* Location: ./upload/includes/68kb/modules/kb/controllers/admin_glossary.php */ 