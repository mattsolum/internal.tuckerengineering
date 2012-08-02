<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Property extends Model {
	
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
				$data['id'] 		= $id;
				$data['date_added']	= $property->date_added;
				$this->delete($id);
			}
			else
			{
					
			}
			
			$data['street_number']					= $property->location->number;
			$data['route']							= $property->location->route;
			$data['subpremise']						= $property->location->subpremis;
			$data['locality']						= $property->location->locality;
			$data['administrative_area_level_1']	= $property->location->admin_level_1;
			$data['administrative_area_level_2']	= $property->location->admin_level_2;
			$data['postal_code']					= $property->location->postal_code;
			$data['neighborhood']					= $property->location->neighborhood;
			$data['latitude']						= $property->location->latitude;
			$data['longitude']						= $property->location->longitude;
			
			$data['date_added']						= (isset($data['date_added']))?$data['date_added']:now();
			$data['date_updated']					= now();
			
			$query = $this->CI->db->insert('properties', $data);
			
			if($id == FALSE && $this->CI->db->affected_rows() == 1)
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
		
		$this->CI->db->delete('properties', array('id' => $id));
		
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
			
			$property->id 						= $result->property_id;
			$property->date_added 				= $result->date_added;
			$property->date_updated				= $result->date_updated;
			
			$property->location->number 		= $result->street_number;
			$property->location->route			= $result->route;
			$property->location->subpremise		= $result->subpremise;
			$property->location->locality		= $result->locality;
			$property->location->admin_level_1 	= $result->administrative_area_level_1;
			$property->location->admin_level_2	= $result->administrative_area_level_2;
			$property->location->postal_code	= $result->postal_code;
			$property->location->neighborhood	= $result->neighborhood;
			
			$property->location->latitude		= $result->latitude;
			$property->location->longitude		= $result->longitude;
			
			
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
		
		$where['street_number'] = $property->location->number;
		$where['route']			= $property->location->route;
		$where['locality'] 		= $property->location->locality;
		
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