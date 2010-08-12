<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// ------------------------------------------------------------------------

/**
 * Form Declaration
 *
 * Creates the opening portion of the form.
 *
 * @access	public
 * @param	string	the URI segments of the form destination
 * @param	array	a key/value pair of attributes
 * @param	array	a key/value pair hidden data
 * @return	string
 */	
if ( ! function_exists('form_open'))
{
	function form_open($action = '', $attributes = '', $hidden = array())
	{
		$CI =& get_instance();

		if ($attributes == '')
		{
			$attributes = 'method="post"';
		}
		
		if (substr($action, 0, 5) == 'admin')
		{
			$action = str_replace('admin', $CI->config->item('admin', 'triggers'), $action);
		}

		$action = ( strpos($action, '://') === FALSE) ? $CI->config->site_url($action) : $action;

		$form = '<form action="'.$action.'"';
		
		$form .= _attributes_to_string($attributes, TRUE);

		$form .= '>';

		if (is_array($hidden) AND count($hidden) > 0)
		{
			$form .= form_hidden($hidden);
		}
		
		// CSRF
		if ($CI->config->item('csrf_protection') === TRUE)
		{
			$form .= form_hidden($CI->security->csrf_token_name, $CI->security->csrf_hash);
		}
		
		return $form;
	}
}

// ------------------------------------------------------------------------

/**
* Form Prep
*
* Formats text so that it can be safely placed in a form field in the event it has HTML tags.
*
* @access public
* @param string
* @return string
*/
if ( ! function_exists('form_prep'))
{
	function form_prep($str = '', $field_name = '')
	{
		static $prepped_fields = array();

		// if the field name is an array we do this recursively
		if (is_array($str))
		{
			foreach ($str as $key => $val)
			{
				$str[$key] = form_prep($val);
			}

			return $str;
		}

		if ($str === '')
		{
			return '';
		}

		// we've already prepped a field with this name
		// @todo need to figure out a way to namespace this so
		// that we know the *exact* field and not just one with
		// the same name
		if (isset($prepped_fields[$field_name]))
		{
			return $str;
		}
		
		// $str = htmlspecialchars($str);

		$str = htmlspecialchars($str, ENT_COMPAT, 'UTF-8');

		// In case htmlspecialchars misses these.
		$str = str_replace(array("'", '"'), array("&#39;", "&quot;"), $str);

		if ($field_name != '')
		{
			$prepped_fields[$field_name] = $str;
		}

		return $str;
	}
}

/* End of file MY_form_helper.php */
/* Location: ./upload/system/68kb/helpers/MY_form_helper.php */ 