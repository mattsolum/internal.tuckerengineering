<?php

class Invoices extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Navigation');
		$this->load->model('Invoice');

		$this->User->check_auth();
	}

	public function _remap($method)
	{
		$param_offset = 2;

		if($method == 'print')
		{
			$method = 'print_invoice';
		}

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

	public function apply_payment($invoice_id)
	{
		$invoice = $this->Invoice->get($invoice_id);

		$data = array();
		$data['invoice'] = $invoice;

		if($this->input->post('tender') != false)
		{
			$payment = new StructPayment();
			$payment->client_id = $invoice->client->id;

			$payment->tender = strtolower($this->input->post('tender'));
			$payment->number = strtolower($this->input->post('number'));
			$payment->amount = strtolower(preg_replace('/[^0-9\.]/', '', $this->input->post('amount')));

			if($payment->is_valid())
			{
				$jobs = $this->input->post('job');

				if($this->Payment->apply_by_job($payment, $jobs))
				{
					$this->Messages->flash('Payment for $' . number_format($payment->amount, 2) . ' successfully applied.', 'success');
					redirect('invoices/' . $invoice_id);
				}/**/
			}
			else
			{
				$this->Messages->flash('Something was wrong with the submitted payment information.', 'error');
				$this->load->view('invoices/payment', $data);
			}
		}
		else
		{
			$this->load->view('invoices/payment', $data);
		}
	}

	public function index($invoice_id = NULL)
	{	
		if($invoice_id == NULL)
		{
			$this->load->view('invoices/index');
		}
		else
		{
			$this->view($invoice_id);
		}
	}
	
	/**
	 * View... view. Displays a single record
	 *
	 * @author Matthew Solum
	 * @param $invoice_id
	 * @return NULL
	 */
	public function view($invoice_id)
	{
		//code
		$invoice = $this->Invoice->get($invoice_id);

		$this->load->view('invoices/view', array('invoice' => $invoice));
	}
	
	/**
	 * Displays a single record for editing
	 *
	 * @author Matthew Solum
	 * @param $invoice_id
	 * @return null
	 */
	public function edit($invoice_id)
	{
		//code
	}

	public function publish($invoice_id)
	{
		$invoice = $this->Invoice->get($invoice_id);

		$this->load->view('invoices/publish', array('invoice' => $invoice));
	}

	public function print_invoice($invoice_id)
	{
		$invoice = $this->Invoice->get($invoice_id);

		$this->load->view('invoices/print', array('invoice' => $invoice));

	}

	public function batch($date = NULL)
	{
		if($date == NULL)
		{
			
		}
	}
	
}

/* End of file invoice.php */
/* Location: ./system/application/controllers/invoice.php */