<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package        CodeIgniter
 * @author        Rick Ellis
 * @copyright    Copyright (c) 2006, EllisLab, Inc.
 * @license        http://www.codeignitor.com/user_guide/license.html
 * @link        http://www.codeigniter.com
 * @since        Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Template Class
 *
 * Build your CodeIgniter pages much easier with partials, breadcrumbs, layouts and themes
 *
 * @package        	CodeIgniter
 * @subpackage    	Libraries
 * @category    	Libraries
 * @author        	Philip Sturgeon
 * @link
 */
class Template
{
    private $_module = '';
    private $_controller = '';
    private $_method = '';

    private $_theme = '';
    private $_layout = FALSE; // By default, dont wrap the view with anything

    private $_title = '';
    private $_metadata = array();

	private $_partials = array();

    private $_breadcrumbs = array();

    private $_title_separator = ' | ';

    private $_parser_enabled = FALSE;
    private $_parser_body_enabled = FALSE;

	private $_theme_locations = array();

    // Seconds that cache will be alive for
    private $cache_lifetime = 0;//7200;

    private $_ci;

    private $_data = array();

	/**
	 * Constructor - Sets Preferences
	 *
	 * The constructor can be passed an array of config values
	 */
	function __construct($config = array())
	{
        $this->_ci =& get_instance();

		if (empty($config))
		{
			$this->initialize($config);
		}

        log_message('debug', 'Template class Initialized');

    	// Work out the controller and method
    	if( method_exists( $this->_ci->router, 'fetch_module' ) )
    	{
    		$this->_module 	= $this->_ci->router->fetch_module();
    	}

        $this->_controller	= $this->_ci->router->fetch_class();
        $this->_method 		= $this->_ci->router->fetch_method();
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize preferences
	 *
	 * @access	public
	 * @param	array
	 * @return	void
	 */
	function initialize($config = array())
	{
		foreach ($config as $key => $val)
		{
			$this->{'_'.$key} = $val;
		}

		if (empty($this->_theme_locations))
		{
			$this->_theme_locations = array(ROOTPATH . 'themes/' => '../themes/');
		}

		if ($this->_parser_enabled === TRUE)
		{
			$this->_ci->load->library('parser');
		}
	}

    // --------------------------------------------------------------------

    /**
     * Magic Get function to get data
     *
     * @access    public
     * @param	  string
     * @return    mixed
     */
	public function __get($name)
	{
		return isset($this->_data[$name]) ? $this->_data[$name] : NULL;
	}

    // --------------------------------------------------------------------

    /**
     * Magic Set function to set data
     *
     * @access    public
     * @param	  string
     * @return    mixed
     */
	public function __set($name, $value)
	{
		$this->_data[$name] = $value;
	}

    // --------------------------------------------------------------------

    /**
     * Set the mode of the creation
     *
     * @access    public
     * @param    string
     * @return    void
     */
    public function build($view = '', $data = array(), $return = FALSE)
    {
		// Set whatever values are given. These will be available to all view files
    	is_array($data) OR $data = (array) $data;

		if (empty($this->_title))
        {
        	$this->_title = $this->_guess_title();
        }

        // Output template variables to the template
        $template['title']	= $this->_title;
        $template['breadcrumbs'] = $this->_breadcrumbs;
        $template['metadata']	= implode("\n\t\t", $this->_metadata);
    	$template['partials']	= array();
		$template['site_title'] = $this->_get_title();
		$template['site_keywords'] = $this->_get_keywords();
		$template['site_description'] = $this->_get_description();
		$template['site_theme'] = 'themes/'.$this->_theme;
		$template['breadcrumbs'] = $this->_breadcrumbs;
		$template['head_data'] = implode("\n\t", $this->_metadata);
		$template['settings'] = $this->_ci->settings->get_settings();

		// lets set the template path for images and such
		if ($this->_in_admin)
		{
			$template['template'] = base_url() . 'themes/cp/';
			//set the default admin nav
			if ( ! isset($data['nav'])) 
			{
				$data['nav'] = 'settings';
			}
			$template['modules'] = $this->_modules;
		}
		else 
		{
			$template['template'] = base_url() . 'themes/'. $this->_theme;
			$template['module'] = $this->_ci->router->fetch_module();
		}


		$template = array_merge($data, $template);
		
		// Merge in what we already have with the specific data
		// $this->_data = array_merge($this->_data, $template);
		
		 // Add the full array for all views
		// http://codeigniter.com/forums/viewthread/44916/
		$this->_ci->load->vars($template);
		
    	// Assign by reference, as all loaded views will need access to partials
        //$this->_data['template'] =& $template;

        // Let CI do the caching instead of the browser
        $this->_ci->output->cache( $this->cache_lifetime );

        // Test to see if this file
    	$this->_body = $this->_load_view( $view, TRUE, $this->_parser_body_enabled );

        // Want this file wrapped with a layout file?
        if ($this->_layout)
        {
			$template['body'] = $this->_body;

			// If using a theme, use the layout in the theme
			foreach ($this->_theme_locations as $location => $offset)
			{
				$theme_view = $this->_theme . '/' . $this->_layout;
				
				if ($this->_theme && file_exists($location . $theme_view . self::_ext($theme_view)))
				{
					// If directory is set, use it
					$this->_data['theme_view_folder'] = $offset.$this->_theme.'/';
					$layout_view = $this->_data['theme_view_folder'] . '/' . $this->_layout;

					break;
				}
			}

			// No theme layout file was found, lets use whatever they gave us
			isset($layout_view) || $layout_view = $this->_layout;

			// Parse if parser is enabled, or its a theme view
			if ($this->_parser_enabled === TRUE && $this->_theme)
			{
				$this->_body = $this->_ci->parser->parse($layout_view, $template, TRUE, TRUE);
			}

			else
			{
				$this->_body = $this->_ci->load->view($layout_view, $template, TRUE);
			}
        }

        // Want it returned or output to browser?
        if ( ! $return)
        {
			$this->_ci->events->trigger('template/build');
			if ($this->_in_admin)
			{
				$this->_ci->output->set_output($this->_body);
			}
			else
			{
				$this->_ci->load->library('simpletags');
				$result = $this->_ci->simpletags->parse($this->_body, $template, array($this->_ci->events, 'parser_callback'));
				// Send it to output
				$this->_ci->output->set_output($result['content']);
			}
        }

        return $this->_body;
    }


    /**
     * Put extra javascipt, css, meta tags, etc before all other head data
     *
     * @access    public
     * @param     string	$line	The line being added to head
     * @return    void
     */
    public function prepend_metadata($line)
    {
    	array_unshift($this->_metadata, $line);
        return $this;
    }


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
        if($name == 'keywords' && !strpos($content, ','))
        {
        	$content = preg_replace('/[\s]+/', ', ', trim($content));
        }

        switch($type)
        {

        	case 'meta':
        		$this->_metadata[$name] = '<meta name="'.$name.'" content="'.$content.'" />';
        	break;

        	case 'link':
        		$this->_metadata[$content] = '<link rel="'.$name.'" href="'.$content.'" />';
        	break;

			case 'js':
				$this->_metadata[$content] = '<script type="text/javascript" src="'.site_url('get_js/'.$content.$version).'"></script>';
			break;
			
			case 'js_include':
				$this->_metadata[$content] = '<script type="text/javascript" src="'.base_url().$content.'?'.$version.'"></script>';
			break;
        }

        return $this;
    }

