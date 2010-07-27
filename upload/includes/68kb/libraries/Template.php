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
 * Template Library
 * 
 * Modified for our use from:
 * http://github.com/philsturgeon/codeigniter-template
 *
 * @subpackage	Libraries
 * @link		http://68kb.com/user_guide/
 *
 */
class Template
{
	// Modular Extensions
	private $_module = '';
	private $_controller = '';
	private $_method = '';
    
	// Themes
	private $_theme = '';
	private $_layout = FALSE;
	private $_in_admin = FALSE;
	
	// HTML HEAD
	private $_title = '';
	private $_keywords = '';
	private $_description = '';
	private $_title_separator = ' | ';
	private $_breadcrumbs = array();
	private $_metadata = array();
	private $_modules = array();
    
	// Parsing and caching
	private $_parser_enabled = TRUE;
	
	// Global CI object
	private $_ci;
	
	/**
	 * Constructor
	 */
	function __construct() 
	{
		$this->_ci = CI_Base::get_instance();
		log_message('debug', 'Template class Initialized');

    	// Work out the controller and method
    	if (method_exists($this->_ci->router, 'fetch_module'))
    	{
    		$this->_module 	= $this->_ci->router->fetch_module();
    	}
    	
        $this->_controller	= $this->_ci->router->fetch_class();
        $this->_method 		= $this->_ci->router->fetch_method();
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Build the template
	*
	* @param	string
	* @param	array
	* @param	bool
	*
	* @return 	string
	*/
	public function build($view = '', $data = array(), $return = FALSE)
	{
		$this->_ci->benchmark->mark('template_build_start');
		
		// setup common template vars
        $template['site_title']			= $this->_get_title();
        $template['site_keywords']		= $this->_get_keywords();
        $template['site_description']	= $this->_get_description();
		$template['breadcrumbs']		= $this->_breadcrumbs;
		$template['head_data']			= implode("\n\t", $this->_metadata);
		$template['theme']				= $this->_theme;
		$template['settings'] 			= $this->_ci->settings->get_settings();
		
		// Merge the data array with the template array
        $template = array_merge($data, $template);

		// lets set the template path for images and such
		if ($this->_in_admin)
		{
			$template['template'] = base_url() . 'themes/cp/';
			//set the default admin nav
			if ( ! isset($template['nav'])) 
			{
				$template['nav'] = 'settings';
			}
			$template['modules'] = $this->_modules;
		}
		else 
		{
			$template['template'] = base_url() . 'themes/'. $this->_theme;
			$template['module'] = $this->_ci->router->fetch_module();
		}
		
		// Add the full array for all views
		// http://codeigniter.com/forums/viewthread/44916/
		$this->_ci->load->vars($template);
		
		// Test to see if this view file is available
    	$this->_body = $this->_load_view($view, $template);

		// Want this file wrapped with a layout file?
        if ($this->_layout)
        {
			$template['body'] = $this->_body;
			
			// For this if/else we do not use the _load_view method 
			// because of $this->_layout and parser.
			
			// If using a theme, use the layout in the theme
			if ($this->_theme && file_exists(ROOTPATH . 'themes/' . $this->_theme .'/'. $this->_layout . EXT))
			{
				$layout_view = ROOTPATH . 'themes/'. $this->_theme .'/'. $this->_layout;
			}
			// Now try the default theme folder
			elseif ($this->_theme && file_exists(ROOTPATH . 'themes/default/'. $this->_layout . EXT))
			{
				$layout_view = ROOTPATH . 'themes/default/'. $this->_layout;
			}
			// At this point it must part of a module
            else 
			{
				$layout_view = $this->_layout;
			}
			
			// Parse if parser is enabled, or its a theme view
			if ($this->_parser_enabled === TRUE OR $this->_theme)
			{
				$this->_body = $this->_ci->parser->parse($layout_view, $template, TRUE);
			}
			else 
			{
				$this->_body = $this->_ci->load->view($layout_view, $template, TRUE);
			}
		}
		$this->_ci->benchmark->mark('template_build_end');
		
		// Want it returned or output to browser?
        if ($return)
        {
            return $this->_body;
        }
        else 
        {
			$this->_ci->events->trigger('template/build');
            // Send it to output
            $this->_ci->output->set_output($this->_body);
        }
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Load a view file
	*/
	private function _load_view($view = '', $data = array(), $search = TRUE)
	{
		if ($search == TRUE)
		{
			$theme_view = trim_slashes($this->_module . '/' . $view);

			// First check the theme folder
			if ($this->_theme && file_exists(ROOTPATH . 'themes/' . $this->_theme .'/'. $theme_view . EXT))
			{
				$this->_ci->load->library('parser');
				return $this->_ci->parser->parse(ROOTPATH . 'themes/' . $this->_theme .'/'. $theme_view, $data, TRUE);
	    	}
			// This checks the default theme.
			elseif (file_exists(ROOTPATH . 'themes/default/'. $theme_view . EXT))
			{
				$this->_ci->load->library('parser');
				return $this->_ci->parser->parse(ROOTPATH . 'themes/default/'. $theme_view, $data, TRUE);
			}
			// Now check outside module
			elseif (file_exists(ROOTPATH .'themes/'. $this->_theme .'/'. $view . EXT)) 
			{
				$this->_ci->load->library('parser');
				return $this->_ci->parser->parse(ROOTPATH . 'themes/' . $this->_theme .'/'. $view, $data, TRUE);
			}
			// Now check outside module
			elseif (file_exists(ROOTPATH .'themes/default/'. $view . EXT)) 
			{
				$this->_ci->load->library('parser');
				return $this->_ci->parser->parse(ROOTPATH . 'themes/default/'. $view, $data, TRUE);
			}
			// Now we use the view in the module
			else 
	    	{
				if ($this->_parser_enabled === TRUE)
				{
					$this->_ci->load->library('parser');
					return $this->_ci->parser->parse($this->_module.'/'.$view, $data, TRUE);
				}
				else 
				{
					return $this->_ci->load->view($this->_module.'/'.$view, $data, TRUE);
				}
			}
		}
		else 
		{
			if ($this->_parser_enabled === TRUE)
			{
				$this->_ci->load->library('parser');
				return $this->_ci->parser->parse($view, $this->data, TRUE);
			}
			else 
			{
				return $this->_ci->load->view($view, $this->data, TRUE);
			}
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
     * Put extra javascipt, css, meta tags, etc after other head data
     *
     * @access    public
     * @param     string	$line	The line being added to head
     * @return    void
     */
	public function append_metadata($line)
	{
		$this->_metadata[] = $line;
		return $this;
	}
	
	// ------------------------------------------------------------------------
	
	/**
     * Set metadata for output later
     *
     * @access    public
     * @param	  string	$name		keywords, description, etc
     * @param	  string	$content	The content of meta data
     * @param	  string	$type		Meta-data comes in a few types, links for example
     * @return    void	
     */
	public function set_metadata($name, $content, $type = 'meta')
	{
		$name = htmlspecialchars(strip_tags($name));
		$content = htmlspecialchars(strip_tags($content));
    	$version = $this->_ci->settings->get_setting('script_version');
		if (strpos($version, '##') !== FALSE)
		{
			$version = '';
		}
		// Keywords with no comments? ARG! comment them
		if ($name == 'keywords' && ! strpos($content, ','))
        {
			$this->_ci->load->helper('inflector');
			$content = keywords($content);
		}
        
		switch ($type) 
		{
			case 'meta':
				$meta = '<meta name="'.$name.'" content="'.$content.'" />';
			break;
			case 'link':
				$meta = '<link rel="'.$name.'" href="'.$content.'" type="text/css" />';
			break;
			case 'js':
				$meta = '<script type="text/javascript" src="'.site_url('get_js/'.$content.$version).'"></script>';
			break;
			case 'js_include':
				$meta = '<script type="text/javascript" src="'.base_url().$content.'?'.$version.'"></script>';
			break;
			default:
				$meta = '<meta name="'.$name.'" content="'.$content.'" />';
		} 
        
		$this->append_metadata($meta);
    	
		return $this;
	}
	
	// ------------------------------------------------------------------------
	
	/**
     * Set the title of the page from controller
     *
     * @access    public
     * @param    string
     * @return    void
     */
    public function title($site_title = '')
    {
		if ($this->_in_admin)
		{
			if ($site_title == '')
			{
				$site_title = '68kb';
			}
			else 
			{
				$site_title = $site_title . $this->_title_separator .'68kb';
			}
		}
		elseif ($site_title == '')
		{
			$site_title = $this->_ci->settings->get_setting('site_name');
		}
		else 
		{
			$site_title = $site_title . $this->_title_separator . $this->_ci->settings->get_setting('site_name');
		}
		
		$this->_title = $site_title;
		return $site_title;
    }
	
	// ------------------------------------------------------------------------
	
	/**
	* Gets the title 
	* 
	* This is used so you can set the title in the tile() method and still revert
	* to default if nothing is entered. 
	* 
	*/
	private function _get_title()
	{
		if ($this->_title)
		{
			return $this->_title;
		}
		else 
		{
			return $this->title();
		}
	}

	// ------------------------------------------------------------------------
	
	/**
     * Set the meta keywords
     *
     * @access    public
     * @param    string
     * @return    void
     */
	public function meta_keywords($site_keywords = '')
	{
		if ($site_keywords == '')
		{
			$site_keywords = $this->_ci->settings->get_setting('site_keywords');
		}
		$this->_keywords = $site_keywords;
		return $site_keywords;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Gets the keywords 
	* 
	* See _get_title for usage
	* 
	*/
	private function _get_keywords()
	{
		if ($this->_keywords)
		{
			return $this->_keywords;
		}
		else 
		{
			return $this->meta_keywords();
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
     * Set the meta keywords
     *
     * @access    public
     * @param    string
     * @return    void
     */
	public function meta_description($site_description = '')
	{
		if ($site_description == '')
		{
			$site_description = $this->_ci->settings->get_setting('site_description');
		}
		$this->_description = $site_description;
		return $site_description;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Gets the description 
	* 
	* See _get_title for usage
	* 
	*/
	private function _get_description()
	{
		if ($this->_description)
		{
			return $this->_description;
		}
		else 
		{
			return $this->meta_description();
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
     * Which Template file are we using here?
     *
     * @access    public
     * @param     string	$view
     * @return    void
     */
	public function set_layout($view = 'layout')
	{
		$this->_layout = $view;
	}

	// ------------------------------------------------------------------------

	/**
	* Which theme are we using here?
	*
	* @access    public
	* @param     string	$theme	Set a theme for the template library to use	
	* @return    void
	*/
	public function set_theme($theme = 'default')
	{
		$this->_theme = $theme;
	}

	// ------------------------------------------------------------------------
	
	/**
	* Should be parser be used or the view files just loaded normally?
	*
	* @access    public
	* @param     bool
	* @return    void
	*/
	public function enable_parser($bool = TRUE)
	{
		$this->_parser_enabled = $bool;
		return $this;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Build bread-crumbs
	*
	* @access    public
	* @param     string	$name	What will appear as the link text
	* @param     string	$uri	The URL segment
	* @return    void
	*/
	public function set_breadcrumb($name, $uri = '')
	{
		$this->_breadcrumbs[] = array('name' => $name, 'uri' => $uri );
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Set the template to admin
	*
	* @deprecated Will be set in admin_controller
	* @param	bool
	* @return 	bool
	*/
	public function in_admin($bool = FALSE)
	{
		$this->_in_admin = $bool;
	}
}

/* End of file Template.php */
/* Location: ./upload/includes/68kb/libraries/Template.php */ 