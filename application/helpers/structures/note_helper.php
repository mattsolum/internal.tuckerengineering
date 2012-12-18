<?php defined('BASEPATH') OR exit('No direct script access allowed');

class StructNote
{
	public $id;
	public $type;
	
	public $user_id;
	
	public $text;
	
	public $date_added;
	
	public function set_from_json($json)
	{
	
	}
	
	public function __toString()
	{
		return $user_id . ': ' . $text;
	}
}