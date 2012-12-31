<?php defined('BASEPATH') OR exit('No direct script access allowed');

class StructProperty
{
	public $id 				= NULL;
	
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
	
	public function __construct($json = NULL)
	{
		$this->info = array();
		$this->assets = new stdClass;
		$this->notes = array();
		
		if($json != NULL)
		{
			$this->set_from_json($json);
		}
	}
	
	public function add_note($user_id, $text)
	{
		$note = new StructNote();

		$note->user->id = $user_id;
		$note->type 	= 'property';
		$note->text 	= $text;
		$note->type_id 	= $this->id;

		$this->notes[] = $note;
	}

	public function is_valid()
	{
		$location = $this->location_valid();
		$meta = $this->meta_valid();
		
		return ($location && $meta);
	}
	
	public function set_id($id)
	{
		if(preg_match('/^[0-9]+$/', $id))
		{	
			for($i = 0; $i < count($this->notes); $i++)
			{
				$this->notes[$i]->type_id 	= $id;
				$this->notes[$i]->type 		= 'property';
			}
			
			$this->id = $id;
			
			return TRUE;
		}
		
		return FALSE;
		
	}
	
	private function location_valid()
	{
		//Check that all required fields are at least set
		if(!$this->is_pobox() && ($this->number == '' || $this->route == ''))
		{
			return FALSE;
		}

		if(
			$this->locality			== ''	||
			$this->admin_level_1	== ''	||
			$this->postal_code		== ''
		)
		{
			return FALSE;
		}
		
		return TRUE;
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
		$str = '';
		
		//$str 	.= (isset($this->id))?'#' . $this->id . '; ':'';
		$str		.= $this->location_string() . "\n";
		
		//Stringify the meta data
		foreach($this->info AS $key => $value)
		{
			$str .= str_replace('_', ' ', $key) . ' - ' . $value . ', ';
		}

		if(count($this->info) > 0)
		{
			$str = substr($str, 0, strlen($str) - 2);
		}
		
		if(count($this->notes) > 0)
		{
			$str .= "\nNotes:";
			
			foreach($this->notes AS $note)
			{
				$str .= "\n$note";
			}
		}
		
		//todo: stringify assets
		
		return $str;
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

		return trim($formatted);
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
				$this->info[$key] = $value; 
			}
		}
		
		//TODO: Assets
		
		if(isset($json->notes))
		{
			foreach($json->notes AS $note)
			{
				$this->notes[] = new StructNote($note);
			}
		}
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

	public function is_pobox()
	{
		if(preg_match('/(Postal|(P(ost|\.)?( |-)?O(ffice|\.)?))/i', $this->route))
		{
			return TRUE;
		}

		return FALSE;
	}
}