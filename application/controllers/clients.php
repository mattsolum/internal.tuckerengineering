<?php

class Clients extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('Navigation');

		$this->User->check_auth();
	}

	public function index()
	{	
		$this->load->view('clients/index');
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
		//code
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
		//code
	}
}

/* End of file client.php */
/* Location: ./system/application/controllers/client.php */