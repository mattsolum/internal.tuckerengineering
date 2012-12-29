<?PHP
	
class ClientAPI extends PrototypeAPI
{
	
	public function __construct(&$API)
	{
		parent::__construct($API);
		$this->CI->load->model('Client');
		$this->CI->load->model('Map');
	}
	
	public function get()
	{	
		$result = $this->CI->Client->get($this->API->id);
		
		if($result !== FALSE)
		{
			return $result;
		}
		else
		{
			$this->error = 'Client with ID "' . $this->API->id . '" does not exist.';
			return FALSE;	
		}
		
		return FALSE;
	}
	
	public function post()
	{
		$json = json_decode($this->CI->input->post('data'));
		
		$client = new StructClient();
		$client->set_from_json($json);
		
		//If there is no latitude there probably is not a longitude either
		//no point checking.
		//Geocode and parse if no lat/lon provided.
		//If there is no latitude there probably is not a longitude either
		//no point checking.
		//Geocode and parse if no lat/lon provided.
		if($client->location->latitude == '')
		{
			$parsed_location = $this->CI->Map->parse_address($client->location->location_string());
			
			if(!is_array($parsed_location) && get_class($parsed_location) == 'StructProperty')
			{
				$client->location->set_location($parsed_location);
			}
			
			unset($parsed_location);
		}
		
		if($client->is_valid())
		{
			$id = $this->CI->Client->commit($client);
			if($id !== FALSE)
			{
				return array('id' => $id);
			}
			else
			{
				$this->error = 'Error inserting client into database. Client given: ' . $client;
				return FALSE;
			}
		}
		else
		{
			$this->error = 'Data given is not a valid client object. Client given: ' . $client;
			return FALSE;	
		}
	}
	
	public function put()
	{
		$this->post($this->API);
	}
	
	public function delete()
	{
		if($this->API->id != '' && preg_match('/^[0-9]+$/', $this->API->id))
		{
			$this->CI->property->delete($this->API->id);
		}
		else if ($this->API->id != '')
		{	
			$client = $this->CI->Client->get($this->API->id);
			$this->CI->property->delete($client->id);
		}
		else
		{
			$this->error = 'Numeric or String ID required. None given.';
			return FALSE;	
		}
	}
	
	public function property_get()
	{
		if($this->API->id != '' && preg_match('/^[0-9]+$/', $this->API->id))
		{
			//If the ID is a number
			$property = $this->CI->Property->get($this->API->id);
		}
		else if ($this->API->id != '')
		{	
			$property = $this->CI->Property->get_by_string($this->API->id);
		}
		else
		{
			$this->error = 'Numeric or String ID required. None given.';
			return FALSE;	
		}
		
		if($property !== FALSE)
		{
			$clients = $this->CI->Client->get_by_property_id($property->id);
			
			if($clients !== FALSE)
			{
				return $clients;
			}
			else
			{
				$this->error = 'Error getting client by property: no clients with given property ID.';
				return FALSE;
			}
		}
		else
		{
			$this->error = 'Error getting client by property: no properties found with given ID.';	
			return FALSE;
		}
	}
}