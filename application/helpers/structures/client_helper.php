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

	private function sort_contacts()
	{
		usort($this->contact, "usort_contacts");
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

	public function add_contact_item($type, $info)
	{
		foreach($this->contact as $item)
		{
			if($item->type == $type && $item->info == $info)
			{
				return FALSE;
			}
		}

		$con = new StructContact();
		$con->set($type, $info);

		$this->contact[] = $con;
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

		foreach($this->contact AS $contact)
		{
			if(!$contact->is_valid(FALSE))
			{
				return FALSE;
			}
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
			
			for($i = 0; $i < count($this->contact); $i++)
			{
				$this->contact[$i]->id = $id;
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
			$this->sort_contacts();

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
					
					$this->contact[$key] = new StructContact();
					$this->contact[$key]->id   = $this->id;
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

function usort_contacts($a, $b)
{
	$ap = preg_replace('/[^a-z0-9]/', '', strtolower($a->type));
	$bp = preg_replace('/[^a-z0-9]/', '', strtolower($b->type));
	
	$ord = array('9' => -10, '8' => -9, '7' => -8, '6' => -7, '5' => -6, '4' => -5, '3' => -4, '2' => -3, '1' => -2, '0' => -1, 'a' => 0, 'b' => 1, 'c' => 2, 'd' => 3, 'e' => 4, 'f' => 5, 'g' => 6, 'h' => 7, 'i' => 8, 'j' => 9, 'k' => 10, 'l' => 11, 'm' => 12, 'n' => 13, 'o' => 14, 'p' => 15, 'q' => 16, 'r' => 17, 's' => 18, 't' => 19, 'u' => 20, 'v' => 21, 'w' => 22, 'x' => 23, 'y' => 24, 'z' => 25);

	$va = 0;
	$vb = 0;
	$i 	= 0;

	$min_length = min(strlen($a->type), strlen($b->type));

	while($i < $min_length && $va == $vb)
	{
		$char_a = substr($ap, $i, 1);
		$char_b = substr($bp, $i, 1);

		if($ord[$char_b] > $ord[$char_a])
		{
			$vb += 1;
		}
		else if($ord[$char_b] < $ord[$char_a])
		{
			$va += 1;
		}

		$i++;
	}

	if($va == $vb)
	{
		return 0;
	}
	else if($va < $vb)
	{
		return -1;
	}
	else
	{
		return 1;
	}
}