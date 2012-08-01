<?php

class Client extends Controller {

	function client()
	{
		parent::Controller();	
	}
	
	function index()
	{	
		$this->load->view('index');
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