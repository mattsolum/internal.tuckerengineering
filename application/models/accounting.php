<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Accounting extends CI_Model
{
	private $CI = NULL;
	
	public function __construct()
	{
		parent::__construct();
		$this->CI =& get_instance();
		
		$this->CI->load->model('Payment');
	}
	
	//Returns TRUE on success.
	public function commit($accounting)
	{
		if(!$accounting->is_valid())
		{
			log_message('error', 'Error in model Accounting method commit: accounting is invalid.');
			return FALSE;
		}
		
		$this->CI->db->trans_start();
		
		//Get rid of 'Positive Balance' items
		//They will be recreated later.
		//If an existing job is edited and the client is changed
		//we need to make sure that the positive balance 
		//from the previous client does not get applied.
		//Yes, I have trust issues. Users are always using me.
		$date_added = now();
		foreach($accounting->credits AS $key => $credit)
		{
			if($item->item = 'Positive Balance')
			{
				$date_added = $credit->date_added;
				unset($accounting->credits[$key]);
			}
		}
		
		if(isset($accounting->debits[0]) && $this->job_exists($accounting->debits[0]->job_id))
		{
			$this->delete_by_job($accounting->debits[0]->job_id);
		}
		
		//Let's check their balance!
		$bal = 0;

		if(isset($accounting->debits[0]))
		{
			$bal = $this->get_balance_by_client($accounting->debits[0]->client_id);
		}

		//Look, there is money in the account!
		if($bal > 0)
		{
			//Make a NEW positive balance credit
			$positive_balance = new StructCredit();
			
			$positive_balance->client_id 	= $accounting->debits[0]->client_id;
			$positive_balance->job_id 		= $accounting->debits[0]->job_id;
			$positive_balance->item			= 'Positive Balance';
			$positive_balance->date_added	= $date_added;
			$positive_balance->date_updated	= now();
			
			//Use whatever is lower: the positive balance or the total for this job.
			$positive_balance->amount		= min($bal, $accounting->total() * -1);
			
			//The payment being applied is assumed to be 
			//the last payment we received from this client.
			$positive_balance->payment 		= $this->CI->Payment->get_client_last($positive_balance->client_id);
			
			//add it to the credits
			$accounting->credits[] = $positive_balance;
		}
		
		//Put everything in one array so it can be looped through all at once
		$all = array_merge($accounting->debits, $accounting->credits);
		
		foreach($all as $item)
		{
			$this->create_ledger_item($item);
		}
		
		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('error', 'Error in model Accounting committing object.');
			return FALSE;
		}
		else return TRUE;
	}
	
	public function create_ledger_item($ledger)
	{
		if(!$ledger->is_valid())
		{
			log_message('error', 'Model Accounting method create_ledger_item: ledger is not valid.');
			return FALSE;
		}
		
		$this->CI->db->trans_start();
		
		$data = array();
		
		if(isset($ledger->ledger_id))
		{
			$data['date_added'] = $ledger->date_added;
			$data['ledger_id']	= $ledger->ledger_id;
		}
		else
		{
			$data['date_added'] = ($ledger->date_added != '')?$ledger->date_added:now();
		}
		
		$data['date_updated'] = now();
		
		if(isset($ledger->payment))
		{
			$data['payment_id']	= $ledger->payment->id;
		}
		
		$data['job_id']		= $ledger->job_id;
		$data['client_id']	= $ledger->client_id;
		$data['item']		= $ledger->item;
		$data['amount']		= $ledger->amount;
		
		$this->CI->db->insert('ledger', $data);
		
		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('error', 'Error in model Accounting method create_ledger_item.');
			return FALSE;
		}
		else return TRUE;
	}
	
	public function job_balance($job_id)
	{
		$accounting = $this->get_by_job($job_id);
		
		return $accounting->credits->total() + $accounting->debits->total();
	}
	
	public function get_by_job($job_id)
	{
		$accounting = new StructAccounting();
		$credits = FALSE;
		$debits = FALSE; 

		$query = $this->CI->db->get_where('ledger', array('job_id' => $job_id));

		if($query->num_rows() > 0)
		{
			foreach($query->result() AS $row)
			{

				if($row->amount > 0)
				{
					//Is a credit
					$credit = new StructCredit();
				
					$credit->client_id 		= $row->client_id;
					$credit->job_id			= $row->job_id;
					$credit->ledger_id		= $row->ledger_id;
					
					$credit->item			= $row->item;
					
					$credit->date_added 	= $row->date_added;
					$credit->date_updated	= $row->date_updated;
					
					$credit->amount			= $row->amount;
					
					if($row->payment_id != NULL)
					{
						$credit->payment = $this->CI->Payment->get($row->payment_id);
					}
					
					$credits[] = $credit;
					
					unset($credit);
				}
				else
				{
					//Is a debit
					$ledger = new StructDebit();
				
					$ledger->ledger_id 	= $row->ledger_id;
					$ledger->client_id 	= $row->client_id;
					$ledger->job_id		= $job_id;
					
					$ledger->item		= $row->item;
					$ledger->amount		= $row->amount;
					
					$ledger->date_added	= $row->date_added;
					
					$debits[] = $ledger;
					
					unset($ledger);
				}
			}
		}
		
		if($credits !== FALSE)
			$accounting->credits	= $credits;
			
		if($debits !== FALSE)
			$accounting->debits		= $debits;
		
		return $accounting;
	}
	
	private function get_credits_by_job($job_id)
	{
		$this->CI->db->select('*');
		$this->CI->db->from('ledger');
		
		$this->CI->db->where('job_id', $job_id);
		$this->CI->db->where('amount >', 0);
		
		$query = $this->CI->db->get();
		
		if($query->num_rows() > 0)
		{
			$result = array();
			
			foreach($query->result() AS $row)
			{
				$credit = new StructCredit();
				
				$credit->client_id 		= $row->client_id;
				$credit->job_id			= $row->job_id;
				$credit->ledger_id		= $row->ledger_id;
				
				$credit->item			= $row->item;
				
				$credit->date_added 	= $row->date_added;
				$credit->date_updated	= $row->date_updated;
				
				$credit->amount			= $row->amount;
				
				if($row->payment_id != NULL)
				{
					$credit->payment = $this->CI->Payment->get($row->payment_id);
				}
				
				$result[] = $credit;
				
				unset($credit);
			}
			
			return $result;
		} 
		
		return FALSE;
	}
	
	private function get_debits_by_job($job_id)
	{
		$where = array('amount <' => '0', 'job_id' => $job_id);
		
		$query = $this->CI->db->get_where('ledger', $where);
		
		if($query->num_rows() > 0)
		{
			$result = array();
			
			foreach($query->result() AS $row)
			{
				$ledger = new StructDebit();
				
				$ledger->ledger_id 	= $row->ledger_id;
				$ledger->client_id 	= $row->client_id;
				$ledger->job_id		= $job_id;
				
				$ledger->item		= $row->item;
				$ledger->amount		= $row->amount;
				
				$ledger->date_added	= $row->date_added;
				
				$result[] = $ledger;
				
				unset($ledger);
			}
			
			return $result;
		}
		
		return FALSE;
	}
	
	//TODO: Make this not take three freaking queries per client.
	public function get_balance_by_client($client_id)
	{
		if(!preg_match('/^[0-9]+$/', $client_id))
		{
			log_message('Error', 'Model Accounting method get_balance_by_client: client_id is invalid.');
			return FALSE;
		}
		
		//Three queries because MySQL does not support full outer joins
		//First the balance of all jobs the client is attached to
		$balance_by_job = $this->CI->db->query('SELECT client_id, SUM(amount) AS balance FROM ledger WHERE client_id = ' . $client_id . ' AND amount < 0 GROUP BY client_id');
		
		//Second summation of all payments
		$payments = $this->CI->db->query('SELECT client_id, sum(amount) AS payments FROM payments WHERE client_id = ' . $client_id . ' GROUP BY client_id');
		
		//Finally summation of payments on jobs the client is not listed on
		$other_payments = $this->CI->db->query('SELECT ledger.client_id AS client_id, SUM(ledger.amount) AS other_payments FROM ledger JOIN jobs ON ledger.job_id = jobs.job_id WHERE jobs.client_id != ledger.client_id AND jobs.client_id = ' . $client_id . ' GROUP BY ledger.client_id');
		
		$bbj 	= 0;
		$p		= 0;
		$op		= 0;
		
		if($balance_by_job->num_rows() > 0)
		{
			$row 	= $balance_by_job->row(0);
			$bbj 	= $row->balance;
		}
		
		if($payments->num_rows() > 0)
		{
			$row 	= $payments->row(0);
			$p 		= $row->payments;
		}
		
		if($other_payments->num_rows() > 0)
		{
			$row	= $other_payments->row(0);
			$op		= $row->other_payments;
		}
		
		//Balance by jobs will always be negative,
		//so add it to payments which will always be positive
		//Then subtract other payments because it will always be postive
		return $p + $bbj - $op;
	}

	public function get_balance_by_jobs($jobs)
	{
		if(is_int($jobs))
		{
			$jobs = array($jobs);
		}

		$this->CI->db->select('job_id');
		$this->CI->db->select_sum('amount', 'balance');
		$this->CI->db->from('ledger');

		foreach ($jobs as $job)
		{
			$this->CI->db->or_where('job_id', $job);
		}

		$this->CI->db->group_by('job_id');
		$this->CI->db->order_by('job_id', 'DESC');

		$query = $this->CI->db->get();

		if($query->num_rows() > 0)
		{
			$result = array();

			foreach($query->result() AS $row)
			{
				$result[$row->job_id] = $row->balance;
			}

			return $result;
		}

		return FALSE;
	}
	
	public function delete_by_job($job_id)
	{
		$this->CI->db->trans_start();
		
		$where = array('job_id' => $job_id);
		$query = $this->CI->db->delete('ledger', $where);
		
		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('error', 'Error in model Accounting method delete_by_job.');
			return FALSE;
		}
		else return TRUE;
	}
	
	private function job_exists($job_id)
	{
		$where = array('job_id' => $job_id);
		
		$query = $this->CI->db->get_where('ledger', $where);
		
		if($query->num_rows() > 0)
		{
			return TRUE;
		}
		
		return FALSE;
	}

	/**
	 * Returns an array of job ID's for jobs that have a negative balance and belong to a given client.
	 * 
	 * @param  int $client_id
	 * @return array
	 */
	public function list_unpaid_jobs_by_client_id($client_id)
	{
		$this->CI->db->select('jobs.job_id');
		$this->CI->db->from('jobs');
		$this->CI->db->join('ledger', 'jobs.job_id = ledger.job_id');
		$this->CI->db->where('jobs.client_id', $client_id);
		$this->CI->db->where('SUM(ledger.amount) < 0');

		$query = $this->CI->db->get();
		if($query->num_rows() > 0)
		{
			$result = array();
			foreach($query->result() AS $row)
			{
				$result[] = $row->job_id;
			}

			return $result;
		}

		return array();
	}

	public function list_jobs_by_payment_id($payment_id)
	{
		$this->CI->db->select('job_id');
		$this->CI->db->from('ledger');
		$this->CI->db->where('payment_id', $payment_id);

		$query = $this->CI->db->get();

		if($query->num_rows() > 0)
		{
			$result = array();
			foreach($this->result() AS $row)
			{
				$result[] = $row->job_id;
			}

			return $result;
		}

		return array();
	}

	public function delete_by_payment_id($payment_id)
	{
		$where = array();
		$where['payment_id'] = $payment_id;

		$this->CI->db->trans_start();

		$this->CI->db->delete('ledger', $where);

		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('error', 'Error in model Accounting method delete_by_payment_id: transaction failed.');
			return FALSE;
		}
		else return TRUE;
	}
}