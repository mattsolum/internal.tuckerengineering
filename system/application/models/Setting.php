<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Setting extends Model {
	
	var $CI = NULL;
	
	public function Setting()
	{
		parent::__construct();
		$this->CI =& get_instance();	
	}
	
	public function get($name)
	{
		$name = $this->_format_name($name);
		
		if($this->exists($name))
		{
			$query = $this->CI->db->get_where('config', array('name' => strtolower($name)));
			
			if($query->num_rows() > 0)
			{
				$row = $query->row();
			
				return $row->value;
			}
		}
	}
	
	public function set($name, $value)
	{
		$this->delete($name);
		return $this->add($name, $value);
	}
	
	public function delete($name)
	{
		$name = $this->_format_name($name);
		
		if($this->exists($name))
		{
			if($this->CI->db->delete('config', array('name' => strtolower($name))))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return true;
		}
	}
	
	//Returns an array of all the config items
	//Start and end define the record to begin and end at respectively
	//Leave blank to return everything
	public function get_all($start = NULL, $limit = NULL)
	{
		$this->CI->db->order_by('name', 'ASC');
		$query = $this->CI->db->get('config', $limit, $start);
		
		if($query->num_rows() > 0)
		{
			return $query->result_array();
		}
	}
	
	
	//Returns false on failure, array on success
	public function filter($name, $start = NULL, $limit = NULL)
	{
		$this->CI->db->order_by('name', 'ASC');
		$name = $this->_format_name($name);
		
		$this->CI->db->like('name', $name, 'after');
		
		$query = $this->CI->db->get('config', $limit, $start);
		
		if($query->num_rows() > 0)
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;	
		}
	}
	
	//Checks to see if the parameter already exists
	private function exists($name)
	{
		$query = $this->CI->db->get_where('config', array('name' => strtolower($name)));
		
		if($query->num_rows() > 0)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	//Adds the parameter if it does not exist
	private function add($name, $value)
	{
		if(!$this->exists($name))
		{
			$data = $this->parse($value);
			$data['name'] = $this->_format_name($name);
			
			$query = $this->CI->db->insert('config', $data);
			
			return $query;
		}
		else
		{
			return FALSE;	
		}
	}
	
	private function _format_name($name)
	{
		return preg_replace('/[^a-zA-Z0-9\._-]/', '', strtolower($name));
	}
	
	//Parses comments out of the value
	//Comments defined as everything after the double forward slash (//)
	private function parse($value)
	{
		$parsed = array('value' => null, 'note' => null);
		$matches = array();
		
		if(preg_match('/(?<=\/\/).*/', $value, $matches))
		{
			$parsed['note'] = $matches[0];
		}
		
		$parsed['value'] = trim(preg_replace('/\/\/.*/', '', $value));
		
		return $parsed;
	}
	
}