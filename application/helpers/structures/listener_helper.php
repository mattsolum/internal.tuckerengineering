<?php defined('BASEPATH') OR exit('No direct script access allowed');

class StructListener
{
	public $package = NULL;
	public $extension = NULL;
	public $function = NULL;
	public $custom_var = NULL;
	
	public function __construct($package, $extension, $function, $custom_var = NULL)
	{
		$this->package = $package;
		$this->function = $function;
		$this->extension = $extension;
		$this->custom_var = $custom_var;
	}
}	