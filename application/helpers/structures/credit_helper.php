<?php defined('BASEPATH') OR exit('No direct script access allowed');

class StructCredit
{
	//LOTS OF ID's
	//Maybe we could get away with fewer?
	public $client_id		= NULL;
	public $job_id			= NULL;
	public $ledger_id		= NULL;
	
	public $payment			= NULL;
	
	public $item			= NULL;
	
	public $date_added		= NULL;
	public $date_updated	= NULL;
	
	public $amount			= 0.0;
	
	public function __construct($json = NULL)
	{
		if($json != NULL)
		{
			$this->set_from_json($json);
		}
	}
	
	public function set_from_json($json)
	{	
		if(is_string($json))
		{
			$json = json_decode($json);
		}
		
		if($json != NULL)
		{
			$this->client_id	= $json->client_id;
			$this->job_id		= $json->job_id;
			
			$this->item			= $json->item;
			$this->amount		= $json->amount;
			
			if(isset($json->payment))
			{
				$this->payment = new StructPayment($json->payment);
			}
			
			$this->date_added	= $json->date_added;
			$this->date_updated	= $json->date_updated;
		}
	}
	
	
	public function is_valid()
	{
		if(	$this->job_id == NULL || $this->client_id == NULL || $this->amount == 0)
		{
			return FALSE;
		}
		
		if($this->payment != NULL)
		{
			if(($this->payment->tender == 'credit' || $this->payment->tender == 'check') && $this->number == '')
			{
				return FALSE;
			}
		}
		//I just concatinate them. Probably not the fastest method, but the least
		//number of lines.
		if(!preg_match('/^[0-9]+$/', $this->client_id . $this->job_id))
		{
			return FALSE;
		}
		
		if($this->amount < 0)
		{
			$this->amount = $this->amount * -1;
		}
		
		return TRUE;
	}
	
	public function amount()
	{
		return $amount;
	}
	
	public function __toString()
	{
		$string  = $this->item;
		
		if($this->payment != NULL)
		{
			$string .= ' ' . $this->payment->tender;
			$string .= ($this->payment->number != 0)?' *' . $this->payment->number:'';
		}
		
		$string .= ' $' . number_format($this->amount, 2);
		
		return $string;
	}
}