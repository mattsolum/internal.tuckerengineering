<?php defined('BASEPATH') OR exit('No direct script access allowed');

class RestApi
{
	public $auth;
	public $type;
	public $method;
	public $id;
	public $filetype;
	public $arguments;
	public $request_method;
	
	private $active_class;
	
	private $CI;
		
	public function __construct()
	{
		$this->CI =& get_instance();
		$this->arguments = new stdClass;
		
		$this->parse_request();
		
		//Check that the request is validated
		//We will check again to make sure that the
		//user is allowed to perform the action they have requested.
		if($this->auth() && $this->prep())
		{
			$method = $this->method;
			$result = $this->active_class->$method();
			
			if($result === TRUE)
			{
				$this->output('');
			}	
			else if($result !== FALSE)
			{
				$this->output($result);
			}
			else
			{
				$this->CI->output->set_status_header(500);
				$this->error($this->active_class->error);	
			}
		}
	}
	
	private function parse_request()
	{
		$this->CI->load->helper('file');
		$uri = str_ireplace('api/v2/', '', stristr($_SERVER['REQUEST_URI'], 'api/v2/'));
		
		//Parse out the querystring, if it exists.
		if(strpos($uri, '?') !== FALSE)
		{
			$querystring = explode('&', substr(strstr($uri, '?'), 1));
			
			foreach ($querystring as $value)
			{
				$key_val = explode('=', $value);
				$key = $key_val[0];
				$this->arguments->$key = $key_val[1];
			}
			
			unset($querystring);
			$uri = str_replace(strstr($uri, '?'), '', $uri);
		}
		
		
		//Parse out the filetype, if it exists
		if(strrpos($uri, '.') !== FALSE)
		{
			$filetype = strtolower(substr($uri, strrpos($uri, '.') + 1));
			
			//Check to make sure the last element is a valid file type.
			if(get_mime_by_extension('hello.' . $filetype) !== FALSE)
			{
				$this->filetype = $filetype;
				$uri = str_replace('.'.$this->filetype, '', $uri);
			}
			
			unset($filetype);
		}
		
		
		//Now assemble the method
		$segments = explode('/', $uri);
		
		$this->type = strtolower(array_shift($segments));
		
		$last_segment = array_pop($segments);

		$this->id = $last_segment;
		$this->method = implode('_', $segments);
		
		//Now get the HTTP request method
		$this->request_method = substr(preg_replace('/[^a-z]/','', strtolower($_SERVER['REQUEST_METHOD'])), 0, 6);
		
		//HTTPOverride
		if(isset($this->arguments->HTTPOverride))
		{
			$this->request_method = $this->arguments->HTTPOverride;
		}
		
		//Assemble the API method.
		$this->method = trim(preg_replace('/[^a-zA-Z0-9_]/', '', $this->method));
		
		$this->method = preg_replace('/^( *)_+/', '', $this->method);
		
		//TODO: Auth
	}
	
	//Assembles all the data into a nugget the subclasses can use.
	private function package()
	{
		$data = new stdClass;
		
		$data->id			= $this->id;
		$data->arguments	= $this->arguments;
		$data->auth			= TRUE;
		
		return $data;
	}
	
	private function prep()
	{
		//Set some locations
		$library_path = APPPATH . 'libraries/REST/2/';
		$module_path = $library_path . 'modules/';
		
		$classname = ucfirst($this->type) . 'API';
		
		//load the prototype file
		require_once($module_path . 'prototype.php');
		
		if($this->type == 'prototype' || !file_exists($module_path . $this->type . '.php'))
		{
			$this->error('Module "' . $this->type . '" does not exist.');
			return FALSE;
		}
		
		require_once($module_path . $this->type . '.php');
		
		//Make sure it is formatted properly
		if(class_exists($classname))
		{
			$this->active_class = new $classname($this);
		} 
		else {
			$this->error('Module "' . $this->type . '" does not exist.');
			return FALSE;
		}
		
		//Make sure the method we want to access exists
		
		//To determine if the last segment is truly an ID and not a further method name
		//We check if the method exists using the ID as part of the method name
		if(method_exists($this->active_class, trim($this->method . '_' . $this->id . '_' . $this->request_method, '_')))
		{
			$this->method .= trim($this->method . '_' . $this->id . '_' . $this->request_method, '_');
			$this->id = '';
		}
		else
		{
			//If it does not exist in the long format, try the method name without it
			if(method_exists($this->active_class, trim($this->method . '_' . $this->request_method, '_')))
			{
				//The ID is truly an ID, format it.
				$this->id = (string)str_replace('_', ' ', urldecode($this->id));
				$this->method = trim($this->method . '_' . $this->request_method, '_');
			}
			else
			{
				$this->error('Method "' . $this->method . '" does not exist in module "' . $this->type . '".');
				return FALSE;
			}
		}
		
		return TRUE;
	}
	