	/**
	 * Which theme are we using here?
	 *
	 * @access	public
	 * @param	string	$theme	Set a theme for the template library to use
	 * @return	void
	 */
	public function set_theme($theme = '')
	{
		$this->_theme = $theme;
		return $this;
	}


	/**
	 * Which theme layout should we using here?
	 *
	 * @access	public
	 * @param	string	$view
	 * @return	void
	 */
	public function set_layout($view = '')
	{
		$this->_layout = $view;
		return $this;
	}

	/**
	 * Set a view partial
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	boolean
	 * @return	void
	 */
	public function set_partial($name, $view, $search = TRUE)
	{
		$this->_partials[$name] = array('view' => $view, 'search' => $search);
		return $this;
	}

	/**
	 * Set a view partial
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	boolean
	 * @return	void
	 */
	public function inject_partial($name, $string, $data = array())
	{
		$this->_partials[$name] = array('string' => $string, 'data' => $data);
		return $this;
	}


	/**
	 * Helps build custom breadcrumb trails
	 *
	 * @access	public
	 * @param	string	$name		What will appear as the link text
	 * @param	string	$url_ref	The URL segment
	 * @return	void
	 */
    public function set_breadcrumb($name, $uri = '')
    {
    	$this->_breadcrumbs[] = array('name' => $name, 'uri' => $uri );
        return $this;
    }


    /**
     * enable_parser
     * Should be parser be used or the view files just loaded normally?
     *
     * @access    public
     * @param     string	$view
     * @return    void
     */
    public function enable_parser($bool)
    {
        $this->_parser_enabled = $bool;
        return $this;
    }

    /**
     * enable_parser_body
     * Should be parser be used or the body view files just loaded normally?
     *
     * @access    public
     * @param     string	$view
     * @return    void
     */
    public function enable_parser_body($bool)
    {
        $this->_parser_body_enabled = $bool;
        return $this;
    }

    /**
     * theme_locations
     * List the locations where themes may be stored
     *
     * @access    public
     * @param     string	$view
     * @return    array
     */
    public function theme_locations()
    {
        return $this->_theme_locations;
    }

