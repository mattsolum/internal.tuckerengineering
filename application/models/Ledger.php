<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Ledger extends CI_Model
{
	private $CI = NULL;
	
	public function Ledger()
	{
		parent::__construct();
		$this->CI =& get_instance();
	}
	
	public function commit($ledger)
	{
		
	}
	
	public function get($ledger_id)
	{
		
	}
	
	public function get_by_client($client_id)
	{
		
	}
	
	public function get_by_job($job_id)
	{
		
	}
	
	private function create($ledger)
	{
		
	}
	
	private function read($ledger_id)
	{
		
	}
	
	private function update($ledger)
	{
		
	}
	
	public function delete($ledger_id)
	{
		
	}
	
	private function exists($ledger)
	{
	
	}
}