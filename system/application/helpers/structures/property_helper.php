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
	
	public function is_valid()
	{
		return ($this->location->is_valid() && $this->meta_valid());
		//TODO add support for assets.
	}
	
	private function meta_valid()
	{
		//Keys need to be in a format acceptable as a variable name
		//Values can be anything you want, they will be sanitized prior to
		//Being inserted in the database.
		foreach($this->info AS $key => $value)
		{
			if(!preg_match('/^[a-zA-Z_]$/', $key))
			{
				return FALSE;
			}
		}
		
		return TRUE;
	}
	
	public function __toString()
	{
		$string 	= '#' . $this->id . ';';
		$string		.= ' ' . (string)$this->location . ';';
		
		//Stringify the meta data
		foreach($this->info AS $key => $value)
		{
			$string .= ' ' . str_replace('_', ' ', $key) . ': ' . $value . ';';
		}
		
		//todo: stringifiy assets
		
		return $string;
	}
}