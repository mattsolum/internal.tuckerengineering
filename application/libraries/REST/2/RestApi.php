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