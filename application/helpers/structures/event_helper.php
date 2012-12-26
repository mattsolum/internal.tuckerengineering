<?php defined('BASEPATH') OR exit('No direct script access allowed');

class StructEvent
{
	public $event = NULL;
	public $data = NULL;
	
	public function __construct($event, &$data)
	{
		$this->event = $event;
		$this->data =& $data;
	}
}