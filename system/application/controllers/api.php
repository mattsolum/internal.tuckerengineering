<?php

class Api extends Controller {

	function api()
	{
		parent::Controller();
	}
	
	public function index()
	{	
		$this->error('No parameters provided');
	}
	
	public function v1($auth, $method)
	{	
		//The library handles everything from parsing to output.
		$this->load->library('REST/1/RestApi.php');
	}
	
	//Outputs an error message
	private function error($message)
	{
		$this->load->view('api/error', array('message' => $message . "\nFrom " . $this->uri->uri_string()));	
	}
	
	//Parse the URI into the important segments
	private function parse_request($request)
	{
		//URL format: {version}/{auth}/{method}/{:id}{.format}{?arguments}
		$parameters = array( 'version' => '', 'auth' => '', 'method' => '', 'id' => '', 'format' => '', 'arguments' => array());
		
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
				$parameters['arguments'][$nv[0]] = $nv[1];
			}
		}
		
		//Now extract the file type, if it exists
		if(strpos($lastSegment, '.xml') !== FALSE || strpos($lastSegment, '.json') !== FALSE)
		{
			$parameters['format'] = substr($lastSegment, strpos($lastSegment, '.') + 1);
			$lastSegment = str_replace('.' . $parameters['format'], '', $lastSegment);
		}
		
		//Is the last segment an Identifier?
		$idexists = 0;
		
		if(substr($lastSegment, 0, 1) == ':')
		{
			$parameters['id'] = urldecode(substr($lastSegment, 1));
			$idexists = 1;
		}
		
		//Pull out the version and auth token
		//Very easy
	 	$parameters['version'] = $this->uri->segment(2);
	 	$parameters['auth'] = $this->uri->segment(3);
	 	
	 	//Now lets extract the method
	 	if($this->uri->total_segments() > 3)
	 	{
	 		for($i = 4; $i <= $this->uri->total_segments() - $idexists; $i++)
	 		{
	 			$parameters['method'] .= '_' . $this->uri->segment($i);
	 		}
	 		
	 		$parameters['method'] = substr($parameters['method'], 1) . '_' . $requestMethod;
	 	}
		
		return $parameters;
	}
}

/* End of file api.php */
/* Location: ./system/application/controllers/api.php */