<?php

class Clients extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('Navigation');
		$this->load->model('Client');
		$this->load->model('Job');

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
		$client = $this->Client->get($client_id);
		$jobs = $this->Job->get_by_client_id($client_id);
		$num_jobs = $this->Job->number_of_jobs_for_client_id($client_id);


		if($client != FALSE)
		{
			if(is_numeric($client_id))
			{
				redirect(site_url('clients/' . url_title($client->name, '_', TRUE)));
			}

			$this->load->view('clients/view', array('client' => $client, 'jobs' => $jobs, 'num_jobs' => $num_jobs));
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
		$client = $this->Client->get($client_id);

		if($client != FALSE)
		{
			if($this->input->post('cl_name') != FALSE)
			{
				$client = $this->load_client_from_input($client);

				if($client->is_valid())
				{
					if($this->Client->commit($client))
					{
						redirect(site_url('clients/' . url_title($client->name, '_', true)));
					}
				}
				else
				{
					$this->load->view('clients/edit', array('client' => $client));
				}
			}
			else
			{
				if(is_numeric($client_id))
				{
					redirect(site_url('clients/edit/' . url_title($client->name, '_', TRUE)));
				}

				$this->load->view('clients/edit', array('client' => $client));
			}
		}
		else
		{
			$this->load->view('sections/404');
		}
	}

	public function create()
	{
		$client = new StructClient();

		if($this->input->post() == false)
		{
			$this->load->view('clients/create', array('client' => $client));
		}
		else
		{
			$this->load->model('Client');

			$post = $this->input->post();
			$cl2 = $this->Client->get($post['cl_name']);

			if($cl2 != FALSE)
			{
				$client = $cl2;
			}

			$client = $this->load_client_from_input($client);

			if($client->is_valid())
			{
				if($this->Client->commit($client))
				{
					redirect(site_url('clients/' . url_title($client->name, '_', true)));
				}
			}
			else
			{
				$this->load->view('clients/create', array('client' => $client));
			}
		}
	}

	private function load_client_from_input($client = NULL)
	{
		if($client == NULL)
		{
			$client = new StructClient();
		}

		$post = $this->input->post();

		$client->name 						= $post['cl_name'];
		$client->location->set_addr_1($post['cl_addr_1']);
		$client->location->subpremise 		= $post['cl_subpremise'];
		$client->location->locality			= $post['cl_locality'];
		$client->location->admin_level_1 	= $post['cl_admin_level_1'];
		$client->location->postal_code		= $post['cl_postal_code'];

		foreach($post['cl_contact'] AS $type => $list)
		{
			foreach($list AS $info)
			{
				if($info != '')
				{
					$client->add_contact_item($type, $info);
				}
			}
		}

		return $client;
	}

	public function apply_payment($client_id)
	{
		$client_id = str_replace('_', ' ', $client_id);
		$client = $this->Client->get($client_id);
		$jobs = $this->Job->get_by_client_id($client_id);

		if($this->input->post('tender') != false)
		{
			$payment = new StructPayment();
			$payment->client_id = $this->Client->get_id($client_id);

			$payment->tender = strtolower($this->input->post('tender'));
			$payment->number = strtolower($this->input->post('number'));
			$payment->amount = strtolower(preg_replace('/[^0-9\.]/', '', $this->input->post('amount')));

			if($payment->is_valid())
			{
				$jobs = $this->input->post('job');

				/*if($this->Payment->apply_by_job($payment, $jobs))
				{
					//Success!
				}/**/
			}
		}
		else
		{
			$this->load->view('clients/payment', array('jobs' => $jobs, 'client' => $client));
		}
	}
}

/* End of file clients.php */
/* Location: ./system/application/controllers/clients.php */