<?php

class Clients extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('Navigation');
		$this->load->model('client');

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

	public function index($client_id = NULL)
	{	
		if($client_id != NULL)
		{
			$this->view($client_id);
		}
		else
		{
			$this->load->view('clients/index');
		}
	}
	
	/**
	 * View... view. Displays a single record
	 *
	 * @author Matthew Solum
	 * @param $client_id
	 * @return NULL
	 */
	public function view($client_id)
	{
		$client_id = str_replace('_', ' ', $client_id);
		$client = $this->client->get($client_id);

		if($client != FALSE)
		{
			if(is_numeric($client_id))
			{
				redirect(site_url('clients/' . url_title($client->name, '_', TRUE)));
			}

			$this->load->view('clients/view', array('client' => $client));
		}
		else
		{
			$this->load->view('sections/404');
		}
	}
	
	/**
	 * Displays a single record for editing
	 *
	 * @author Matthew Solum
	 * @param $client_id
	 * @return null
	 */
	public function edit($client_id)
	{
		$client_id = str_replace('_', ' ', $client_id);
		$client = $this->client->get($client_id);

		if($client != FALSE)
		{
			if(is_numeric($client_id))
			{
				redirect(site_url('clients/edit/' . url_title($client->name, '_', TRUE)));
			}

			$this->load->view('clients/edit', array('client' => $client));
		}
		else
		{
			$this->load->view('sections/404');
		}
	}

	public function create()
	{
		$client = new StructClient;
		$this->load->view('clients/create', array('client' => $client));
	}
}

/* End of file client.php */
/* Location: ./system/application/controllers/client.php */