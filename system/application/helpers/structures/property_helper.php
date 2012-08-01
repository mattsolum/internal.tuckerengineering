<?php defined('BASEPATH') OR exit('No direct script access allowed');

class StructProperty
{
	public $id 				= 0;
	
	public $location 		= NULL;
	public $info			= NULL;
	public $assets 			= NULL;
	
	public $date_added 		= NULL;
	public $date_updated 	= NULL;
	
	public function __construct()
	{
		$this->location = new StructLocation();
		$this->info = new stdClass;
		$this->assets = new stdClass;
	}
}