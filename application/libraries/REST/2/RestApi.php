<?php defined('BASEPATH') OR exit('No direct script access allowed');

class RestApi
{
	public $auth;
	public $type;
	public $method;
	public $id;
	public $filetype;
	public $arguments;
	public $requestMethod;
	
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
		$request_method = $requestMethod = substr(preg_replace('/[^a-z]/','', strtolower($_SERVER['REQUEST_METHOD'])), 0, 6);
		
		//Assemble the API method.
		$this->method = trim(preg_replace('/[^a-zA-Z0-9_]/', '', $this->method) . '_' . $request_method);
		
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
		
		if(!file_exists($module_path . $this->type . '.php'))
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
		if(method_exists($this->active_class, $this->method . '_' . $this->id))
		{
			$this->method .= '_' . (string)$this->id;
			$this->id = '';
		}
		else
		{
			if(method_exists($this->active_class, $this->method))
			{
				$this->id = (string)str_replace('_', ' ', urldecode($this->id));
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
	
		switch ($this->filetype) {
			case 'xml':
				$this->output_xml($data, $error);
				break;
			case 'json':
				$this->output_json($data, $error);
				break;
			case 'html':
				$this->output_html($data, $error);
				break;
			case 'htm':
				$this->output_html($data, $error);
				break;
			case 'txt':
				$this->output_txt($data, $error);
				break;
			default:
				$this->output_xml($data, $error);
				break;
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
		
		if($per_page < 1)
		{
			$per_page = 1;
		}
		
		$elements = count($data);
		$pages = ceil(count($data) / $per_page);
		
		
		if($elements > ($per_page * $page))
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
		else
		{
			$this->error('Pagination is out of range for result. Number of elements returned: ' . $elements . '.');
			return FALSE;	
		}
	}
	
	private function output_json($data, $error)
	{
		$this->CI->output->set_content_type('application/json');
		
		$final = array();
		$final['result'] = ($error)?'error':'success';
		
		if(is_array($data) && isset($data['pagination']))
		{
			$final['pagination'] = $data['pagination'];
			unset($data['pagination']);
		}
		
		$final['data'] = $data;
		
		$this->CI->output->set_output(json_encode($final));
	}
	
	private function output_xml($data, $error)
	{
		$this->CI->output->set_content_type('text/xml');
		
		$xml = '<APIResponse><result>';
		$xml .= ($error)?'error</result>':'success</result>';
		
		foreach($data as $key => $value)
		{
			//If the root array is numerically indexed, 
			//which would be improper for XML,
			//use the request type as the tag name
			if(preg_match('/^[0-9]/', $key))
			{
				$xml .= $this->recurse_xml($this->type, $value );
			}
			else 
			{
				$xml .= $this->recurse_xml($key, $value);
			}
		}
		
		$xml .= '</APIResponse>';
		
		$this->CI->output->set_output($xml);
	}
	
	private function recurse_xml($key, $value)
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
					
					$xml .= $this->recurse_xml($key, $value2);
				}
				else
				{
					$xml .= $this->recurse_xml($key2, $value2);	
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
	
	private function output_html($data, $error)
	{
		$this->CI->output->set_content_type('text/html');
		
		if(is_array($data) && isset($data['pagination'])) unset($data['pagination']);
		
		if($error == FALSE && isset($this->arguments->view) && view_exists($this->arguments->view))
		{
			
			$this->CI->load->view($this->arguments->view, array($this->type => $data));
		}
		else
		{
			$html  = '<span class="result">';
			$html .= ($error)?'error</span>':'success</span>';
			
			$html .= '<ul>';
			
			foreach($data as $value)
			{
				$html .= $this->recurse_html($value);
			}
			
			$html .= '</ul>';
			
			$this->CI->output->set_output($html);	
		}
	}
	
	private function recurse_html($value)
	{
		$html = '<li>';
		$flag = false;
		
		if(is_array($value) || is_object($value))
		{
			$html .= '<ul>';
			
			foreach ($value as $value2) {
				$html .= $this->recurse_html($value2);
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
	
	private function output_txt($data, $error = FALSE)
	{
		$this->CI->output->set_content_type('text');
		
		$str = $this->recurse_txt($data);
		
		$str = str_replace('; ', ";\n", $str);
		$str = str_replace(':: ', "::\n", $str);
		
		$str = trim($str);
		
		$this->CI->output->set_output($str);
	}
	
	private function recurse_txt($data)
	{
		$str = '';
		if(is_array($data))
		{
			foreach ($data as $key => $value) {
				$str .= "\n" . $this->recurse_txt($value);
			}
		}
		else @$str .= (string)$data;
		
		return $str;
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