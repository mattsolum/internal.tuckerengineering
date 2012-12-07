<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Accounting extends CI_Model
{
	private $CI = NULL;
	
	public function Accounting()
	{
		parent::__construct();
		$this->CI =& get_instance();
		
		$this->CI->load->model('Ledger');
		$this->CI->load->model('Payment');
	}
	
	public function commit($account)
	{
		//Decides on its own to create or update a given record
	}
	
	public function get_by_job($job_id)
	{
		//Spawns a StructAccounting object based on a job_id
	}
	
	public function get_by_client($client_id)
	{
		
	}
	
	private function read()
	{
		
	}
	
	private function create($accounting)
	{
		
	}
	
	public function edit($accounting)
	{
		
	}
	
	public function delete()
	{
		
	}
	
	private function exists($accounting)
	{
		
	}
}