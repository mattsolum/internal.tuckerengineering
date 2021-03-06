<?php

class Jobs extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Navigation');
		$this->load->model('Job');
		$this->load->model('Client');

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

	function index($job_id = NULL)
	{	
		if($job_id == NULL)
		{
			$this->load->view('jobs/index');
		}
		else
		{
			$job = $this->Job->get($job_id);
			$this->load->view('jobs/view', array('job' => $job));
		}
	}

	function invoice($job_id = NULL)
	{
		$this->load->model('Invoice');

		if($job_id == NULL)
		{
			$this->Messages->flash('A job ID was not provided, the invoice could not be located.');
			$this->load->view('jobs/index');
		}
		else
		{
			$job = $this->Job->get($job_id);
			
			$invoice = new StructInvoice();

			$invoice->client = $job->client;
			$invoice->jobs[] = $job;

			$invoice_id = $this->Invoice->commit($invoice);

			if($invoice_id != FALSE)
			{
				redirect('invoices/' . $job->client->id . '-' . $invoice_id);
			}
			else
			{
				$this->Messages->flash('I am sorry, something went wrong with the invoice.', 'error');
				redirect('jobs/' . $job->id);
			}
		}
	}

	function apply_payment($job_id)
	{
		$job = $this->Job->get($job_id);

		$this->load->view('clients/payment', array('jobs' => array($job), 'client' => $job->client));
	}
	
	/**
	 * Displays a single record for editing
	 *
	 * @author Matthew Solum
	 * @param $job_id
	 * @return null
	 */
	public function edit($job_id, $page = 'job')
	{
		//code
		$job = $this->Job->get($job_id);
		if($job == FALSE)
		{
			$this->session->set_userdata('job_edit', FALSE);
			$this->load->view('404');
		}
		else
		{
			if($this->session->userdata('job_edit') != FALSE)
			{
				$job->set_from_json(base64_decode($this->session->userdata('job_edit')));
			}

			if($page == 'job')
			{
				$this->load->view("jobs/create_1", array('job' => $job));
			}
			else if($page == 'client')
			{

				if($this->input->post() != FALSE)
				{
					//Information submitted from last page
					$job->location = $this->set_property();
					$job->accounting->debits = $this->set_debits();

					if($this->input->post('jb_note') != '')
					{
						$job->add_note($this->User->get_user_id(), $this->input->post('jb_note'));
					}
				}

				$this->session->set_userdata('job_edit', base64_encode(json_encode($job)));

				if($job->location->is_valid() && $job->accounting->is_valid(FALSE))
				{
					$this->load->view('jobs/create_2', array('job' => $job));
				}
				else
				{
					redirect(site_url('jobs/create/client'));
				}
			}
			else if($page == 'requester')
			{

				if($this->input->post() != FALSE)
				{
					//Information submitted from last page
					$job->client = $this->set_client();
				}

				$this->session->set_userdata('job_edit', base64_encode(json_encode($job)));

				if($job->client->is_valid())
				{
					$this->load->view("jobs/create_3", array('job' => $job));
				}
				else
				{
					redirect(site_url('jobs/create/requester'));
				}
			}
			else if($page == 'final')
			{
				if($this->input->post() != FALSE)
				{
					//Information submitted from last page
					if($this->input->post('cl_is_requster') == TRUE)
					{
						$job->requester = $job->client;
					}
					else
					{
						$job->requester = $this->set_client();
					}
				}

				$this->session->set_userdata('job_edit', base64_encode(json_encode($job)));

				if($job->is_valid(FALSE))
				{
					$result = $this->CI->Job->commit($job);

					if($result !== FALSE)
					{
						$this->CI->Messages->flash('The job record was successfully added.', 'success');
						$this->session->set_userdata('job_edit', FALSE);
						redirect(site_url('jobs/' . $result));
					}
					else
					{
						$this->CI->Messages->flash('There was an internal error while saving the job record, please try again.', 'error');
						redirect(site_url('jobs/create/job'));
					}
				}
				else
				{
					if(!$job->client->is_valid())
					{
						$this->CI->Messages->flash('The given client information was incomplete. Please review the record below.', 'error');
						redirect(site_url('jobs/create/client'));
					}
					elseif(!$job->requester->is_valid())
					{
						$this->CI->Messages->flash('The given requester information was incomplete. Please review the record below.', 'error');
						redirect(site_url('jobs/create/requester'));
					}
					else
					{
						$this->CI->Messages->flash('The given job information was incomplete. Please review the record below.', 'error');
						redirect(site_url('jobs/create/job'));
					}
				}
			}
		}
	}

	/**
	 * Displays a form for creating a new job
	 * 
	 * @return null
	 */
	public function create($page = 'job')
	{
		$job = new StructJob();
		if($this->session->userdata('job') != FALSE)
		{
			$job->set_from_json(base64_decode($this->session->userdata('job')));
		}

		if($page == 'start_over')
		{
			$this->session->set_userdata('job', FALSE);
			redirect(site_url('jobs/create/job'));
		}
		else if($page == 'job')
		{
			$this->load->view("jobs/job", array('job' => $job));
		}
		else if($page == 'client')
		{

			if($this->input->post() != FALSE)
			{
				//Information submitted from last page
				$job->location = $this->set_property();
				$job->accounting->debits = $this->set_debits();

				if($this->input->post('jb_note') != '')
				{
					$job->add_note($this->User->get_user_id(), $this->input->post('jb_note'));
				}
			}

			$this->session->set_userdata('job', base64_encode(json_encode($job)));

			if($job->location->is_valid() && $job->accounting->is_valid(FALSE))
			{
				$this->load->view('jobs/client', array('job' => $job));
			}
			else
			{
				$this->Messages->flash('The address or debits entered were invalid, please check them and resubmit.');
				redirect(site_url('jobs/create/job'));
			}
		}
		else if($page == 'requester')
		{

			if($this->input->post() != FALSE)
			{
				//Information submitted from last page
				$job->client = $this->set_client();
			}

			$this->session->set_userdata('job', base64_encode(json_encode($job)));

			if($job->client->is_valid())
			{
				$this->load->view("jobs/requester", array('job' => $job));
			}
			else
			{
				redirect(site_url('jobs/create/client'));
			}
		}
		else if($page == 'final')
		{
			if($this->input->post() != FALSE)
			{
				//Information submitted from last page
				if($this->input->post('cl_is_requster') == TRUE)
				{
					$job->requester = $job->client;
				}
				else
				{
					$job->requester = $this->set_client();
				}
			}

			$this->session->set_userdata('job', base64_encode(json_encode($job)));

			if($job->is_valid(FALSE))
			{
				$result = $this->Job->commit($job);

				if($result !== FALSE)
				{
					$this->Messages->flash('The job record was successfully added.', 'success');
					$this->session->set_userdata('job', FALSE);
					redirect(site_url('jobs/' . $result));
				}
				else
				{
					$this->Messages->flash('There was an internal error while saving the job record, please try again.', 'error');
					redirect(site_url('jobs/create/job'));
				}
			}
			else
			{
				if(!$job->client->is_valid())
				{
					$this->Messages->flash('The given client information was incomplete. Please review the record below.', 'error');
					redirect(site_url('jobs/create/client'));
				}
				elseif(!$job->requester->is_valid())
				{
					$this->Messages->flash('The given requester information was incomplete. Please review the record below.', 'error');
					redirect(site_url('jobs/create/requester'));
				}
				else
				{
					$this->Messages->flash('The given job information was incomplete. Please review the record below.', 'error');
					redirect(site_url('jobs/create/job'));
				}
			}
		}
	}

	private function set_debits()
	{
		$post = $this->input->post();
		$debits = array();

		for($i = 0; $i < count($post['jb_item']); $i++)
		{
			if($post['jb_item'][$i] != '')
			{
				$debit = new StructDebit();
				$debit->item = trim(preg_replace('/[^a-zA-Z0-9 -]/', '', $post['jb_item'][$i]));
				$debit->amount = trim(preg_replace('/[^0-9\.]/', '', $post['jb_item_amount'][$i]));

				$debits[] = $debit;
			}
		}

		return $debits;
	}

	private function set_property()
	{
		$location = new StructProperty();

		$post = $this->input->post();

		if($post['jb_addr_1'] != '')
		{
			$location->set_addr_1(trim(preg_replace('/[^a-zA-Z0-9 \.#-]/', '', $post['jb_addr_1'])));
		}

		$location->subpremise 		= trim(preg_replace('/[^a-zA-Z0-9 \.#-]/', '', $post['jb_subpremise']));
		$location->locality			= trim(preg_replace('/[^a-zA-Z0-9 \'-]/', '', $post['jb_locality']));
		$location->admin_level_1 	= trim(preg_replace('/[^a-zA-Z0-9 \'-]/', '', $post['jb_admin_level_1']));
		$location->postal_code		= trim(preg_replace('/[^a-zA-Z0-9 -]/', '', $post['jb_postal_code']));

		if($post['prop_foundation'] != '')
		{
			$location->info['foundation_type'] = trim(preg_replace('/[^a-zA-Z0-9 -]/', '', $post['prop_foundation']));
		}
		
		if($post['prop_type'] != '')
		{
			$location->info['property_type'] = trim(preg_replace('/[^a-zA-Z0-9 -]/', '', $post['prop_type']));
		}

		return $location;
	}

	private function set_client()
	{
		$this->load->model('Client');

		$client = new StructClient();

		$post = $this->input->post();
		$cl2 = $this->Client->get($post['cl_name']);

		if($cl2 != FALSE)
		{
			$client = $cl2;
		}

		return $this->load_client_from_input($client);
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
}

/* End of file job.php */
/* Location: ./system/application/controllers/job.php */