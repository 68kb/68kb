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
 * ckEditor Addon
 *
 *
 * @subpackage	Addons
 * @link		http://68kb.com/user_guide/addons/
 *
 */
class Ckeditor_extension
{
	
	private $_ci;
	
	/**
	 * Setup events
	 *
	 * @access	public
	 */
	public function __construct($modules)
	{
		$this->_ci = CI_Base::get_instance();
		
		$modules->register('content/form', $this, 'integrate');
		$modules->register('htmleditor', $this, 'integrate');
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * This is the where clause for search listings
	 */
	public function integrate()
	{
		$output = '
			<script type="text/javascript" src="includes/addons/ckeditor/ckeditor/ckeditor.js"></script>
			<script type="text/javascript" src="includes/addons/ckeditor/ckeditor/adapters/jquery.js"></script>
			<script type="text/javascript">
				//<![CDATA[
				$(function()
				{
					var config = {
						toolbar:
						[
							[\'Bold\', \'Italic\', \'-\', \'NumberedList\', \'BulletedList\', \'-\', \'Link\', \'Unlink\'],
							[\'UIColor\', \'Image\']
						]
					};

					// Initialize the editor.
					// Callback function can be passed and executed after full instance creation.
					$(\'#page_content\').ckeditor(config);
					$(\'#post_content\').ckeditor(config);
				});

				//]]>
			</script>
		';
		echo $output;
	}
}
/* End of file ckeditor_extension.php */
/* Location: ./upload/includes/addons/ckeditor/ckeditor_extension.php */ 