	private function auth()
	{
		//Todo: Fill in actual auth procedure.
		return TRUE;
	}
	
	private function error($message)
	{
		$data = array('message' => $message, 'debug' => array());
		$data['debug']['id'] 		= $this->id;
		$data['debug']['module'] 	= $this->type;
		$data['debug']['method'] 	= $this->method;
		$data['debug']['filetype'] 	= $this->filetype;
		$data['debug']['arguments'] = $this->arguments;
		$this->output($data, TRUE);
	}
	
	private function output($data, $error = FALSE)
	{	
		if(is_string($data) && $data == '')
		{
			$data = array();
		}
		elseif (is_string($data))
		{
			$data = array('message' => $data);	
		}
		
		if(is_array($data) && !$this->array_is_assoc($data))
		{
			$data = $this->paginate($data);
			if($data === FALSE)
			{
				return FALSE;
			}
		}
		
		$formats_path = APPPATH . 'libraries/REST/2/formats/';
		
		if(file_exists($formats_path . strtolower($this->filetype) . '.php'))
		{
			include $formats_path . strtolower($this->filetype) . '.php';
			
			$output_class = 'Api' . ucfirst(strtolower($this->filetype));
			
			if(class_exists($output_class))
			{
				$output = new $output_class($this);
				
				$this->CI->output->set_content_type($output->mime);
				$this->CI->output->set_output($output->format($data, $error));
			}
		}
		elseif(file_exists($formats_path . 'xml.php'))
		{
			include $formats_path . 'xml.php';
			
			$output_class = 'ApiXml';
			
			if(class_exists($output_class))
			{
				$output = new $output_class($this);
				
				$this->CI->output->set_content_type($output->mime);
				$this->CI->output->set_output($output->format($data, $error));
			}	
		}
		else
		{
			$this->CI->output->set_content_type('text/xml');
			$this->CI->output->set_output('<APIResponse><error>Output file not installed!</error></APIResponse>');
		}
	}
	
	private function paginate($data)
	{	
		//Make sure the top level element is an array AND it is a numeric array
		$per_page 	= (isset($this->arguments->per_page))?$this->arguments->per_page:10;
		$page		= (isset($this->arguments->page))?$this->arguments->page - 1:0;
		
		if($page < 0)
		{
			$page = 0;
		}
		
		$elements = count($data);
		
		if($elements == 0)
		{
			return $data;
		}
		
		$pages = ceil(count($data) / $per_page);

		if(($per_page * ($page)) > $elements)
		{
			$this->error('Pagination is out of range for result. Number of elements returned: ' . $elements . '.');
			return FALSE;	
		}
		else
		{
			$slice = array_slice($data, $per_page * $page, $per_page);
			$pagination = array('page' => $page + 1, 'total_pages' => $pages, 'elements_returned' => $elements, 'elements_per_page' => $per_page);
			
			$final['pagination'] = $pagination;
			foreach($slice AS $value)
			{
				$final[] = $value;
			}
			unset($slice);
			
			return $final;
		}
	}
	
	//Checks that an array is associatively keyed
	private function array_is_assoc(&$array)
	{
		foreach($array as $key => $value)
		{
			if(!is_numeric($key))
			{
				return TRUE;
			}
		}
		
		return FALSE;
	}
	
}