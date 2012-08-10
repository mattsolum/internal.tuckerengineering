<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Notification extends CI_Model {
	
	private $CI = NULL;
	
	public function Notification()
	{
		parent::__construct();
		$this->CI =& get_instance();	
	}	
	
	public function add()
	{
		
	}
	
	public function clear()
	{
		
	}
}