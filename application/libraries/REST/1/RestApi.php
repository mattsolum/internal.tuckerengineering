<?php defined('BASEPATH') OR exit('No direct script access allowed');

class RestApi
{
	private $auth;
	private $type;
	private $method;
	private $id;
	private $format;
	private $arguments;
	private $requestMethod;
	
	private $CI;
		
	public function __construct()
	{
		$this->CI =& get_instance();
		$this->_parse_request();
		
		$method = $this->method;
		$init_method = '_' . $this->type;
		
		//Ensures the requested method is not private 
		//and that both the initialization method and the requested method exist.
		if( substr($this->method, 0, 1) != '_' && ( method_exists($this, $init_method) && method_exists($this, $method) ) )
		{	
			$this->$init_method();
			$this->$method();
		}
		else
		{
			$this->_error('Requested method does not exist.');
		}
	}
	
	private function _parse_request()
	{
		//URL format: {version}/{auth}/{method}/{!id}{.format}{?arguments}
		//Clean up the request method since it will be passed as part of the method name to the library
		$requestMethod = substr(preg_replace('/[^a-z]/','', strtolower($_SERVER['REQUEST_METHOD'])), 0, 6);
		
		
		$segments = explode('/', $_SERVER['REQUEST_URI']);
		$lastSegment = $segments[count($segments) - 1];
		
		//First, extract the query string
		if(strpos($lastSegment, '?') !== FALSE)
		{
			$querystring = substr($lastSegment, strpos($lastSegment, '?') + 1);
			
			$lastSegment = str_replace('?' . $querystring, '', $lastSegment);
			
			//Now lets parse the query string into arguments
			$arguments = explode('&', $querystring);
			foreach($arguments as $argument)
			{
				$nv = explode('=', $argument);
				$this->arguments[urldecode($nv[0])] = urldecode($nv[1]);
			}
		}
		
		//Now extract the file type, if it exists
		$filetype = array();
		if(preg_match('/(?<=\.)(json|xml|html|htm)/i', $lastSegment, $filetype))
		{
			$this->format = $filetype[0];
			$lastSegment = str_ireplace('.' . $this->format, '', $lastSegment);
		}
		
		//Is the last segment an Identifier?
		$idexists = 0;
		
		if(substr($lastSegment, 0, 1) == '!')
		{
			$this->id = urldecode(substr($lastSegment, 1));
			$idexists = 1;
		}
		
		//Pull out the version and auth token
		//Very easy
			$this->auth = $this->CI->uri->segment(3);
			
			$this->type = strtolower(preg_replace('/(_.*$|[^a-zA-Z0-9])/', '', $this->CI->uri->segment(4)));
			
			//Now lets extract the method
			if($this->CI->uri->total_segments() > 3)
			{
				for($i = 4; $i <= $this->CI->uri->total_segments() - $idexists; $i++)
				{
					if(!$idexists && $i == $this->CI->uri->total_segments())
					{
						$this->method .= '_' . $lastSegment;
					}
					else
					{
						$this->method .= '_' . preg_replace('/[^a-zA-Z0-9]/', '', $this->CI->uri->segment($i));	
					}
				}
				
				$this->method = strtolower(substr($this->method, 1) . '_' . $requestMethod);
				$this->requestMethod = $requestMethod;
			}
	}
	
	private function _user_auth($key)
	{
		
	}
	
	private function _output($data = array(), $error = false)
	{
		switch ($this->format) {
			case 'xml':
				$this->_output_xml($data, $error);
				break;
			case 'json':
				$this->_output_json($data, $error);
				break;
			case 'html':
				$this->_output_html($data, $error);
				break;
			case 'htm':
				$this->_output_html($data, $error);
				break;
			default:
				$this->_output_xml($data, $error);
				break;
		}
	}
	
	private function _output_xml($data, $error = false)
	{
		$xml = '<APIResponse><result>';
		$xml .= ($error)?'error</result>':'success</result>';
		
		foreach($data as $key => $value)
		{
			//If the root array is numerically indexed, 
			//which would be improper for XML,
			//use the request type as the tag name
			if(preg_match('/^[0-9]/', $key))
			{
				$xml .= $this->_recurse_xml($this->type, $value );
			}
			else 
			{
				$xml .= $this->_recurse_xml($key, $value);
			}
		}
		
		$xml .= '</APIResponse>';
		
		$this->CI->output->set_header('Content-Type: text/xml');
		echo($xml);
	}
	
