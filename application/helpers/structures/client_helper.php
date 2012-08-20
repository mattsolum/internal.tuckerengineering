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
	public $note = '';
	
	function is_valid()
	{
		//TODO
		return TRUE;
	}
	
	function __toString()
	{
		$string = '';
		
		$string .=	($this->name != '')	?$this->name . ' :: ':'';
		
		foreach($this->contact AS $contact_item)
		{
			$string .= $contact_item['type'] . ' - ' . $contact_item['info'] . ', ';
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
}