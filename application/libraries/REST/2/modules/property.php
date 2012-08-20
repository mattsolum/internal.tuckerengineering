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
			$results = $this->CI->Map->parse_address($this->API->id);
			
			if(!is_array($results))
			{
				$results = array($results);
			}
			
			foreach($results AS $prop)
			{
				$include_subpremise = ($prop->subpremise != '');
				
				$id = $this->CI->Property->exists($prop, $include_subpremise);
				
				if($id !== FALSE)
				{
					if(is_array($id))
					{
						foreach($id AS $sid)
						{
							$properties[] = $this->CI->Property->get($sid);
						}
					}
					else
					{
						$properties[] = $this->CI->Property->get($id);	
					}
				}
			}
			
			if(count($properties) == 1)
			{
				$properties = $properties[0];
			}
			else if(count($properties) == 0)
			{
				$properties = FALSE;
			}
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
		
		$this->API->id = preg_replace('/[^0-9]/', '', $this->API->id);
		
		$property->id 						= ($this->API->id != '')							?$id								:NULL; 
		
		$property->location->number 		= (isset($input->location->number))			?$input->location->number			:'0';
		$property->location->route			= (isset($input->location->route))			?$input->location->route			:'';
		$property->location->subpremise		= (isset($input->location->subpremise))		?$input->location->subpremise		:'';
		$property->location->locality		= (isset($input->location->locality))		?$input->location->locality			:'';
		$property->location->admin_level_1	= (isset($input->location->admin_level_1))	?$input->location->admin_level_1	:'';
		$property->location->admin_level_2	= (isset($input->location->admin_level_2))	?$input->location->admin_level_2	:'';
		$property->location->postal_code	= (isset($input->location->postal_code))	?$input->location->postal_code		:'';
		$property->location->neighborhood	= (isset($input->location->neighborhood))	?$input->location->neighborhood		:'';
		$property->location->latitude		= (isset($input->location->latitude))		?$input->location->latitude			:'';
		$property->location->longitude		= (isset($input->location->longitude))		?$input->location->longitude		:'';
		
		//If there is no latitude there probably is not a longitude either
		//no point checking.
		//Geocode and parse if no lat/lon provided.
		if($property->location->latitude == '')
		{
			$parsed_location = $this->CI->Map->parse_address((string)$property->location);
			
			if(!is_array($parsed_location) && get_class($parsed_location) == 'StructLocation')
			{
				$property->location = $parsed_location;
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