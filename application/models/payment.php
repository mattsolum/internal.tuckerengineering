<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends CI_Model {
	
	private $CI = NULL;
	
	public function __construct()
	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->model('Note');
	}
	
	public function get($id)
	{
		$id = preg_replace('/[^0-9]/', '', $id);
		
		$where = array('payment_id' => $id);
		
		$query = $this->CI->db->get_where('payments', $where);
		
		if($query->num_rows() > 0)
		{
			$row = $query->row(0);
			
			$payment = new StructPayment();
			
			$payment->id 			= $row->payment_id;
			$payment->client_id		= $row->client_id;
			
			$payment->tender		= $row->tender;
			$payment->number		= $row->number;
			$payment->amount		= $row->amount;
			
			$payment->date_added 	= $row->date_added;
			$payment->date_posted	= $row->date_posted;
			
			return $payment;
		}
		
		return FALSE;
	}
	
	public function get_client_last($client_id)
	{
		$this->db->select('*');
		$this->db->from('payments');
		$this->db->where('client_id', $client_id);
		$this->db->order_by('date_added', 'DESC');
		$this->db->limit(1);
		
		$query = $this->db->get();
		
		if($query->num_rows() > 0)
		{
			$row = $query->row(0);
			
			$payment = new StructPayment();
			
			$payment->id			= $row->payment_id;
			$payment->client_id		= $row->client_id;
			$payment->tender		= $row->tender;
			$payment->number		= $row->number;
			$payment->amount		= $row->amount;
			$payment->date_added	= $row->date_added;
			$payment->date_posted	= $row->date_posted;
			
			return $payment;
		}
		
		return FALSE;
	}
	
	//To apply a payment you must send a StructPayment Object.

	public function apply_by_job($payment, $job_id)
	{
		return $this->apply_by_jobs($payment, array($job_id));
	}
	
	public function apply_by_jobs($payment, $jobs)
	{
		$this->CI->db->trans_start();
		
		$this->apply_payment($payment, $jobs);
		
		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in Payment method apply_by_job: transaction failed.');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	public function apply_by_client($payment, $client_id)
	{
		$this->CI->db->trans_start();
		
		$jobs = $this->CI->Accounting->list_unpaid_jobs_by_client_id($client_id);

		$this->apply_payment($payment, $jobs);
		
		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in Payment method apply_by_client: transaction failed.');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	public function apply_by_invoice($payment, $invoice_id, $client_id)
	{
		$this->CI->db->trans_start();
		
		$jobs = array();
		$invoice = $this->CI->Invoice->get($invoice_id, $client_id);
		foreach($this->invoice->jobs AS $job)
		{
			$jobs[] = $job->id;
		}

		$this->apply_payment($payment, $jobs);
		
		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in Payment method apply_by_invoice: transaction failed.');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	public function commit($payment)
	{
		if(get_class($payment) != 'StructPayment' || !$payment->is_valid())
		{
			return FALSE;
		}

		$id = $this->exists($payment);

		if($id !== FALSE)
		{	
			return $id;
		}
		else
		{
			return $this->create($payment);
		}
	}

	public function create($payment)
	{
		$this->CI->db->trans_start();

		$data = array();
		$data['payment_id'] = $this->get_next_payment_id();
		$data['client_id']	= $payment->client_id;
		$data['tender']		= $payment->tender;
		$data['number']		= $payment->number;
		$data['amount']		= $payment->amount;
		$data['date_added']	= ($payment->date_added != null)?$payment->date_added:now();

		if($payment->date_posted != null)
		{
			$data['date_posted'] = $payment->date_posted;
		}

		$this->CI->db->insert('payments', $data);

		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in Payment method create: transaction failed.');
			return FALSE;
		}
		else
		{
			return $data['payment_id'];
		}
	}

	private function get_next_payment_id()
	{
		$this->CI->db->select('payment_id');
		$this->CI->db->from('payments');
		$this->CI->db->order_by('payment_id', 'DESC');
		$this->CI->db->limit(1);
		$query = $this->CI->db->get();

		if($query->num_rows > 0)
		{
			$row = $query->row(0);

			return $row->payment_id + 1;
		}

		return 0;
	}
	
	public function delete($payment_id)
	{
		$where = array();
		$where['payment_id'] = $payment_id;

		$this->CI->db->trans_start();

		$this->CI->db->delete('payments', $where);
		$this->CI->Accounting->delete_by_payment_id($payment_id);

		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in Payment method delete: transaction failed.');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	private function exists($payment)
	{
		$where = array();

		if($payment->id != NULL)
		{
			$where['payment_id'] = $payment->id;
		}
		elseif($payment->number != NULL)
		{
			$where['number'] 	= $payment->number;
			$where['client_id'] = $payment->client_id;
		}
		else
		{
			return FALSE;
		}

		$query = $this->CI->db->get_where('payments', $where);

		if($query->num_rows() > 0)
		{
			$row = $query->row(0);
			return $row->payment_id;
		}

		return FALSE;
	}
	
	public function apply_payment($payment, $jobs = NULL)
	{
		if(is_int($jobs))
			
		{
			$jobs = array($jobs);
		}

		$id = $this->commit($payment);
		$payment->id = $id;

		if($id == FALSE)
		{
			log_message('Error', 'Error in Payment method apply_payment: transaction failed.');
			return FALSE;
		}

		$balances = $this->CI->Accounting->get_balance_by_jobs($jobs);

		$amount_remaining = $this->payment_balance($id);

		foreach($jobs AS $job)
		{
			if($balances[$job] < 0 && $amount_remaining > 0)
			{
				$credit = new StructCredit();
				$credit->job_id		= $job;
				$credit->client_id 	= $payment->client_id;
				$credit->amount 	= min($amount_remaining, $balances[$job] * -1);
				$credit->payment	= $payment;
				$credit->item		= 'Payment';
				$credit->date_added = ($payment->date_added != null)?$payment->date_added:now();
				$this->CI->Accounting->create_ledger_item($credit);

				$note = new StructNote();

				$note->user->id = 0;
				$note->type = 'job';
				$note->type_id = $job;

				$note->text = $this->CI->User->get_name() . ' applied a payment of $' . number_format($credit->amount, 2) . '.';
				$this->CI->Note->commit($note);

				$this->CI->Event->trigger('job.dirty', $job);

				$amount_remaining -= $credit->amount;
			}

			if($amount_remaining == 0)
			{
				break;
			}
		}

		return TRUE;
	}

	public function get_batch_pay($date = NULL)
	{
		$this->load->model('Client');
		$this->load->model('Job');

		$batch = array();

		$where = array();
		$where['date_posted'] = $date;

		if($date == NULL)
		{
			$date = now();
		}

		$query = $this->CI->db->get_where('payments', $where);

		if($query->num_rows() > 0)
		{
			foreach($query->result() AS $payment)
			{
				$bpayment = new StructBatch();
				$bpayment->payment = $this->get($payment->payment_id);
				$bpayment->client = $this->Client->get($bpayment->payment->client_id);
				$bpayment->jobs = $this->Job->get_by_payment_id($bpayment->payment->id);
				$bpayment->date = $date;

				$batch[] = $bpayment;
			}
		}

		return $batch;
	}

	public function mark_batchpay()
	{	
		$data = array('date_posted' => now());

		$this->CI->db->trans_start();
		
		$this->db->where('date_posted', NULL);
		$this->db->update('payments', $data);

		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in Payment method mark_batchpay: transaction failed.');
			$this->CI->Messages->flash("An internal error occured, and the payments were not marked as posted.");
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	private function payment_balance($payment_id)
	{
		/*
		$this->CI->db->select_sum('ledger.amount', 'ledger_total');
		$this->CI->db->select('payments.amount', 'payment_total');
		$this->CI->db->join('payments', 'ledger.payment_id = payments.payment_id');
		$this->CI->db->where('ledger.payment_id', $payment_id);
		*/

		$q = 'SELECT SUM(ledger.amount) AS ledger_total, payments.amount AS payment_total FROM ledger JOIN payments ON ledger.payment_id = payments.payment_id WHERE ledger.payment_id = ' . $payment_id;


		$query = $this->CI->db->query($q);

		if($query->num_rows() > 0)
		{
			$row = $query->row(0);

			return $row->payment_total - $row->ledger_total;
		}

		return FALSE;
	}
}