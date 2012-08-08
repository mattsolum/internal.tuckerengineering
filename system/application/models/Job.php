<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Job extends Model {
	
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
		
		$id = $this->exists();
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
		
		if($query->num_rows() > 0)
		{
			$result = $query->row();
			
			
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