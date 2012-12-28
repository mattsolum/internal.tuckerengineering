<?php

class Jobs extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Navigation');

		$this->User->check_auth();
	}

	function index()
	{	
		$this->load->view('jobs/index');
	}
	
	/**
	 * View... view. Displays a single record
	 *
	 * @author Matthew Solum
	 * @param $job_id
	 * @return NULL
	 */
	public function view($job_id)
	{
		//code
	}
	
	/**
	 * Displays a single record for editing
	 *
	 * @author Matthew Solum
	 * @param $job_id
	 * @return null
	 */
	public function edit($job_id)
	{
		//code
		$this->load->view("newJob");
	}
}

/* End of file job.php */
/* Location: ./system/application/controllers/job.php */