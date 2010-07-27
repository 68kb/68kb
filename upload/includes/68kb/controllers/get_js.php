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
 * JS Controller
 *
 *
 * @subpackage	Controllers
 * @link		http://68kb.com/user_guide/
 *
 */
class Get_js extends Controller
{
    public function __construct()
	{
		parent::__construct();
		$this->load->library('settings/settings');
	}
    
	// ------------------------------------------------------------------------
	
	/**
	* Index Method
	*
	* This gets the js file and strips out the version number and get_js from
	* the file name. This is for caching js files. 
	*
	* This is used so we can include js files from a view file so that we can 
	* use the language file. 
	* 
	* To call it use: 
	* <pre><script type="text/javascript" src="<?php echo site_url('get_js/admin/js/base/'.$settings['script_version']);?>"></script></pre>
	*/
	public function index()
	{
		$this->output->set_header('Content-type: text/javascript');
        $this->output->set_header('Expires: '.gmdate("D, d M Y H:i:s", time()+315360000).' GMT');
        $this->output->set_header('Cache-Control: max-age=315360000');
		
 		$file_name = str_replace("/get_js", "", $this->uri->uri_string());

		$version = $this->settings->get_setting('script_version');
		
		$file_name = str_replace('/'.$version, '', $file_name);

		if ($file_name)
		{
			echo $this->template->build($file_name, array(), TRUE);
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Remap everything to index()
	*
	* @uses index()
	*
	**/
	public function _remap()
	{
		$this->index();
	}
}
/* End of file get_js.php */
/* Location: ./upload/includes/68kb/controllers/get_js.php */ 