<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Meta extends CI_Model {
	
	private $CI = NULL;
	
	public function __construct()
	{
		parent::__construct();
		$this->CI =& get_instance();	
	}
	
	//Return the ID on success
	//and FALSE on failure
	//$name and $value have a maximum length of 64 characters
	public function insert($id, $type, $name, $value)
	{
		$data = array();
		
		$this->CI->db->trans_start();
		
		$exists = $this->exists($id, $type, $name);
		if($exists == TRUE)
		{
			$this->delete($id, $type, $name);
		}
		
		$data['id'] 	= preg_replace('/[^0-9]/', '', $id);
		
		$data['type'] 	= preg_replace('/[^a-zA-Z]/', '', strtolower($type));
		$data['name']	= strtolower(substr(preg_replace('/[^a-zA-Z_]/', '', str_replace(' ', '_', $name)), 0, 64));
		$data['value']	= substr($value, 0, 64); //Codeigniter's active record class sanitizes inputs, so we are trusting that for safety here.
		
		$query = $this->CI->db->insert('meta', $data);
		
		
		
		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in Meta method insert: transaction failed.');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	public function delete($id, $type, $name = null)
	{
		$where = array();
		
		$where['id'] 	= preg_replace('/[^0-9]/', '', $id);
		$where['type'] 	= preg_replace('/[^a-zA-Z]/', '', strtolower($type));
		if($name != null)
		{
			$where['name']	= strtolower(substr(preg_replace('/[^a-zA-Z_]/', '', str_replace(' ', '_', $name)), 0, 64));
		}
				
		$this->CI->db->trans_start();
		
		$this->CI->db->delete('meta', $where);
		
		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in Meta method delete: transaction failed.');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	public function get($id, $type, $name = NULL)
	{
		$where = array();
		
		$where['id'] 	= preg_replace('/[^0-9]/', '', $id);
		$where['type'] 	= preg_replace('/[^a-zA-Z]/', '', strtolower($type));
		
		if($name !== NULL)
		{
			$where['name']	= strtolower($name);
		}
		
		$this->CI->db->order_by('name', 'asc');
		
		$query = $this->CI->db->get_where('meta', $where);
		
		if($query->num_rows() > 0)
		{	
			if($name !== NULL)
			{
				return $query->row();
			}
			else return $query->result_array();
		}
		else return FALSE;
	}
	
	public function exists($id,$type,$name)
	{
		$where = array();
		
		$where['id'] 	= preg_replace('/[^0-9]/', '', $id);
		$where['type'] 	= preg_replace('/[^a-zA-Z]/', '', $type);
		$where['name']	= preg_replace('/[^a-zA-Z_]/', '', strtolower($name)); //Once again relying on the active record class for safety 
		
		$query = $this->CI->db->get_where('meta', $where);
		
		if($query->num_rows() > 0)
		{	
			return TRUE;
		}
		else return FALSE;
	}
}