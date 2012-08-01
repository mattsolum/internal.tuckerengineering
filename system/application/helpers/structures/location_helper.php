<?php defined('BASEPATH') OR exit('No direct script access allowed');

class StructLocation
{
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
		
	//Takes the input from the first box on a standard
	//address form and parses it into street number/name.
	//Added this functionality to step around google ignoring
	//unit numbers when the address is presented as '123-A Generic St.'
	function set_street_1()
	{
		//todo
	}
	
	//Takes an address string and passes it to google
	//and sets itself with the result.
	function set_from_string()
	{
		//todo
	}
	
	//Retreives the latitude and longitude for this address
	function geocode()
	{
		//todo
	}
	
	function is_valid()
	{
		//Check that all required fields are at least set
		if(
			$this->number 			== '' 	||
			$this->route 			== ''	||
			$this->locality			== ''	||
			$this->admin_level_1	== ''	||
			$this->postal_code		== ''
		) return FALSE;
		
		$CI =& get_instance();
		
		$CI->load->model('Map');
		
		//Since everything is set check it with Google.
		return $CI->Map->validate_address($this);
	}
	
	function __toString()
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
}