	private function _recurse_xml($key, $value)
	{
		$xml = "<$key>";
		$flag = false;
		
		if(is_array($value) || is_object($value))
		{
			foreach ($value as $key2 => $value2) {
				if(preg_match('/^[0-9]+$/', $key2))
				{
					if(!$flag)
					{
						$xml = '';
						$flag = true;
					}
					
					$xml .= $this->_recurse_xml($key, $value2);
				}
				else
				{
					$xml .= $this->_recurse_xml($key2, $value2);	
				}
			}
		}
		else 
		{
			$xml .= $value;	
		}
		
		if(!$flag) $xml .= "</$key>";
		
		return $xml;
	}
	
	private function _output_json($data, $error = false)
	{
		$this->CI->output->set_header('Content-Type: application/json');
		
		$final = array();
		$final['result'] = ($error)?'error':'success';
		$final['data'] = $data;
		
		echo(json_encode($final));
	}
	
	private function _output_html($data, $error = FALSE)
	{
		
		if($error == FALSE && isset($this->arguments['view']) && file_exists(APPPATH . 'views/' . $this->arguments['view'] . EXT))
		{
			
			$this->CI->load->view($this->arguments['view'], array($this->type => $data));
		}
		else
		{
			$html  = '<span class="result">';
			$html .= ($error)?'error</span>':'success</span>';
			
			$html .= '<ul>';
			
			foreach($data as $value)
			{
				$html .= $this->_recurse_html($value);
			}
			
			$html .= '</ul>';
			
			$this->CI->output->set_header('Content-Type: text/html');
			echo($html);	
		}
	}
	
	private function _recurse_html($value, $oneOfMany = TRUE)
	{
		$html = '<li>';
		$flag = false;
		
		if(is_array($value) || is_object($value))
		{
			$html .= '<ul>';
			
			foreach ($value as $value2) {
				$html .= $this->_recurse_html($value2);
			}
			
			$html .= '</ul>';
		}
		else 
		{
			$html .= $value;	
		}
		
		$html .= '</li>';
		
		return $html;
	}
	
	private function _error($message)
	{
		$data['errorMessage'] = $message;
		$data['parameters']['version'] = '1';
		$data['parameters']['auth'] = $this->auth;
		$data['parameters']['method'] = $this->method;
		$data['parameters']['id'] = $this->id;
		$data['parameters']['format'] = $this->format;
		$data['parameters']['arguments'] = $this->arguments;
		$data['parameters']['post_values'] = $this->CI->input->post(NULL, TRUE);
		$data['parameters']['type'] = $this->type;
 		
		log_message('ERROR', $message . ' :: Parameters: version 1, auth ' . $this->auth . ', method ' . $this->method . ', ID ' . $this->id . ', format ' . $this->format . ', arguments ' . print_r($this->arguments, true) . '.');
		
		$this->CI->output->set_status_header('400');
		$this->_output($data, true);
	}
	
	public function set_format($format)
	{
		if($format != '')
		{
			$this->format = $format;
		}
	}
	
	
	/*
	|
	| CONFIG
	|
	| Allows getting, setting, and deleting of config items.
	|
	*/
	
	//Initializes for all config functions
	private function _config()
	{
		$this->CI->load->model('Setting');
		
		//If start and limit are passed as arguments clear them of all unwanted characters
		//and if they were not, set them both to NULL so we can refrence
		//them with impunity.
		
		$this->arguments['start'] = (isset($this->arguments['start']))?preg_replace('/[^0-9]/', '', $this->arguments['start']):NULL;
		$this->arguments['limit'] = (isset($this->arguments['limit']))?preg_replace('/[^0-9]/', '', $this->arguments['limit']):NULL;
	}
	
	public function config_post() 
	{
		$data['name'] = ($this->id)?$this->id:strtolower($this->CI->input->post('name'));
		$data['value'] = $this->CI->input->post('value');
		
		if($data['name'] && $data['value'])
		{
			$this->CI->Setting->set($data['name'], $data['value']);
			$this->_output();
		}
		else
		{
			$this->_error('Name or value is missing.');
		}
	}
	
	public function config_put()
	{
		$this->config_post();
	}
	
	public function config_get() 
	{
		$config['name'] = $this->id;
		$config['value'] = setting($this->id);
		
		if($config['value'] != '')
		{
			$this->_output($config);
		}
		else
		{
			$this->_error('Config item "' . $this->id . '" does not exist.');
		}
	}
	
