<?PHP

class PropertyAPI extends PrototypeAPI
{
	
	function __construct(&$API)
	{
		parent::__construct($API);
		$this->CI->load->model('Map');
		$this->CI->load->model('Property');
		$this->CI->load->helper('loader');
	}
	
	public function get()
	{ 
		$properties = array();
		
		if($this->API->id != '' && preg_match('/^[0-9]+$/', $this->API->id))
		{
			//If the ID is a number
			$properties = $this->CI->Property->get($this->API->id);
		}
		else if ($this->API->id != '')
		{	
			$properties = $this->CI->Property->get_by_string($this->API->id);
		}
		
		if(isset($properties) && $properties != FALSE)
		{
			return $properties;
		}
		else 
		{	
			$this->error = 'No property exists with the id \'' . $this->API->id . '\'.';
			return FALSE;
		}
	}
	
	public function post()
	{
		$input = json_decode(urldecode($this->CI->input->post('data')));
		$property = new StructProperty();
		
		$property->set_from_json($input);
		
		//If there is no latitude there probably is not a longitude either
		//no point checking.
		//Geocode and parse if no lat/lon provided.
		if($property->latitude == '')
		{
			$parsed_location = $this->CI->Map->parse_address($property->location_string());
			
			if(!is_array($parsed_location) && get_class($parsed_location) == 'StructProperty')
			{
				$property->set_location($parsed_location);
			}
			
			unset($parsed_location);
		}
		
		//Add info to our property object
		if(isset($input->info))
		{
			foreach($input->info AS $name => $value)
			{
				$property->info->$name = $value;
			}
		}
		
		//todo: Support Notes
		
		if($property->is_valid())
		{
			if($this->CI->Property->insert($property))
			{
				if($property->id != '')
				{
					$result = array('id' => $property->id);
				}
				else
				{
					$result = array('id' => $this->CI->Property->exists($property));
				}
				
				return $result;
			}
			else
			{
				$this->error = 'Failed to insert property into database.';
				return FALSE;
			}
		}
		else
		{
			$this->error = 'Data received is invalid: ' . (string)$property;
			return FALSE;	
		}
	}
	
	public function put()
	{
		$this->post($this->API);
	}
	
	public function delete()
	{
		if($this->CI->Property->delete($this->API->id))
		{
			return TRUE;
		}
		else 
		{
			$this->error = "Failure deleting property ID '{$this->API->id}'.";
			return FALSE;
		}
	}
}