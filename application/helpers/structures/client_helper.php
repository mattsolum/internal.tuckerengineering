<?php defined('BASEPATH') OR exit('No direct script access allowed');


class StructClient
{
	//Client Information
	public $id = NULL;
	public $name = '';
	
	public $title = '';
	//Address
	public $location = NULL;
	//Contact Information
	public $contact = array();
	//Notes
	public $notes;
	
	public $date_added;
	public $date_updated;
	
	public function is_valid()
	{
		//TODO
		return TRUE;
	}
	
	public function __toString()
	{
		$string = '';
		
		$string .=	($this->name != '')	?$this->name . ' :: ':'';
		
		foreach($this->contact AS $contact_item)
		{
			$string .= $contact_item->type . ' - ' . $contact_item->info . ', ';
		}
		
		if(count($this->contact) > 0)
		{
			$string = substr($string, 0, strlen($string) - 2) . '; ';
		}
		
		if($this->location != NULL && get_class($this->location) == 'StructProperty')
		{
			$string .= (string)$this->location;
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