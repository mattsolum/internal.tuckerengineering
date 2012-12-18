<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Note extends Model {
	
	private $CI = NULL;
	
	public function Note()
	{
		parent::__construct();
		$this->CI =& get_instance();	
	}
	
	
	public function get_job($id)
	{
		return $this->get($id, 'job');
	}
	
	public function get_property($id)
	{
		return $this->get($id, 'property');
	}
	
	public function get_client($id)
	{
		return $this->get($id, 'client');
	}
	
	public function get($id, $type)
	{
		
	}
	
	public function commit($note)
	{
	
	}
	
	private function create($note)
	{
	
	}
	
	private function update($note)
	{
		
	}
	
	public function delete($note)
	{
	
	}
}