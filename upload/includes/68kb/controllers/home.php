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
 * Home Controller
 *
 *
 * @subpackage	Controllers
 * @link		http://68kb.com/user_guide/
 *
 */
class Home extends Front_Controller {

	public function __construct()
	{
		parent::__construct();	
	}
	
	public function index()
	{
		$this->events->trigger('home_controller');
		$data = array();
		$this->template->build('home', $data);
	}
}

/* End of file home.php */
/* Location: ./upload/includes/68kb/controllers/home.php */ 