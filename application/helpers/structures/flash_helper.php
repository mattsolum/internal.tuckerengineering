<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Flash {

	private $message 	= null;
	private $type		= null;

	public function __construct($msg, $type)
	{
		$this->message 	= $msg;
		$this->type 	= $type;
	}

	public function set_message($msg)
	{
		return $this->message = $msg;
	}

	public function get_message()
	{
		return $this->message;
	}

	public function set_type($type)
	{
		return $this->type = $type;
	}

	public function get_type()
	{
		return $this->type;
	}
}