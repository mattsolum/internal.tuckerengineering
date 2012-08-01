<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Event extends Model {
	
	private $CI = NULL;
	
	public function Event()
	{
		parent::__construct();
		$this->CI =& get_instance();	
	}	
	
	public function trigger_event($event, $data = NULL)
	{
		
	}
	
	public function register_listener($callback)
	{
		
	}
	
	private function find_listener()
	{
		
	}
}