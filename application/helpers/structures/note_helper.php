<?php defined('BASEPATH') OR exit('No direct script access allowed');

class StructNote
{
	public $id			= NULL;
	public $type_id 	= NULL;
	public $type		= NULL;
	
	public $user		= NULL;
	
	public $text		= NULL;
	
	public $date_added	= NULL;
	
	public function __construct($json = NULL)
	{
		$this->user = new StructUser();
		
		if($json != NULL)
		{
			$this->set_from_json($json);
		}
	}
	
	public function set_from_json($json)
	{
		if(is_string($json))
		{
			$json = json_decode($json);
		}
		
		$this->id			= $json->id;
		$this->type_id		= $json->type_id;
		$this->type			= $json->type;
		
		$this->text			= $json->text;
		
		$this->date_added 	= $json->date_added;
		
		$this->user			= new StructUser($json->user);
	}
	
	public function is_valid()
	{
		if($this->type_id == NULL || $this->type == NULL || $this->user->id == NULL || $this->text == NULL)
		{
			return FALSE;	
		}
		
		return TRUE;
	}
	
	public function __toString()
	{
		return $this->user->name . ' - ' . str_replace("\n", ' ', $this->text);
	}
}