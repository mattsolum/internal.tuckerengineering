<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Payments extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Navigation');
		$this->load->model('Payment');
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

	public function batch($date = null)
	{
		$this->load->view('payments/batchpay');
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