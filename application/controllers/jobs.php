<?php

class Jobs extends CI_Controller {
	
	function index()
	{	
		$this->load->view('index');
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