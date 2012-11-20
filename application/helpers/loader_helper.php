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

/**
* Model Exists
*
* This function is used to check if a "model" file exists.
*
* @author	Matthew Solum
* @param	string
*/
function model_exists($model)
{
	$CI =& get_instance();
	
	$paths = $CI->load->get_package_paths();
	
	if ($model == '')
	{
		return FALSE;
	}

	$path = '';

	// Is the model in a sub-folder? If so, parse out the filename and path.
	if (($last_slash = strrpos($model, '/')) !== FALSE)
	{
		// The path is in front of the last slash
		$path = substr($model, 0, $last_slash + 1);

		// And the model name behind it
		$model = substr($model, $last_slash + 1);
	}
	
	$model = strtolower($model);
	
	foreach ($paths as $mod_path)
	{
		if (file_exists($mod_path.'models/'.$path.$model.'.php'))
		{
			return TRUE;
		}
	}
	
	return FALSE;
}