<?php defined('BASEPATH') OR exit('No direct script access allowed');

class StructNote
{
	public $id;
	public $type_id;
	public $type;
	
	public $user;
	
	public $text;
	
	public $date_added;
	
	public function __construct($json = NULL)
	{
		$this->user = new StructUser();
	}
	
	public function set_from_json($json)
	{
		
	}
	
	public function __toString()
	{
		return $this->user->name . ': ' . $this->text;
	}
}