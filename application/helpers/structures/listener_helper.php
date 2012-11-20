<?php defined('BASEPATH') OR exit('No direct script access allowed');

class StructListener
{
	public $package = NULL;
	public $extension = NULL;
	public $callback = NULL;
	
	public function __construct($package, $extension, $callback)
	{
		$this->package = $package;
		$this->callback = $callback;
		$this->extension = $extension;
	}
}	