<?php defined('BASEPATH') OR exit('No direct script access allowed');

class RestApi
{
	private $auth;
	private $type;
	private $method;
	private $id;
	private $filetype;
	private $arguments;
	private $requestMethod;
	
	private $CI;
		
	public function __construct()
	{
		$this->CI =& get_instance();
		$this->arguments = new stdClass;
		
		$library_path = APPPATH . 'libraries/REST/2/';
		$module_path = $library_path . 'modules/';
		
		require_once($module_path . 'prototype.php');
		
		$this->_parse_request();
		$classname = ucfirst($this->type) . 'API';
		
		//Check that the request is validated
		//We will check again to make sure that the
		//user is allowed to perform the action they have requested.
		if($this->_auth())
		{
			//Make sure the proper file exists
			if(file_exists($module_path . $this->type . '.php'))
			{	
				//Make sure it is formatted properly
				if(class_exists($classname))
				{
					$this->active_class = new $classname();
					
					//Make sure the method we want to access exists
					if(method_exists($this->active_class, $this->method)
					{
						$method = $this->method;
						//NOW we can access the api method we want.
						$this->active_class->$method();
					}
				}	
			}
		}
	}
	
	private function _parse_request()
	{
		$this->CI->load->helper('url');
		$uri = str_ireplace('api/v2/', '', stristr(current_url(), 'api/v2/'));
		
		//Parse out the querystring, if it exists.
		if(strpos($uri, '?') !== FALSE)
		{
			$querystring = explode('?', substr(strstr($uri, '?'), 1));
			
			foreach ($querystring as $key => $value)
			{
				$this->arguments->$key = $value;
			}
			
			unset($querystring);
			$uri = str_replace(strstr($uri, '?'), '', $uri);
		}
		
		
		//Parse out the filetype, if it exists
		if(strpos($uri, '.') !== FALSE)
		{
			$this->filetype = substr(strstr($uri, '.'), 1);
			$uri = str_replace('.'.$this->filetype, '', $uri);
		}
		
		
		//Now assemble the method
		$segments = explode('/', $uri);
		
		$this->type = strtolower(array_shift($segments));
		
		$last_segment = array_pop($segments);
		
		if(substr($last_segment, 0, 1) == '!')
		{
			$this->id = substr($last_segment, 1);
			$this->method = implode('_', $segments);
		}
		else
		{
			$this->method = implode('_', $segments) . '_' . $last_segment;
		}
		
		//Now get the HTTP request method
		$request_method = $requestMethod = substr(preg_replace('/[^a-z]/','', strtolower($_SERVER['REQUEST_METHOD'])), 0, 6);
		
		//Assemble the API method.
		$this->method = preg_replace('/[^a-zA-Z0-9_]/', '', $this->method) . '_' . $request_method;
		
		if(substr($this->method, 0, 1) == '_')
		{
			$this->method = substr($this->method, 1);
		}
		
		//TODO: Auth
	}
	
	private function _auth()
	{
		//Todo: Fill in actual auth procedure.
		return TRUE;
	}
	
	private function _error()
	{
		
	}
	
	private function _output()
	{
	
	}
	
	private function _output_json()
	{
		
	}
	
	private function _output_xml()
	{
	
	}
	
	private function _recurse_xml()
	{
	
	}
	
	private function _output_html()
	{
		
	}
	
	private function _recurse_html()
	{
		
	}
	
}