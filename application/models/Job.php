<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Job extends CI_Model {
	
	private $CI = NULL;
	
	public function Job()
	{
		parent::__construct();
		$this->CI =& get_instance();	
	}
	
	
	//Return the ID on success
	//and FALSE on failure
	public function insert($job)
	{
		$id = preg_replace('/[^0-9]/', '', $id);
		
		$this->CI->db->trans_start();
		
		$id = $this->exists($job);
		if($id !== FALSE)
		{
			$data['job_id'] = $id;
			$this->delete();
		}
		else
		{
				
		}
		
		$query = $this->CI->db->insert('jobs', $data);
		
		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in Job method insert: transaction failed.');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	public function delete($id)
	{
		$id = preg_replace('/[^0-9]/', '', $id);
		
		$this->CI->db->trans_start();
		
		$this->CI->db->delete('jobs', array('job_id' => $id));
		
		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in Job method delete: transaction failed.');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	public function get($id)
	{
		$id = preg_replace('/[^0-9]/', '', $id);
		
		$query = $this->CI->db->get_where('jobs', array('job_id' => $id));
		
		$job = new StructJob();
		
		if($query->num_rows() > 0)
		{
			$result = $query->row();
			
			$job->id			= $result->job_id;
			$job->service		= $result->service;
			$job->amount		= $result->price;
			$job->travel_fee	= $result->travel_fee;
			$job->adjustment	= $result->adjustment;
			$job->date_added	= $result->date_added;
			$job->date_updated	= $result->date_updated;
			$job->date_billed	= $result->date_billed;
			
			$job->client		= $this->CI->Client->get($result->client_id);
			$job->requester		= $this->CI->Client->get($result->requester_id);
			$job->location		= $this->CI->Property->get($result->property_id);
			
			return $job;
		}
		else
		{
			log_message('Error', 'Error in Job method get: no data found with given ID.');
			return FALSE;
		}
	}
	
	//Return ID on success and FALSE on failure
	public function exists($job)
	{
		$where = array();
		
		$where[''] = 0;
		
		$query = $this->CI->db->get_where('jobs', $where);
		
		if($query->num_rows() > 0)
		{
			$result = $query->row();
			
			return TRUE;
		}
		else return FALSE;
	}
}