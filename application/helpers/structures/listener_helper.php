<?php defined('BASEPATH') OR exit('No direct script access allowed');

class StructListener
{
	public $package = NULL;
	public $extension = NULL;
	public $callback = NULL;
	public $custom_var = NULL;
	
	public function __construct($package, $extension, $callback, $custom_var = NULL)
	{
		$this->package = $package;
		$this->callback = $callback;
		$this->extension = $extension;
		$this->custom_var = $custom_var;
	}
}	