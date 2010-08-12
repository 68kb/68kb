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
 * Admin Users Controller
 *
 * @subpackage	Controllers
 * @link		http://68kb.com/user_guide/admin/users.html
 *
 */
class Admin extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();
		
		redirect('admin/');
	}
}
/* End of file admin.php */
/* Location: ./upload/system/68kb/modules/kb/controllers/admin.php */ 