<?php defined('BASEPATH') OR exit('No direct script access allowed');

class StructAsset
{
	public $id 				= NULL;
	public $type_id 		= NULL;
	public $type 			= 'job'; //Eventually I want to be able to connect these to jobs AND locations.
	public $filename		= NULL;
	
	public $date_added 		= NULL;
	public $date_updated 	= NULL;

	public function __construct($json = NULL)
	{
		if($json != NULL)
		{
			$this->set_from_json($json);
		}
	}

	public function set_from_json($json)
	{
		if(is_string($json))
		{
			json_decode($json);
		}

		$this->id 			= $json->id;
		$this->filename 	= $json->filename;
		$this->date_added 	= $json->date_added;
		$this->date_updated = $json->date_updated;

	}
	
	public function data()
	{
		//todo: return the file's data.
	}
	
	public function mime_type()
	{
		//todo: return the file's mime-type
	}
	
	public function __toString()
	{
		//todo: return keywords.
		return $this->filename;
	}

	public function is_valid()
	{
		if($this->filename != NULL)
		{
			return TRUE;
		}

		log_message('error', 'Asset ' . $this . ' is invalid.');
		return FALSE;
	}
}