<?php defined('BASEPATH') OR exit('No direct script access allowed');


class StructClient
{
	//Client Information
	public $id 		= NULL;
	public $name 	= '';
	
	public $title 	= '';
	
	public $balance	= 0;
	
	//Property
	public $location = NULL;
	
	//Contact Information
	public $contact = array();
	
	//Notes
	public $notes;
	
	//Dates
	public $date_added;
	public $date_updated;
	
	public function __construct($json = NULL)
	{
		$this->location = new StructProperty();
		$this->notes	= array();
		
		if($json !== NULL)
		{
			$this->set_from_json($json);
		}
	}
	
	public function is_valid()
	{
		//Reasons it may be invalid
		//All we really need is a name and a location
		if(
			$this->name == '' ||
			$this->location == NULL ||
			!$this->location->is_valid()
		)
		{
			return FALSE;
		}
		
		return TRUE;
	}
	
	public function __toString()
	{
		$string = '';
		
		$string .=	($this->name != '')?$this->name . ' :: ':'';
		
		$string .= 'Balance: $' . number_format($this->balance, 2) . ";\n";
		
		if(count($this->contact) > 0)
		{
			foreach($this->contact AS $contact_item)
			{
				$string .= $contact_item->type . ' - ' . $contact_item->info . ', ';
			}
			
			$string = substr($string, 0, strlen($string) - 2) . '; ';
		}
		
		if($this->location != NULL && get_class($this->location) == 'StructProperty')
		{
			$string .= (string)$this->location;
		}
		
		$string .= "\nNotes:";
		
		foreach($this->notes AS $note)
		{
			$string .= "\n$note";
		}
		
		return $string;
	}
	
	public function set_from_json($json)
	{
		if(is_string($json))
		{
			$json = json_decode($json);
		}
		
		$this->id		= (isset($json->id))	?$json->id		:0;
		$this->name		= (isset($json->name))	?$json->name	:'';
		$this->title	= (isset($json->title))	?$json->title	:'';
		
		if(isset($json->contact) && is_array($json->contact))
		{
			foreach($json->contact AS $key => $contact_item)
			{
				if(isset($contact_item->type) && isset($contact_item->info))
				{
					$type = $contact_item->type;
					$info = $contact_item->info;
					
					$this->contact[$key]->type = $type;
					$this->contact[$key]->info = $info;
				}
			}
		}
		
		if(isset($json->location))
		{
			$this->location = new StructProperty();
			$this->location->set_from_json($json->location);
		}
		
		//TODO: Notes!
	}
}