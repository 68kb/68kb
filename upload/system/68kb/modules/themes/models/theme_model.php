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
 * Template Model
 *
 * @subpackage	Models
 * @link		http://68kb.com/user_guide/
 */
class Theme_model extends CI_Model {

	private $theme_dir = '';
	
	/**
	 * Constructor
	 *
	 * @return 	void
	 */
	public function __construct()
	{
		parent::__construct();
		log_message('debug', 'Theme model Initialized');
		$this->theme_dir = ROOTPATH.'themes/';
	}
	
	// ------------------------------------------------------------------------
		
	/**
	 * Activate a theme
	 *
	 * @param	string
	 * @return	bool
	 */
	public function activate($dir = '')
	{
		if ( ! file_exists($this->theme_dir.$dir.'/layout.php') OR $dir == '')
		{
			show_error(lang('lang_missing_file'));
		}
		
		$data = array('option_value' => $this->security->xss_clean($dir));
		
		$this->db->where('option_name', 'site_theme');
		$this->db->update('settings', $data);
		
		return TRUE;
	}
	
	// ------------------------------------------------------------------------
	
	/**
 	 * Load Active Theme
 	 *
 	 * Load the config file for the active theme.
 	 *
 	 * @access	public
 	 * @param	string	the file
 	 * @return	bool
 	 */
	public function load_active_theme($theme)
	{
		$preview = $this->theme_dir.$theme.'/preview.png';
		$data['theme']['dir'] = $theme;
		
		if (file_exists($preview))
		{
			$data['theme']['preview'] = base_url().'themes/'.$theme.'/preview.png';
		}
		else
		{
			$data['theme']['preview'] = base_url().'themes/cp/images/nopreview.gif';
		}
		
		if (file_exists($this->theme_dir.$theme.'/config.php'))
		{
			include($this->theme_dir.$theme.'/config.php');
			

			if (file_exists($this->theme_dir.$theme.'/admin.php')) 
			{
				$data['theme']['admin'] = TRUE;
			}
			else
			{
				$data['theme']['admin'] = FALSE;
			}
		}
		else
		{
			$data['theme']['name'] = $theme;
			$data['theme']['description'] = '';
			$data['theme']['admin'] = FALSE;
		}
		
		return $data['theme'];
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Load All Other Themes
	 *
	 * Scans the themes directory and loads the config files if found.
	 *
	 * @access	public
	 * @param	string
	 * @return	arr
	 */
	public function load_themes($default)
	{
		// set the folders that are not avialble for theme files.
		$not_available = array(".", "..", ".DS_Store", ".svn", "modules", "index.htm", "cp");
		
		// Set available to blank as a fall back.
		$available_theme = '';
		
		// Load the benchmark so we can how slow this is.
		$this->benchmark->mark('load_themes_start');
		
		// Set the counter to 0. Used in the array built below.
		$i=0;
		
		if ($handle = opendir($this->theme_dir)) 
		{
			while (false !== ($file = readdir($handle))) 
			{
				if (is_dir($this->theme_dir.$file) && $file != $default && ! in_array($file, $not_available)) 
				{
					$preview = $this->theme_dir.$file.'/preview.png';
					if (file_exists($preview))
					{
						$preview = base_url().'themes/'.$file.'/preview.png';
					}
					else
					{
						$preview = base_url().'themes/cp/images/nopreview.gif';
					}
					
					if (file_exists($this->theme_dir.$file.'/config.php'))
					{
						include($this->theme_dir.$file.'/config.php');
						$available_theme[$i]['title']=$data['theme']['name'];
						$available_theme[$i]['description']=$data['theme']['description'];
						$available_theme[$i]['version']=$data['theme']['version'];
						$available_theme[$i]['file'] = $file;
						$available_theme[$i]['preview'] = $preview;
						$available_theme[$i]['name']=$file;
						$available_theme[$i]['preview']=$preview;
						if (file_exists($this->theme_dir.$file.'/admin.php')) 
						{
							$available_theme[$i]['admin'] = TRUE;
						}
						else
						{
 							$available_theme[$i]['admin']= FALSE;
						}
						unset($data);
					}
					else
					{
						$available_theme[$i]['title']=$file;
						$available_theme[$i]['preview'] = $preview;
						$available_theme[$i]['file'] = $file;
						$available_theme[$i]['admin']= FALSE;
					}
				}
				++$i;
			}
			closedir($handle);
		}
		
		$this->benchmark->mark('load_themes_end');
		
		return $available_theme;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Show Theme Admin File
	 *
	 * See the developer theme admin file for usage.
	 *
	 * @param 	string
	 * @return 	mixed
	 * @access 	public
	 */
 	public function show_admin($theme_name)
 	{
		$theme_name = (string) $theme_name;
		
		// load language
		if (file_exists($this->theme_dir.$theme_name.'/language/'.$this->config->item('language').'/'.$theme_name.'_lang.php'))
		{
			$this->lang->load($theme_name, '', FALSE, $this->theme_dir.$theme_name.'/');
		}
		
		// load admin
		if (file_exists($this->theme_dir . $theme_name.'/admin.php'))
		{
			return $this->theme_dir . $theme_name.'/admin.php';
		}
		
		return FALSE;
 	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Check if a file is writable.
	 * 
	 * @param	string
	 * @return	boolean
	 */
	public function is_really_writable($file)
	{
		$this->load->helper('file');
		if ( ! is_really_writable($file))
		{
			if ( ! write_file($file, '', 'w')) // lets try to create the file
			{
				return FALSE;
			}
		}
		return TRUE;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Write the options to the css file
	 * 
	 * @param	string
	 * @param	string
	 * @return	boolean
	 */
	public function write_css($file, $data)
	{
		if (write_file($file, $data, 'w'))
		{
			return TRUE;
		}
		return FALSE;
	}
}

/* End of file theme_model.php */
/* Location: ./upload/includes/68kb/modules/themes/models/theme_model.php */ 