<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Client extends CI_Model {
	
	private $CI = NULL;
	
	public function Client()
	{
		parent::__construct();
		$this->CI =& get_instance();	
		$this->CI->load->model('Property');
	}
	
	
	//Return the ID on success
	//and FALSE on failure
	public function insert($client)
	{	
		if($property->is_valid())
		{
			$this->CI->db->trans_start();
			
			$data = array();
			
			$id = ($client->id != 0)?$client->id:$this->exists($client);
			
			if($id !== FALSE)
			{
				$data['client_id'] 	= $id;
				$data['date_added']	= $client->date_added;
				$this->delete();
			}
			else
			{
				$data['date_added'] = now();	
			}
			
			$property_id = $this->CI->Property->insert($client->property);
			if($property_id === FALSE)
			{
				return FALSE;
			}
			
			$data['name']			= $client->name;
			$data['title']			= $client->title;
			$data['property_id']	= $property_id;
			$data['date_updated']	= now();
			
			$query = $this->CI->db->insert('clients', $data);
			
			$this->CI->db->trans_complete();
			
			if($id === FALSE)
			{
				$id = $this->exists($client);
			}
			
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
		if(preg_match('/^[0-9]+$/', $id))
		{
			return $this->get_by_id($id);
		}
		else
		{
			return $this->get_by_string($id);	
		}
	}
	
	private function get_by_string($id)
	{
		 //For the time being we will only allow searching by name
		 //when the id is not a number
		 
		 $this->CI->db->like('name', $id, 'none');
		 
		 $query = $this->CI->db->get('clients');
		 
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
		 	log_message('Error', 'Error in Client method get: no data found with given numeric ID.');
		 	return FALSE;
		 }
		 
	}
	
	private function get_by_id($id)
	{
		$id = preg_replace('/[^0-9]/', '', $id);
		
		$query = $this->CI->db->get_where('clients', array('client_id' => $id));
		
		if($query->num_rows() > 0)
		{
			$result = $query->row();
			
			$client = new StructClient();
			
			$client->id			= $result->client_id;
			$client->name		= $result->name;
			$client->title		= $result->title;
			$client->location	= ($result->property_id != NULL)?$this->CI->Property->get($result->property_id):NULL;
			$client->contact	= $this->get_contact($result->client_id);
			
			return $client;
		}
		else
		{
			log_message('Error', 'Error in Client method get: no data found with given numeric ID.');
			return FALSE;
		}
	}
	
	private function get_contact($id)
	{
		$where = array('client_id' => $id);
		
		$query = $this->CI->db->get_where('client_contact', $where);
		
		if($query->num_rows() > 0)
		{
			$result = array();
			
			foreach($query->result() as $row)
			{
				$result[] = array('type' => $row->type, 'info' => $row->info);
			}
			
			return $result;
		}
		
		return NULL;
	}
	
	//Return ID on success and FALSE on failure
	public function exists($id)
	{
		 
	}
}