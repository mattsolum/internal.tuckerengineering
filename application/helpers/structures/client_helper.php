<?php defined('BASEPATH') OR exit('No direct script access allowed');


class StructClient
{
	//Client Information
	public $id = NULL;
	public $name = '';
	//Address
	public $location = NULL;
	//Contact Information
	public $contact = array();
	//Client that *this* client represents
	public $parent = NULL;
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
		
		$string .=	($this->name != '')	?$this->name . ' ':'';
		
		foreach($this->contact AS $contact_item)
		{
			$string .= $contact_item['type'] . ' - ' . $contact_item['data'];
		}
		
		
		if(isset($location))
		{
			
		}
	}
}