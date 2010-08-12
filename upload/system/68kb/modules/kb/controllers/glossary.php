<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 68KB
 *
 * An open source knowledge base script
 *
 * @package		68kb
 * @author		Eric Barnes (http://ericlbarnes.com)
 * @copyright	Copyright (c) 2009, 68 Designs, LLC
 * @license		http://68kb.com/user_guide/license.html
 * @link		http://68kb.com
 * @since		Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * Glossary Controller
 *
 * Handles the glossary page
 */
class Glossary extends Front_Controller
{
	function __construct()
	{
		parent::__construct();
		log_message('debug', 'Glossary Controller Initialized');
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Index Controller
	*
	* Show the home page
	*
	* @access	public
	*/
	function index()
	{
		$this->template->title($this->lang->line('lang_glossary'));
		
		$this->db->from('glossary')->orderby('g_term', 'asc');
		$query = $this->db->get();
		$data['glossary'] = $query;
		$data['letter'] = range('a', 'z');
		
		$this->template->build('glossary', $data);
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Term
	*
	* Find the key term
	*
	* @access	public
	*/
	function term($term='')
	{
		$term = $this->security->xss_clean($term);
		$this->db->from('glossary');
		if ($term == 'sym') 
		{
			$this->db->where('g_term LIKE', '.%');
			$this->db->orwhere('g_term LIKE', '0%');
			$this->db->orwhere('g_term LIKE', '1%');
			$this->db->orwhere('g_term LIKE', '2%');
			$this->db->orwhere('g_term LIKE', '3%');
			$this->db->orwhere('g_term LIKE', '4%');
			$this->db->orwhere('g_term LIKE', '5%');
			$this->db->orwhere('g_term LIKE', '6%');
			$this->db->orwhere('g_term LIKE', '7%');
			$this->db->orwhere('g_term LIKE', '8%');
			$this->db->orwhere('g_term LIKE', '9%');
		} 
		else 
		{
			$this->db->where('g_term LIKE', $term.'%');
		}
		$query = $this->db->get();
		$data['glossary'] = $query;
		$data['letter'] = range('a', 'z');
		
		$this->template->title($this->lang->line('lang_glossary'));
		
		$this->template->build('glossary', $data);
	}
}

/* End of file glossary.php */
/* Location: ./upload/includes/application/controllers/glossary.php */