<?php defined('BASEPATH') OR exit('No direct script access allowed');


/**
* View Exists
*
* This function is used to check if a "view" file exists.
*
* @author	Matthew Solum
* @param	string
*/
function view_exists($view)
{
	$CI =& get_instance();
	$paths = $CI->load->get_package_paths();
	
	$_ci_ext = pathinfo(FALSE, PATHINFO_EXTENSION);
	$_ci_file = ($_ci_ext == '') ? $view.'.php' : $view;
	
	foreach ($paths as $view_file)
	{
		if (file_exists($view_file.'views/'.$_ci_file))
		{
			return TRUE;
		}
	}
	
	return FALSE;
}