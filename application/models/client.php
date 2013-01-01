<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Client extends CI_Model {
	
	private $CI = NULL;
	
	public function __construct()
	{
		parent::__construct();
		$this->CI =& get_instance();	
		$this->CI->load->model('Property');
		$this->CI->load->model('Accounting');
		$this->CI->load->model('Note');
	}
	
	/**
	 * Alias for commit
	 * 
	 * @param  structClient $client
	 * @return Boolean FALSE on failure, Int on success
	 */
	public function insert($client)
	{
		return $this->insert($client);
	}
	
	/**
	 * Creates or updates a given client
	 * @param  StructClient $client
	 * @return Boolean FALSE on failure, Int on success
	 */
	public function commit($client)
	{
		log_message('error', '--- Client->commit() called for ' . $client->id . ' ' . $client->name);

		if(get_class($client) != 'StructClient' || !$client->is_valid())
		{
			log_message('error', 'Error in model Client method commit: client is not valid.');
			return FALSE;
		}

		$this->CI->db->trans_start();
		
		//Find the client's ID if it is not provided
		$id = $this->exists($client);
		
		//If the client's ID is not set and it does not exist
		//$id will be false.
		if($id === FALSE)
		{
			//Client does not exist; create it.
			$id = $this->create($client);
		}
		else
		{
			//Client exists; update it.
			$client->set_id($id);
			$this->update($client);
		}
		
		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in Client method insert: transaction failed.');
			return FALSE;
		}
		else
		{
			log_message('error', '---!! Client->commit() SUCCESS ' . $client->id . ' ' . $client->name);
			return $id;
		}
	}

	/**
	 * Creates a new client record
	 * 
	 * @param  StructClient $client
	 * @return Boolean FALSE on failure, int on success
	 */
	private function create($client)
	{
		log_message('error', '--- Client->create() called for ' . $client->id . ' ' . $client->name);
		$this->CI->Event->trigger('client.commit.before.create', $client);

		$this->CI->db->trans_start();

		$property_id = $this->CI->Property->commit($client->location);
		if($property_id === FALSE)
		{
			log_message('error', 'Error in model Client method commit: property failed to commit.');
			return FALSE;
		}
		
		$data['name']			= $client->name;
		$data['search_name']	= preg_replace('/[^a-zA-Z ]/', '', strtolower($client->name)); //Used for determining if a client exists or not
		$data['title']			= $client->title;
		$data['property_id']	= $property_id;
		$data['date_added'] 	= ($client->date_added != '')?$client->date_added:now();
		$data['date_updated']	= now();
		
		if($client->id != NULL)
		{
			$data['client_id']	= $client->id;
		}

		//Insert the client into the database
		$this->CI->db->insert('clients', $data);

		//Get the new ID
		$id = $this->exists($client);
		
		//Insert contacts
		if(is_array($client->contact))
		{
			$this->commit_contacts($client->id, $client->contact);
		}

		$client->set_id($id);
		$this->CI->Note->commit($client->notes);

		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in Client method create: transaction failed.');
			return FALSE;
		}
		else
		{
			log_message('error', '---!! Client->create() SUCCESS ' . $client->id . ' ' . $client->name);
			$this->CI->Event->trigger('client.commit.after.create', $this->get($id));
			return $id;
		}
	}

	/**
	 * Updates a client record
	 * 
	 * @param  StructClient $client
	 * @return Boolean FALSE on failure, Int on success
	 */
	private function update($client)
	{
		log_message('error', '--- Client->update() called ' . $client->id . ' ' . $client->name);
		$this->CI->db->trans_start();

		$property_id = $this->CI->Property->commit($client->location);
		if($property_id === FALSE)
		{
			log_message('error', 'Error in model Client method commit: property failed to commit.');
			return FALSE;
		}
		else
		{
			$client->location->id = $property_id;
		}

		$this->CI->Event->trigger('client.commit.before.update', $client);

		$data['name']			= $client->name;
		$data['search_name']	= preg_replace('/[^a-zA-Z ]/', '', strtolower($client->name)); //Used for determining if a client exists or not
		$data['title']			= $client->title;
		$data['property_id']	= $property_id;
		$data['date_updated']	= now();
		
		//Update the database
		$this->CI->db->where('client_id', $client->id);
		$this->CI->db->update('clients', $data);
		
		//Insert contacts
		if(is_array($client->contact))
		{
			$this->commit_contacts($client->id, $client->contact);
		}

		$client->add_note(0, 'Updated by ' . $this->CI->User->get_name() . '.');

		$client->set_id($client->id);
		$this->CI->Note->commit($client->notes);

		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in Client method update: transaction failed.');
			return FALSE;
		}
		else
		{
			log_message('error', '---!! Client->update() SUCCESS ' . $client->id . ' ' . $client->name);
			$this->CI->Event->trigger('client.commit.after.update', $this->get($client->id));
			return $client->id;
		}
	}

	/**
	 * Deletes a client from the database
	 * 
	 * @param  integer $id
	 * @param  boolean $include_property
	 * @return boolean
	 */
	public function delete($id, $include_property = FALSE)
	{
		$this->CI->Event->trigger('client.commit.delete', $id);
		log_message('Error', 'Why are we deleting client ' . $id . '?');


		$id = preg_replace('/[^0-9]/', '', $id);
		
		$this->CI->db->trans_start();
		
		if($include_property === TRUE)
		{
			$client = $this->get($id);
			$this->CI->Property->delete($client->location->id);
		}
		
		$this->CI->db->delete('clients', array('client_id' => $id));
		$this->delete_contacts($id);
		
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
			
			$client->balance 		= $this->CI->Accounting->get_balance_by_client($client->id);
			
			$client->location		= ($result->property_id != NULL)?$this->CI->Property->get($result->property_id):NULL;
			$client->notes			= $this->CI->Note->get_by_client($client->id);
			$client->contact		= $this->get_contacts($result->client_id);
			
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
		$this->CI->db->like('search_name', preg_replace('/[^a-zA-Z ]/', '', strtolower($client->name)), 'none');
		
		$query = $this->CI->db->get('clients');
		
		if($query->num_rows() > 0)
		{
			$result = $query->row();
			log_message('error', '---!! Client->exists() SUCCESS ' . $client->id . ' ' . $client->name);
			return $result->client_id;
		}
		else
		{
			log_message('error', '--- Client->exist() client does not exist ' . $client->id . ' ' . $client->name);
			return FALSE;
		}
	}

	private function commit_contacts($client_id, $contact)
	{
		if($this->get_contacts($client_id) != NULL)
		{
			$this->delete_contacts($client_id);
		}

		foreach($contact AS $item)
		{
			if(!$this->insert_contacts($client_id, $item))
			{
				return FALSE;
			}
		}

		return TRUE;
	}
	
	public function insert_contacts($client_id, $contact)
	{	
		$this->CI->db->trans_start();
		
		$data = array('client_id' => $client_id, 'type' => $contact->type, 'info' => $contact->info);
		
		$this->CI->db->insert('client_contact', $data);
		
		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in Client method insert_contacts: transaction failed.');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	public function get_contacts($client_id)
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
		
		return array();
	}
	
	public function delete_contacts($client_id)
	{
		$where = array('client_id' => $client_id);
		
		$this->CI->db->trans_start();
		
		$query = $this->CI->db->delete('client_contact', $where);
		
		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in Client method delete_contacts: transaction failed.');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
}