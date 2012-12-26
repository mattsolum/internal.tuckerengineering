<?php defined('BASEPATH') OR exit('No direct script access allowed');


class StructClient
{
	//Client Information
	public $id 			= NULL;
	public $name 		= '';
	
	public $title 		= '';
	
	public $balance		= 0;
	
	//Property
	public $location 	= NULL;
	
	//Contact Information
	public $contact		= array();
	
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

	public function add_note($user_id, $text)
	{
		$note = new StructNote();

		$note->user->id = $user_id;
		$note->type 	= 'client';
		$note->text 	= $text;
		$note->type_id 	= $this->id;

		$this->notes[] = $note;
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
	
	public function set_id($id)
	{
		if(preg_match('/^[0-9]+$/', $id))
		{	
			for($i = 0; $i < count($this->notes); $i++)
			{
				$this->notes[$i]->type_id 	= $id;
				$this->notes[$i]->type 		= 'client';
			}
			
			$this->id = $id;
			
			return TRUE;
		}
		
		return FALSE;
	}
	
	public function __toString()
	{
		$str = '';
		
		$str .=	$this->name . " ::\n";
		
		$str .= "Balance: $" . number_format($this->balance, 2) . "\n";
		
		if(count($this->contact) > 0)
		{
			foreach($this->contact AS $contact_item)
			{
				$str .= $contact_item->type . ' - ' . $contact_item->info . ', ';
			}
			
			$str = substr($str, 0, strlen($str) - 2) . "\n";
		}
		
		if($this->location != NULL && get_class($this->location) == 'StructProperty')
		{
			$str .= (string)$this->location;
		}
		
		if(count($this->notes) > 0)
		{
			$str .= "\nNotes:";
			
			foreach($this->notes AS $note)
			{
				$str .= "\n$note";
			}
		}
		
		return $str;
	}
	
	public function set_from_json($json)
	{
		if(is_string($json))
		{
			$json = json_decode($json);
		}
		
		$this->id		= (isset($json->id))		?$json->id		:0;
		$this->name		= (isset($json->name))		?$json->name	:'';
		$this->title	= (isset($json->title))		?$json->title	:'';
		$this->balance	= (isset($json->balance))	?$json->balance	:0;
		
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
		
		if(isset($json->notes))
		{
			foreach($json->notes AS $note)
			{
				$this->notes[] = new StructNote($note);
			}
		}
	}
}