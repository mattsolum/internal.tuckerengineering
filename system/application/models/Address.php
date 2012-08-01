<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Address extends Model {
	
	private $CI = NULL;
	
	public function Address()
	{
		parent::__construct();
		$this->CI =& get_instance();	
	}
	
	
	//Return the ID on success
	//and FALSE on failure
	public function insert($id = NULL, $addr)
	{
		$this->CI->db->trans_start();
		
		//Populating Data
		$id = $this->exists($addr);
		if($addr->is_valid() && $id !== FALSE)
		{
			$data['property_id'] 				= $id;
			$data['date_added']					= $addr->date_added;
			
			$this->delete($id);
		}
		else
		{
			$data['date_added']					= now();
		}
		
		$data['street_number'] 					= $addr->number;	
		$data['route']							= $addr->route;
		$data['subpremise']						= $addr->subpremise;
		$data['locality']						= $addr->locality;
		$data['administrative_area_level_1']	= $addr->admin_level_1;
		$data['administrative_area_level_2']	= $addr->admin_level_2;
		$data['postal_code']					= $addr->postal_code;
		$data['neighborhood']					= $addr->neighborhood;
		$data['latitude']						= $addr->latitude;
		$data['longitude']						= $addr->longitude;
		$data['date_modified']					= now();
		
		//Insert and check transaction
		$this->CI->db->insert('properties', $data);
		
		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in Address_model method insert_address: transaction failed.');
			return FALSE;
		}
		else
		{
			return $this->exists($addr);
		}
	}
	
	public function delete($id)
	{
		$id = preg_replace('/[^0-9]/', '', $id);
		
		$this->CI->db->trans_start();
		
		$this->CI->db->delete('properties', array('property_id' => $id));
		
		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in Address_model method insert_address: transaction failed.');
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
		
		$query = $this->CI->db->get_where('properties', array('property_id' => $id));
		
		if($query->num_rows() > 0)
		{
			$result = $query->row();
			
			$address = new StructAddress();
			
			$address->id 			= $result->property_id;
			
			$address->number 		= $result->street_number;
			$address->route			= $result->route;
			$address->subpremise	= $result->subpremise;
			$address->locality		= $result->locality;
			$address->admin_level_1 = $result->administrative_area_level_1;
			$address->admin_level_2	= $result->administrative_area_level_2;
			$address->postal_code	= $result->postal_code;
			$address->neighborhood	= $result->neighborhood;
			
			$address->latitude		= $result->latitude;
			$address->longitude		= $result->longitude;
			
			$address->date_added	= $result->date_added;
			$address->date_updated	= $result->date_updated;
		}
	}
	
	public function get_by_string($address_string)
	{
		$this->CI->load->model('Map');
		$result = $this->CI->Map->validate_address($address_string);
		
		if($result != FALSE && !is_array($result))
		{
			$id = $this->exists($result);
			if($id !== FALSE)
			{
				return $this->get($id);
			}
		}
		
		return FALSE;
	}
	
	//Accepts a job_id and returns an address object
	public function get_by_job($id)
	{
		$id = preg_replace('/[^0-9]/', '', $id);
		
		$query = $this->CI->db->get_where('jobs', array('job_id' => $id));
		
		$addresses = array();
		
		if($query->num_rows() > 0)
		{
			$results = $query->row();
			
			if($results->property_id != '')
			{
				return $this->get($results->property_id);
			}
		}
		
		return FALSE;
	}
	
	//Return ID on success and FALSE on failure
	public function exists($addr, $include_subpremise = TRUE)
	{
		$where = array();
		
		$where['street_number'] = $addr->number;
		$where['route']			= $addr->route;
		$where['locality'] 		= $addr->locality;
		
		if($include_subpremise == TRUE)
		{
			$where['subpremise'] = $addr->subpremise;
		}
		
		$query = $this->CI->db->get_where('properties', $where);
		
		if($query->num_rows() > 0)
		{
			$result = $query->row();
			return $result->property_id;
		}
		else return FALSE;
	}
}