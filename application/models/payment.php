<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends CI_Model {
	
	private $CI = NULL;
	
	public function __construct()
	{
		parent::__construct();
		$this->CI =& get_instance();	
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
		$data['date_added']	= now();

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

	private function payment_exists($payment)
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

		if($id == FALSE)
		{
			log_message('Error', 'Error in Payment method apply_payment: transaction failed.');
			return FALSE;
		}

		$balances = $this->CI->accounting->get_balance_by_jobs($jobs);

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

				$this->CI->Accounting->create_ledger_item($credit);

				$amount_remaining -= $credit->amount;
			}

			if($amount_remaining == 0)
			{
				break;
			}
		}

		return TRUE;
	}

	private function payment_balance($payment_id)
	{
		$this->CI->db->select_sum('ledger.amount', 'ledger_total');
		$this->CI->db->select('payments.amount', 'payment_total');
		$this->CI->db->from('ledger');
		$this->CI->db->join('payments', 'ledger.payment_id = payments.payment_id');
		$this->CI->db->where('ledger.payment_id' => $payment->id);

		$query = $this->CI->db->get('ledger');

		if($query->num_rows() > 0)
		{
			$row = $query->row(0);

			return $row->payment_total - $row->ledger_total;
		}

		return FALSE;
	}
}