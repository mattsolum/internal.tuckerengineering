<?php defined('BASEPATH') OR exit('No direct script access allowed');

class StructProperty
{
	public $id;
	
	public $location;
	public $info;
	public $assets;
	public $notes;
	
	public $date_added;
	public $date_updated;
	
	public function __construct()
	{
		$this->location = new StructLocation();
		$this->info = new stdClass;
		$this->assets = new stdClass;
		$this->notes = new stdClass;
	}
	
	public function is_valid()
	{
		$location = $this->location->is_valid();
		$meta = $this->meta_valid();
		
		return ($location && $meta);
	}
	
	private function meta_valid()
	{
		//Keys need to be in a format acceptable as a variable name
		//Values can be anything you want, they will be sanitized prior to
		//Being inserted in the database.
		if(count($this->info) > 0)
		{
			foreach($this->info AS $key => $value)
			{
				if(!preg_match('/^[a-zA-Z_]+$/', $key))
				{
					return FALSE;
				}
			}
		}
		
		return TRUE;
	}
	
	public function __toString()
	{
		$string 	= (isset($this->id))?'#' . $this->id . '; ':'';
		$string		.= (string)$this->location . ';';
		
		//Stringify the meta data
		foreach($this->info AS $key => $value)
		{
			$string .= ' ' . str_replace('_', ' ', $key) . ': ' . $value . ';';
		}
		
		//todo: stringify assets
		//todo: stringify notes
		
		return $string;
	}
}