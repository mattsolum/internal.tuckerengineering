<?php

class Payment extends CI_Controller {
	
	function index()
	{	
		$this->load->view('index');
	}
	
	/**
	 * Apply payment for a client. Applies payment to all outstanding jobs from oldest to newest.
	 *
	 * @author Matthew Solum
	 * @param $client_id, $type
	 * @return NULL
	 */
	public function client($client_id, $type)
	{
		//code
	}
	
	/**
	 * Apply payment for an invoice. Applies payment to a single invoice, gives notice if remainder.
	 *
	 * @author Matthew Solum
	 * @param $invoice_id
	 * @return NULL
	 */
	public function invoice($invoice_id, $type)
	{
		//code
	}
	
	/**
	 * Apply payment for a job. Applies payment to a single job, gives notice if remainder.
	 *
	 * @author your name
	 * @param $job_id
	 * @return NULL
	 */
	public function job($job_id, $type)
	{
		//code
	}
	
	/**
	 * Applies payment(s) given a post array
	 *
	 * @author Matthew Solum
	 * @param 
	 * @return NULL
	 */
	public function apply()
	{
		//code
	}
	
	/**
	 * Provides a printable list of all undeposited payments.
	 *
	 * @author Matthew Solum
	 * @param 
	 * @return NULL
	 */
	public function post()
	{
		//code
	}
}

/* End of file job.php */
/* Location: ./system/application/controllers/job.php */