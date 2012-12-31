<?php defined('BASEPATH') OR exit('No direct script access allowed');

class search_controller extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Navigation');
		$this->load->model('Search');

		$this->User->check_auth();
	}

	public function _remap($method)
	{
		$param_offset = 2;

		// Default to index
		if ( ! method_exists($this, $method))
		{
			// We need one more param
			$param_offset = 1;
			$method = 'index';
		}

		// Since all we get is $method, load up everything else in the URI
		$params = array_slice($this->uri->rsegment_array(), $param_offset);

		// Call the determined method with all params
		call_user_func_array(array($this, $method), $params);
	} 

	private function prepare($q, $type = '')
	{
		if($type != '' && substr($type, -1) != '/')
		{
			$type = $type . '/';
		}

		if($q == '' && $this->input->post('q') !== FALSE)
		{
			redirect('search/' . $type . urlencode($this->input->post('q')));
		}
		elseif($q != '')
		{
			return trim(urldecode($q));
		}
	}

	public function index($q = '')
	{
		$q = $this->prepare($q);

		$results = $this->Search->find($q);

		$results = array_slice($results, 0, 10);

		$this->load->view('search/index', array('q' => $q, 'results' => $results));
	}

	public function clients($q = '')
	{
		$q = $this->prepare($q, 'clients');

		if($q != '')
		{
			if(strpos($q, '[type:client]') === FALSE) 
			{
				$q .= ' [type:client]';
			}
		}

		$this->load->view('search/index', array('q' => $q, 'post_uri' => 'search/clients'));
	}

	public function jobs($q = '')
	{
		$q = $this->prepare($q, 'jobs');

		if($q != '')
		{
			if(strpos($q, '[type:job]') === FALSE) 
			{
				$q .= ' [type:job]';
			}
		}

		$this->load->view('search/index', array('q' => $q, 'post_uri' => 'search/jobs'));
	}

	public function properties($q = '')
	{
		$q = $this->prepare($q, 'properties');

		if($q != '')
		{
			if(strpos($q, '[type:property]') === FALSE) 
			{
				$q .= ' [type:property]';
			}
		}	

		$this->load->view('search/index', array('q' => $q, 'post_uri' => 'search/properties'));
	}
}