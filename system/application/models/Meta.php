<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Meta extends Model {
	
	private $CI = NULL;
	
	public function Meta()
	{
		parent::__construct();
		$this->CI =& get_instance();	
	}
	
	
	//Return the ID on success
	//and FALSE on failure
	public function insert($id, $type, $name, $value)
	{
		$data = array();
		
		$this->CI->db->trans_start();
		
		$id = $this->exists($id, $type, $name);
		if($id !== FALSE)
		{
			$this->delete();
		}
		
		$data['id'] 	= preg_replace('/[^0-9]/', '', $id);
		$data['type'] 	= preg_replace('/[^a-zA-Z]/', '', strtolower($type));
		$data['name']	= strtolower($name);
		$data['value']	= $value; //Codeigniter's active record class sanitizes inputs, so we are trusting that for safety here.
		
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
	
	public function delete($id, $type, $name)
	{
		$where = array();
		
		$where['id'] 	= preg_replace('/[^0-9]/', '', $id);
		$where['type'] 	= preg_replace('/[^a-zA-Z]/', '', strtolower($type));
		$where['name']	= strtolower($name);
				
		$this->CI->db->trans_start();
		
		if($this->exists($id, $type, $name)) $this->CI->db->delete('meta', $where);
		
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
		$where['name']	= strtolower($name); //Once again relying on the active record class for safety 
		
		$query = $this->CI->db->get_where('meta', $where);
		
		if($query->num_rows() > 0)
		{	
			return TRUE;
		}
		else return FALSE;
	}
}