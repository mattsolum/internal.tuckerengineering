<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Property extends CI_Model {
	
	private $CI = NULL;
	
	public function Property()
	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->model('Meta');
		$this->CI->load->model('Note');
	}
	
	
	public function insert($property)
	{
		return $this->commit($property);
	}
	
	//Return the ID on success
	//and FALSE on failure
	//TODO: Split into Create and Update methods, remove reliance on delete for update.
	public function commit($property)
	{
		if(!$property->is_valid())
		{
			log_message('error', 'Error in model Property method commit: property is not valid.');
			return FALSE;
		}
		
		$this->CI->db->trans_start();
		
		$data = array();
		
		$id = ($property->id != 0)?$property->id:$this->exists($property);
		
		if($id !== FALSE)
		{
			if(!is_array($id))
			{
				$property->id = $id;

				$this->CI->Event->trigger('property.commit.before.update', $property);
				$this->delete($id);
			}
			else
			{
				log_message('Error', 'Error in Property method insert: multiple properties exists similar to given.');
				return FALSE;	
			}
		}
		else
		{
			$property->id = $this->get_next_property_id();
			$property->date_added = now();
			$this->CI->Event->trigger('property.commit.before.create', $property);
		}

		$data['property_id'] 					= $property->id;

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
		
		$data['date_added']						= $property->date_added;
		$data['date_updated']					= now();
		
		$sub_search = ($property->subpremise != '')?' ' . $property->subpremise:'';
		
		$data['search_text']					= strtolower($property->number . ' ' . $property->route . $sub_search . ' ' . $property->locality);
		
		$query = $this->CI->db->insert('properties', $data);
		
		if($id == FALSE)
		{
			$id = $this->exists($property);
		}
		
		$property->set_id($id);
		$this->CI->Note->commit($property->notes);
		
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
			$this->CI->Event->trigger('property.commit.after', $this->get($id));
			return $id;
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
			
			$property->notes			= $this->CI->Note->get_by_property($property->id);
			
			//Get meta data
			$meta = $this->CI->Meta->get($id, 'property');
			if($meta !== FALSE)
			{
				foreach($meta AS $meta_value)
				{
					$key = $meta_value['name'];
					$property->info[$key] = $meta_value['value'];
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
	
	public function get_by_string($str)
	{
		$str = preg_replace('/(^| )east($| )/', ' e ', $str);
		$str = preg_replace('/(^| )west($| )/', ' w ', $str);
		$str = preg_replace('/(^| )north($| )/', ' n ', $str);
		$str = preg_replace('/(^| )south($| )/', ' s ', $str);
		
		$where = array('search_text' => trim(strtolower($str)));
		
		$query = $this->CI->db->get_where('properties', $where);
		
		if($query->num_rows() > 0)
		{
			$result = $query->row();
			
			$id = $result->property_id;
			
			return $this->get($id);
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
			if($query->num_rows() == 1)
			{
				$result = $query->row();
				return $result->property_id;
			}
			else
			{
				$final = array();
				foreach($query->result() as $row)
				{
					$final[] = $row->property_id;
				}
				
				return $final;
			}
			
		}
		else return FALSE;
	}

	private function get_next_property_id()
	{
		$this->CI->db->select('property_id');
		$this->CI->db->from('properties');
		$this->CI->db->order_by('property_id', 'DESC');
		$this->CI->db->limit(1);

		$query = $this->CI->db->get();
		if($query->num_rows() > 0)
		{
			$row = $query->row(0);

			return $row->property_id + 1;
		}

		return 0;
	}
}