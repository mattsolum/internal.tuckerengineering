<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Property extends CI_Model {
	
	private $CI = NULL;
	
	public function Property()
	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->model('Meta');
	}
	
	
	//Return the ID on success
	//and FALSE on failure
	public function insert($property)
	{	
		if($property->is_valid())
		{
			$this->CI->db->trans_start();
			
			$data = array();
			
			$id = $this->exists($property);
			
			if($id !== FALSE)
			{
				$data['property_id'] 		= $id;
				$data['date_added']			= $property->date_added;
				$this->delete($id);
			}
			else
			{
				$data['date_added'] = now();
			}
			
			$data['street_number']					= $property->number;
			$data['route']							= $property->route;
			$data['subpremise']						= $property->subpremise;
			$data['locality']						= $property->locality;
			$data['administrative_area_level_1']	= $property->admin_level_1;
			$data['administrative_area_level_2']	= $property->admin_level_2;
			$data['postal_code']					= $property->postal_code;
			$data['neighborhood']					= $property->neighborhood;
			$data['latitude']						= $property->latitude;
			$data['longitude']						= $property->longitude;
			
			$data['date_updated']					= now();
			
			$query = $this->CI->db->insert('properties', $data);
			
			if($id == FALSE)
			{
				$id = $this->exists($property);
			}
			
			
			
			foreach($property->info AS $key => $value)
			{
				$this->CI->Meta->insert($id, 'property', $key, $value);
			}
			
			$this->CI->db->trans_complete();
			
			if($this->CI->db->trans_status() === FALSE)
			{
				log_message('Error', 'Error in Property method insert: transaction failed.');
				return FALSE;
			}
			else
			{
				return TRUE;
			}
		}
		
		return FALSE;
	}
	
	public function delete($id)
	{
		$id = preg_replace('/[^0-9]/', '', $id);
		
		$this->CI->db->trans_start();
		
		$this->CI->db->delete('properties', array('property_id' => $id));
		
		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in Property method delete: transaction failed.');
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
			
			$property = new StructProperty();
			
			$property->id 				= $result->property_id;
			$property->date_added 		= $result->date_added;
			$property->date_updated		= $result->date_updated;
			
			$property->number 			= $result->street_number;
			$property->route			= $result->route;
			$property->subpremise		= $result->subpremise;
			$property->locality			= $result->locality;
			$property->admin_level_1 	= $result->administrative_area_level_1;
			$property->admin_level_2	= $result->administrative_area_level_2;
			$property->postal_code		= $result->postal_code;
			$property->neighborhood		= $result->neighborhood;
			
			$property->latitude			= $result->latitude;
			$property->longitude		= $result->longitude;
			
			
			//Get meta data
			$meta = $this->CI->Meta->get($id, 'property');
			if($meta !== FALSE)
			{
				foreach($meta AS $meta_value)
				{
					$key = $meta_value['name'];
					$property->info->$key = $meta_value['value'];
				}
			}
			
			return $property;
			
		}
		else
		{
			log_message('Error', 'Error in Property method get: no data found with given ID.');
			return FALSE;
		}
	}
	
	//Return ID on success and FALSE on failure
	public function exists($property, $include_subpremise = TRUE)
	{
		$where = array();
		
		$where['street_number'] = $property->number;
		$where['route']			= $property->route;
		$where['locality'] 		= $property->locality;
		
		if($include_subpremise == TRUE)
		{
			$where['subpremise'] = $property->subpremise;
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