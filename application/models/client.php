<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Client extends CI_Model {
	
	private $CI = NULL;
	
	public function Client()
	{
		parent::__construct();
		$this->CI =& get_instance();	
		$this->CI->load->model('Property');
		$this->CI->load->model('Accounting');
	}
	
	
	//Return the ID on success
	//and FALSE on failure
	public function insert($client)
	{	
		if($client->is_valid())
		{
			$this->CI->db->trans_start();
			
			$data = array();
			
			$id = ($client->id != 0)?$client->id:$this->exists($client);
			
			if($id !== FALSE)
			{
				$data['client_id'] 	= $id;
				$data['date_added']	= $client->date_added;
				$this->delete($id);
			}
			else
			{
				$data['date_added'] = now();	
			}
			
			$property_id = $this->CI->Property->insert($client->location);
			if($property_id === FALSE)
			{
				return FALSE;
			}
			
			$data['name']			= $client->name;
			$data['search_name']	= strtolower(preg_replace('/[^a-zA-Z ]/', '', $client->name));
			$data['title']			= $client->title;
			$data['property_id']	= $property_id;
			$data['date_updated']	= now();
			
			$query = $this->CI->db->insert('clients', $data);
			
			if($id === FALSE)
			{
				$id = $this->exists($client);
			}
			
			if(is_array($client->contact))
			{
				foreach($client->contact AS $info)
				{
					$this->insert_contact($id, $info);
				}
			}
			
			$this->CI->db->trans_complete();
			
			if($this->CI->db->trans_status() === FALSE)
			{
				log_message('Error', 'Error in Client method insert: transaction failed.');
				return FALSE;
			}
			else
			{
				return $id;
			}
		}
	}
	
	public function delete($id, $include_property = FALSE)
	{
		$id = preg_replace('/[^0-9]/', '', $id);
		
		$this->CI->db->trans_start();
		
		if($include_property === TRUE)
		{
			$client = $this->get($id);
			$this->CI->Property->delete($client->location->id);
		}
		
		$this->CI->db->delete('clients', array('client_id' => $id));
		$this->delete_contact($id);
		
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
		if(preg_match('/^[0-9]+$/', $id))
		{
			return $this->get_by_id($id);
		}
		else
		{
			return $this->get_by_string($id);	
		}
	}
	
	//Returns the client ID. Does not check if numeric ID's exist.
	public function get_id($id)
	{
		if(preg_match('/^[0-9]+$/', $id))
		{
			return $id;
		}
		else
		{
			//For the time being any id that is not a number will be treated as a name
			$this->CI->db->like('name', $id);
			$query = $this->CI->db->get('clients');
			
			if($query->num_rows() > 0)
			{
				$row = $query->row(0);
				
				return $row->client_id;
			}
		}
		
		return FALSE;
	}
	
	private function get_by_string($id)
	{
		//For the time being any id that is not a number will be treated as a name
		$this->CI->db->like('name', $id);
		$query = $this->CI->db->get('clients');
		
		//echo('Query: ' . $this->CI->db->last_query());
		
		if($query->num_rows() > 0)
		{
			$clients = array();
			
			foreach ($query->result() as $client_result)
			{
				$clients[] = $this->get_by_id($client_result->client_id);
			}
			
			if(count($clients) == 1)
			{
				return $clients[0];
			}
			else return $clients;
		}
		else
		{
			log_message('Error', 'Error in Client method get: no data found with given string ID.');
			return FALSE;
		} 
	}
	
	public function get_by_property_id($id)
	{
		$id = preg_replace('/[^0-9]/', '', $id);
		
		$query = $this->CI->db->get_where('clients', array('property_id' => $id));
		
		$return = array();
		
		if($query->num_rows() > 0)
		{
			foreach($query->result() AS $row)
			{
				$return[] = $this->get_by_id($row->client_id);
			}		
		}
		else
		{
			log_message('Error', 'Error in Client method get_by_property_id: no data found with given numeric ID.');
			return FALSE;	
		}
		
		if(count($return) == 1)
		{
			return $return[0];
		}
		else return $return;
	}
	
	private function get_by_id($id)
	{
		$id = preg_replace('/[^0-9]/', '', $id);
		
		$query = $this->CI->db->get_where('clients', array('client_id' => $id));
		
		//echo('Query: ' . $this->CI->db->last_query());
		
		if($query->num_rows() > 0)
		{
			$result = $query->row();
			
			$client = new StructClient();
			
			$client->id				= $result->client_id;
			$client->name			= $result->name;
			$client->title			= $result->title;
			
			$balance = $this->CI->Accounting->get_balance_by_client($client->id);
			
			$client->balance		= $balance[0]['balance'];
			
			$client->location		= ($result->property_id != NULL)?$this->CI->Property->get($result->property_id):NULL;
			$client->contact		= $this->get_contact($result->client_id);
			
			$client->date_added 	= $result->date_added;
			$client->date_updated 	= $result->date_updated;
			
			return $client;
		}
		else
		{
			log_message('Error', 'Error in Client method get: no data found with given numeric ID.');
			return FALSE;
		}
	}
	
	//Return ID on success and FALSE on failure
	public function exists($client)
	{
		$this->CI->db->like('search_name', $client->name, 'none');
		
		$query = $this->CI->db->get('clients');
		
		if($query->num_rows() > 0)
		{
			$result = $query->row();
			
			return $result->client_id;
		}
		else
		{
			return FALSE;
		}
	}
	
	public function insert_contact($client_id, $contact)
	{	
		$this->CI->db->trans_start();
		
		$data = array('client_id' => $client_id, 'type' => $contact->type, 'info' => $contact->info);
		
		$this->CI->db->insert('client_contact', $data);
		
		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in Client method insert_contact: transaction failed.');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	public function get_contact($client_id)
	{
		$where = array('client_id' => $client_id);
		
		$this->CI->db->order_by('type', 'ASC');
		
		$query = $this->CI->db->get_where('client_contact', $where);
		
		if($query->num_rows() > 0)
		{
			$result = array();
			
			
			
			foreach($query->result() as $row)
			{
				$current_contact = new stdClass;
				$current_contact->type = $row->type;
				$current_contact->info = $row->info;
				
				$result[] = $current_contact;
				unset($current_contact);
			}
			
			return $result;
		}
		
		return NULL;
	}
	
	public function delete_contact($client_id)
	{
		$where = array('client_id' => $client_id);
		
		$this->CI->db->trans_start();
		
		$query = $this->CI->db->delete('client_contact', $where);
		
		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in Client method delete_contact: transaction failed.');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
}