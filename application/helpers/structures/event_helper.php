<?php defined('BASEPATH') OR exit('No direct script access allowed');

class StructEvent
{
	public $event = NULL;
	public $custom = NULL;
	public $data = NULL;
	
	public function __construct($event, $data, $custom = NULL)
	{
		$this->event = $event;
		$this->data =& $data;
		$this->custom = $custom;
	}
}