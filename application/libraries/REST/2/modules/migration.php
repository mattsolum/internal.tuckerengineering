<?PHP

class MigrationAPI extends PrototypeAPI
{
	public $office_location = NULL;
	
	public function __construct(&$API)
	{
		parent::__construct($API);
		$this->CI->load->library('migration/Migrator');
		$this->CI->load->helper('directory');
		$this->CI->load->model('Map');
		$this->CI->load->model('Client');
		$this->CI->load->model('Property');
		$this->CI->load->model('Note');

		$this->office_location = new StructProperty();
		$this->office_location->number 			= 1311;
		$this->office_location->route 			= 'Chisholm Trail';
		$this->office_location->subpremise 		= 303;
		$this->office_location->locality		= 'Round Rock';
		$this->office_location->admin_level_1 	= 'Texas';
		$this->office_location->admin_level_2 	= 'Williamson';
		$this->office_location->postal_code		= 78681;
		$this->office_location->id 				= 0;
	}

	public function get()
	{
		$id = preg_replace('/[^0-9]/', '', $this->API->id);

		if($id != NULL)
		{
			$filename = './resources/migration/data/' . $id . '.txt';
			if(file_exists($filename))
			{
				return file_get_contents($filename);
			}
			else
			{
				return '0';
			}
		}
		else
		{
			return $this->id_get();
		}
	}

	public function id_get()
	{
		return microtime(true) * 10000;
	}

	public function put()
	{
		return $this->post();
	}

	public function post()
	{
		$id = preg_replace('/[^0-9]/', '', $this->API->id);

		if($id != NULL && $this->input->POST('status') != FALSE)
		{
			$filename = './resources/migration/data/' . $id . '.txt';
			return file_put_contents($filename, $this->input->POST('status'));
		}
	}

	public function client_post()
	{
		$json = json_decode($this->CI->input->post('data'));

		$passed_id = preg_replace('/[^0-9]/', '', $this->API->id);
		
		$client = new StructClient();
		$client->set_from_json($json);

		if($passed_id != '')
		{
			$client->id = $this->API->id;
		}

		log_message('error', '--- migration/client:POST called for ' . $client->id . ' ' . $client->name);

		$client->name = ucwords(strtolower($client->name));
		
		//If there is no latitude there probably is not a longitude either
		//no point checking.
		//Geocode and parse if no lat/lon provided.
		//If there is no latitude there probably is not a longitude either
		//no point checking.
		//Geocode and parse if no lat/lon provided.
		$old_location = clone $client->location;

		if($client->location->latitude == '')
		{
			$parsed_location = $this->CI->Map->parse_address($client->location);
			
			if($parsed_location != FALSE && !is_array($parsed_location) && get_class($parsed_location) == 'StructProperty')
			{
				$client->location->set_location($parsed_location);
			}
			
			unset($parsed_location);
		}

		if(!$client->location->is_valid())
		{
			log_message('error', '--- migration/client:POST address received is invalid');
			$client->location = $this->office_location;
			$client->add_note(0, 'The address given was formatted poorly. The client has been given the office address. Old address was: ' . $old_location->location_string());
		}
		
		if($client->is_valid())
		{
			$id = $this->CI->Client->commit($client);
			if($id !== FALSE)
			{
				log_message('error', '--- migration/client:POST Client successefully committed ' . $client->id . ' ' . $client->name);
				return array('id' => $id);
			}
			else
			{
				log_message('error', '--- migration/client:POST FAILED ' . $client->id . ' ' . $client->name);
				$this->error = 'Error inserting client into database. Client given: ' . $client;
				return FALSE;
			}
		}
		else
		{
			log_message('error', '--- migration/client:POST client is invalid ' . $client->id . ' ' . $client->name);
			$this->error = 'Data given is not a valid client object. Client given: ' . $client;
			return FALSE;	
		}
	}
}