	public function config_delete() 
	{
		if($this->CI->Setting->delete($this->id))
		{
			$this->_output();
		}
	}
	
	public function config_filter_get()
	{	
		$items = $this->CI->Setting->filter($this->id, $this->arguments['start'], $this->arguments['limit']);
		
		if($items !== FALSE)
		{
			$this->_output($items);
		}
		else
		{
			$this->error("No items found like '" . $this->id . "'.");	
		}
		
	}
	
	public function config_all_get()
	{
		$items = $this->CI->Setting->get_all($this->arguments['start'], $this->arguments['limit']);
		
		if($items !== FALSE)
		{
			$this->_output($items);
		}
		else
		{
			$this->error("Apparently there are no config items in the database. Either that or there is an internal error somewhere.");	
		}
	}
	
	/*
	|
	| Property
	|
	| Provides an interface for geocoding, validating, and searching addresses
	|
	*/
	
	private function _property()
	{
		$this->CI->load->model('Map');
		$this->CI->load->model('Property');
	}
	
	
	//This will return an address object
	//provided an ID or an address string
	public function property_get()
	{
		if($this->id != '' && preg_match('/^[0-9]+$/', $this->id))
		{
			//If the ID is a number
			$property = $this->CI->Property->get($this->id);
		}
		else if ($this->id != '')
		{
			$property = new StructProperty();
			
			$property->location = $this->CI->Map->parse_address($this->id);
			
			$id = $this->CI->Property->exists($property);
			if($id !== FALSE)
			{
				$property = $this->CI->Property->get($id);
			}
			else
			{
				$property = FALSE;
			}
		}
		
		if(isset($property) && $property != FALSE)
		{
			$this->_output($property);
		}
		else $this->_error('No property exists with the id \'' . $this->id . '\'.');
	}
	
	public function property_delete()
	{
		if($this->CI->Property->delete($this->id))
		{
			$this->_output();
		}
		else $this->_error("Failure deleting property ID '{$this->id}'.");
	}
	
	public function property_post()
	{	
		$data = json_decode(urldecode($this->CI->input->post('data')));
		$property = new StructProperty();
		
		$this->id = preg_replace('/[^0-9]/', '', $this->id);
		
		$property->id 						= ($this->id != '')?$id:NULL; 
		
		$property->location->number 		= (isset($data->location->number))?$data->location->number:'0';
		$property->location->route			= (isset($data->location->route))?$data->location->route:'';
		$property->location->subpremise		= (isset($data->location->subpremise))?$data->location->subpremise:'';
		$property->location->locality		= (isset($data->location->locality))?$data->location->locality:'';
		$property->location->admin_level_1	= (isset($data->location->admin_level_1))?$data->location->admin_level_1:'';
		$property->location->admin_level_2	= (isset($data->location->admin_level_2))?$data->location->admin_level_2:'';
		$property->location->postal_code	= (isset($data->location->postal_code))?$data->location->postal_code:'';
		$property->location->neighborhood	= (isset($data->location->neighborhood))?$data->location->neighborhood:'';
		$property->location->latitude		= (isset($data->location->latitude))?$data->location->latitude:'';
		$property->location->longitude		= (isset($data->location->longitude))?$data->location->longitude:'';
		
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
		if(isset($data->info))
		{
			foreach($data->info AS $name => $value)
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
				
				$this->_output($result);
			}
			else $this->_error('Failed to insert property into database.');
		}
		else
		{
			$this->_error('Data received is invalid: ' . (string)$property);	
		}
	}
	
	public function property_put()
	{
		$this->property_post();
	}
	
	//Returns latitude and longitude only
	public function property_geocode_get()
	{
		$result = $this->CI->Map->geocode_address($this->id);
		
		if($result !== FALSE)
		{
			$this->_output($result);
		}
		else
		{
			$this->_error('Unable to geocode address \'' . $this->id . '\'.');	
		}
	}
	
	//Returns a property if there is enough information to return only one result
	public function property_validate_get()
	{
		$data = $this->CI->Map->validate_address($this->id);
		
		if($data !== FALSE)
		{
			$this->_output(array('location' => 'valid'));
		}
		else
		{
			$this->_output(array('location' => 'invalid'));
		}
	}
	
	public function property_parse_get()
	{
		$data = $this->CI->Map->parse_address($this->id);
		
		if($data !== FALSE)
		{
			$this->_output($data);
		}
		else
		{
			$this->_error('Could not parse address or no addresses found matching the provided query.');
		}
	}
}