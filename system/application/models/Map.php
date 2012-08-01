<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Map extends Model {
	
	private $CI = NULL;
	private $api_key;
	private $base_url = 'http://maps.googleapis.com/maps/api/geocode/json?';
	
	public function Address()
	{
		parent::construct();
		
		$this->CI =& get_instance();
		$this->api_key = $this->CI->config->item('google_api_key');	
	}	
	
	//Returns Lat and Lon for a given address
	//If the address doesn't exist OR it is too
	//ambiguous (returns more than one result)
	//it will return FALSE
	public function geocode_address($addr)
	{
		if(is_object($addr) && get_class($addr) == 'StructLocation')
		{
			$data = $this->gmaps_request((string)$addr);
		}
		elseif (is_string($addr))
		{
			$data = $this->gmaps_request(array('address' => $addr));
		}
		else
		{
			log_message('error', 'Variable passed to geocode_address is ' . gettype($address) . '. StructLocation object or string expected');
			return FALSE;	
		}
		
		if($data && count($data) == 1)
		{
			return array('latitude' => $data[0]['latitude'], 'longitude' => $data[0]['longitude']);
		}
		else
		{
			log_message('error', 'Address is ambiguous. More than one result returned.');
			return FALSE;	
		}
	}
	
	//Gets an address from a point on a map
	public function reverse_geocode($lat, $lon)
	{
		//todo
	}
	
	//Returns TRUE if the address is valid and specific
	//enough to only refer to one location.
	public function validate_address($addr)
	{
		if(is_object($addr) && get_class($addr) == 'StructLocation')
		{
			$data = $this->gmaps_request((string)$addr);
		}
		elseif (is_string($addr))
		{
			$data = $this->gmaps_request(array('address' => $addr));
		}
		else
		{
			log_message('error', 'Variable passed to validate_address is ' . gettype($address) . '. StructLocation object or string expected');
			return FALSE;	
		}
		
		if($data !== FALSE)
		{
			$result = array();
			
			if(count($data) == 1)
			{
				return TRUE;
			}
			else return FALSE;
		}
		else
		{
			log_message('error', 'Error map_model, validate_address.');
			return FALSE;	
		}
	}
	
	//Returns all the components of a given
	//address string along with Lat and Lon
	//If the address is ambiguous it will 
	//return a list of addresses.
	public function parse_address($addr)
	{
		if(is_object($addr) && get_class($addr) == 'StructLocation')
		{
			$data = $this->gmaps_request((string)$addr);
		}
		elseif (is_string($addr))
		{
			$data = $this->gmaps_request(array('address' => $addr));
			
			//Getting around google ignoring two-part street numbers
			//i.e. 9504-b
			$location = new StructLocation();
			
			$matches = array();
			
			if(preg_match('/^[0-9]+-[a-zA-Z0-9]+(?= )/', $addr, $matches))
			{
				$parts = explode('-', $matches[0]);
				
				$location->subpremise = $parts[1];
			}
			
		}
		else
		{
			log_message('error', 'Variable passed to geocode_address is ' . gettype($address) . '. StructLocation object or string expected');
			return FALSE;	
		}
		
		if($data !== FALSE)
		{
			$result = array();
			
			foreach ($data as $key => $address) {
				
				$result[$key] = new StructLocation();
				
				$result[$key]->number 			= (isset($address['street_number']))?$address['street_number']	:$location->number;
				$result[$key]->route 			= (isset($address['route']))		?$address['route']			:$location->route;
				$result[$key]->subpremise 		= (isset($address['subpremise']))	?$address['subpremise']		:$location->subpremise;
				$result[$key]->locality 		= (isset($address['locality']))		?$address['locality']		:$location->locality;
				$result[$key]->admin_level_1 	= (isset($address['administrative_area_level_1']))?$address['administrative_area_level_1']:$location->admin_level_1;
				$result[$key]->admin_level_2 	= (isset($address['administrative_area_level_2']))?$address['administrative_area_level_2']:$location->admin_level_2;
				$result[$key]->postal_code		= (isset($address['postal_code']))	?$address['postal_code']	:$location->postal_code;
				$result[$key]->neighborhood		= (isset($address['neighborhood']))	?$address['neighborhood']	:$location->neighborhood;
				$result[$key]->latitude			= (isset($address['latitude']))		?$address['latitude']		:$location->latitude;
				$result[$key]->longitude		= (isset($address['longitude']))	?$address['longitude']		:$location->longitude;
				
			}
			
			if(count($result) == 1)
			{
				return $result[0];
			}
			else
			{
				return $result;	
			}
		}
		else
		{
			log_message('error', 'Error map_model, validate_address.');
			return FALSE;	
		}
	}
	
	private function gmaps_request($values)
	{
		$url = $this->base_url;
		
		foreach($values as $key => $value)
		{
			$url .= $key . '=' . urlencode($value) . '&';
		}
		
		$url .= 'sensor=false';
	
		 $ch = curl_init($url);
		 
		 curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		 
		 $data = json_decode(curl_exec($ch));
		 
		 if($data->status == 'OK')
		 {
		 	return $this->parse_gmaps_response($data);
		 }
		 else
		 {
		 	log_message('error', 'Google Maps API response: status \'' . $data->status . '\'.');
		 	return FALSE;
		 }
	}
	
	private function parse_gmaps_response($data)
	{
		$results = array();
		
		foreach($data->results as $key => $value)
		{
			foreach($value->address_components as $component)
			{
				$results[$key][$component->types[0]] = $component->long_name;
			}
			
			$results[$key]['latitude'] = $value->geometry->location->lat;
			$results[$key]['longitude'] = $value->geometry->location->lng;
		}
		
		return $results;
	}
}