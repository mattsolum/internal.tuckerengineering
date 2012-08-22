<?php defined('BASEPATH') OR exit('No direct script access allowed');

class StructProperty
{
	public $id;
	
	public $number 			= ''; 	//Street number
	public $route 			= '';	//Street name
	public $subpremise 		= '';	//Unit #, Suite #, etc.
	public $locality 		= '';	//City
	public $admin_level_1 	= '';	//State
	public $admin_level_2 	= '';	//County
	public $postal_code 	= '';	//Zip
	
	public $neighborhood 	= '';
	
	public $latitude 		= '';
	public $longitude 		= '';
	
	public $info;
	public $assets;
	public $notes;
	
	public $date_added;
	public $date_updated;
	
	public function __construct()
	{
		$this->info = new stdClass;
		$this->assets = new stdClass;
		$this->notes = new stdClass;
	}
	
	public function is_valid()
	{
		$location = $this->location_valid();
		$meta = $this->meta_valid();
		
		return ($location && $meta);
	}
	
	private function location_valid()
	{
		//Check that all required fields are at least set
		if(
			$this->number 			== '' 	||
			$this->route 			== ''	||
			$this->locality			== ''	||
			$this->admin_level_1	== ''	||
			$this->postal_code		== ''
		)
		{
			return FALSE;
		}
		
		$CI =& get_instance();
		
		$CI->load->model('Map');
		
		//Since everything is set check it with Google.
		return $CI->Map->validate_address($this);
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
		$string = '';
		
		//$string 	.= (isset($this->id))?'#' . $this->id . '; ':'';
		$string		.= $this->location_string() . ';';
		
		//Stringify the meta data
		foreach($this->info AS $key => $value)
		{
			$string .= ' ' . str_replace('_', ' ', $key) . ' - ' . $value . ',';
		}
		
		if(count($this->info) > 0)
		{
			$string = substr($string, 0, strlen($string) - 1) . ';';
		}
		
		//todo: stringify assets
		//todo: stringify notes
		
		return $string;
	}
	
	public function location_string()
	{
		$delineator = '';
			
		if($this->subpremise != '')
		{
			$delineator = (is_numeric($this->subpremise))?'#':'Unit ';
		}
	
		$formatted =  ($this->number != '')?$this->number:'';
		
		$formatted .=  ($this->route != '')?' ' . $this->route:'';
		
		$formatted .= ($this->subpremise != '')?' ' . $delineator . $this->subpremise:'';
		
		$formatted .= ($this->locality != '')?', ' . $this->locality:'';
		$formatted .= ($this->admin_level_1 != '')?', ' . $this->admin_level_1:'';
		$formatted .= ($this->postal_code != '')?' ' . $this->postal_code:'';
		
		return $formatted;
	}
	
	public function set_from_json($json)
	{
		if(is_string($json))
		{
			$json = json_decode($json);
		}
		
		$this->id 				= (isset($json->id))			?$json->id				:0;
		
		$this->number			= (isset($json->number))		?$json->number			:0;
		$this->route			= (isset($json->route))			?$json->route			:'';
		$this->subpremise 		= (isset($json->subpremise))	?$json->subpremise		:'';
		$this->locality 		= (isset($json->locality))		?$json->locality		:'';
		$this->admin_level_1	= (isset($json->admin_level_1))	?$json->admin_level_1	:'';
		$this->admin_level_2	= (isset($json->admin_level_2))	?$json->admin_level_2	:'';
		$this->postal_code 		= (isset($json->postal_code))	?$json->postal_code		:'';
		$this->neighborhood		= (isset($json->neighborhood))	?$json->neighborhood	:'';
		$this->latitude 		= (isset($json->latitude))		?$json->latitude		:'';
		$this->longitude 		= (isset($json->longitude))		?$json->longitude		:'';
		
		$this->date_added		= (isset($json->date_added))	?$json->date_added		:NULL;
		$this->date_updated		= (isset($json->date_updated))	?$json->date_updated	:NULL;
		
		if(isset($json->info))
		{
			foreach($json->info AS $key => $value)
			{
				$this->info->$key = $value; 
			}
		}
		
		//TODO: Assets and notes
		/*
		$this->assets			=
		$this->notes			=
		*/
	}
	
	public function set_location($property)
	{
		$this->number			= $property->number;
		$this->route			= $property->route;
		$this->subpremise 		= $property->subpremise;
		$this->locality 		= $property->locality;
		$this->admin_level_1	= $property->admin_level_1;
		$this->admin_level_2	= $property->admin_level_2;
		$this->postal_code 		= $property->postal_code;
		$this->neighborhood		= $property->neighborhood;
		$this->latitude 		= $property->latitude;
		$this->longitude 		= $property->longitude;
	}
}