    /**
     * add_theme_location
     * Set another location for themes to be looked in
     *
     * @access    public
     * @param     string	$view
     * @return    array
     */
    public function add_theme_location($location, $offset)
    {
        $this->_theme_locations[$location] = $offset;
    }

    /**
     * theme_exists
     * Check if a theme exists
     *
     * @access    public
     * @param     string	$view
     * @return    array
     */
	public function theme_exists($theme = NULL)
	{
		$theme || $theme = $this->_theme;

		foreach ($this->_theme_locations as $location => $offset)
		{
			if( is_dir($location.$theme) )
			{
				return TRUE;
			}
		}

		return FALSE;
	}

    /**
     * layout_exists
     * Check if a theme layout exists
     *
     * @access    public
     * @param     string	$view
     * @return    array
     */
	public function theme_layout_exists($layout, $theme = NULL)
	{
		$theme || $theme = $this->_theme;

		foreach ($this->_theme_locations as $location => $offset)
		{
			if( is_dir($location.$theme) )
			{
				return file_exists($location.$theme . '/views/layouts/' . $layout . self::_ext($layout));
			}
		}

		return FALSE;
	}
    /**
     * layout_exists
     * Check if a theme layout exists
     *
     * @access    public
     * @param     string	$view
     * @return    array
     */
	public function get_theme_layouts($theme = NULL)
	{
		$theme || $theme = $this->_theme;

		$layouts = array();

		foreach ($this->_theme_locations as $location => $offset)
		{
			if( is_dir($location.$theme) )
			{
				foreach(glob($location.$theme . '/views/layouts/*.*') as $layout)
				{
					$layouts[] = pathinfo($layout, PATHINFO_BASENAME);
				}
			}
		}

		return $layouts;
	}

    // A module view file can be overriden in a theme
    private function _load_view($view = '', $search = TRUE, $parse_view = TRUE)
    {
    	// Load exactly what we asked for, no f**king around!
    	if ($search !== TRUE)
    	{
    		if ($this->_parser_enabled === TRUE && $parse_view === TRUE)
			{
				return $this->_ci->parser->parse( $view, $this->_data, TRUE );
			}

			else
			{
				return $this->_ci->load->view( $view, $this->_data, TRUE );
			}
    	}

		// Only bother looking in themes if there is a theme
		if ($this->_theme != '')
		{
			foreach ($this->_theme_locations as $location => $offset)
			{
				$theme_view = $this->_theme . '/' . $this->_module . '/' . $view;
				$theme_no_module = $this->_theme . '/' . $view;
				
				if (file_exists( $location . $theme_view . self::_ext($theme_view)))
				{
					if($this->_parser_enabled === TRUE && $parse_view === TRUE)
					{
						return $this->_ci->parser->parse( $offset.$theme_view, $this->_data, TRUE );
					}

					else
					{
						return $this->_ci->load->view( $offset.$theme_view, $this->_data, TRUE );
					}
				}
				elseif (file_exists( $location . $theme_no_module . self::_ext($theme_no_module)))
				{
					
					if($this->_parser_enabled === TRUE && $parse_view === TRUE)
					{
						return $this->_ci->parser->parse( $offset.$theme_no_module, $this->_data, TRUE );
					}

					else
					{
						return $this->_ci->load->view( $offset.$theme_no_module, $this->_data, TRUE );
					}
				}
			}
		}

		// Not found it yet? Just load, its either in the module or root view
		if($this->_parser_enabled === TRUE && $parse_view === TRUE)
		{
			return $this->_ci->parser->parse( $this->_module.'/'.$view, $this->_data, TRUE );
		}

		else
		{
			return $this->_ci->load->view( $this->_module.'/'.$view, $this->_data, TRUE );
		}

    }

    private function _guess_title()
    {
        $this->_ci->load->helper('inflector');

        // Obviously no title, lets get making one
        $title_parts = array();

        // If the method is something other than index, use that
        if ($this->_method != 'index')
        {
        	$title_parts[] = $this->_method;
        }

        // Make sure controller name is not the same as the method name
        if( ! in_array($this->_controller, $title_parts))
        {
        	$title_parts[] = $this->_controller;
        }

        // Is there a module? Make sure it is not named the same as the method or controller
        if ( ! empty($this->_module) && ! in_array($this->_module, $title_parts))
        {
        	$title_parts[] = $this->_module;
        }

        // Glue the title pieces together using the title separator setting
        $title = humanize(implode($this->_title_separator, $title_parts));

        return $title;
    }

	private function _ext($file)
	{
		return pathinfo($file, PATHINFO_EXTENSION) ? '' : EXT;
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
				$site_title = '68KB';
			}
			else 
			{
				$site_title = $site_title . $this->_title_separator .'68KB';
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

}

// END Template class