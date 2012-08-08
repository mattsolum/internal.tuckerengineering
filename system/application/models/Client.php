<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Client extends Model {
	
	private $CI = NULL;
	
	public function Client()
	{
		parent::__construct();
		$this->CI =& get_instance();	
	}
	
	
	//Return the ID on success
	//and FALSE on failure
	public function insert($client)
	{
		$id = preg_replace('/[^0-9]/', '', $id);
		
		$this->CI->db->trans_start();
		
		$id = $this->exists();
		if($id !== FALSE)
		{
			$data['client_id'] = $id;
			$this->delete();
		}
		else
		{
				
		}
		
		$query = $this->CI->db->insert('clients', $data);
		
		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in Client method insert: transaction failed.');
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
		
		$this->CI->db->delete('clients', array('client_id' => $id));
		
		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in Client method delete: transaction failed.');
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
		
		$query = $this->CI->db->get_where('clients', array('client_id' => $id));
		
		if($query->num_rows() > 0)
		{
			$result = $query->row();
			
			
		}
		else
		{
			log_message('Error', 'Error in Client method get: no data found with given ID.');
			return FALSE;
		}
	}
	
	//Return ID on success and FALSE on failure
	public function exists($client)
	{
		$where = array();
		
		$where[''] = 0;
		
		$query = $this->CI->db->get_where('clients', $where);
		
		if($query->num_rows() > 0)
		{
			$result = $query->row();
			
			return TRUE;
		}
		else return FALSE;
	}
}