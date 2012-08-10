<?php defined('BASEPATH') OR exit('No direct script access allowed');

//If only the name is provided it gets the setting
//If a value is provided as well it sets the setting.
function setting($name, $value = null) {
	$CI =& get_instance();
	$CI->load->model('Setting');
	
	if($value == null)
	{
		return $CI->Setting->get($name);	
	}
	else
	{
		return $CI->Setting->set($name, $value);	
	}
}