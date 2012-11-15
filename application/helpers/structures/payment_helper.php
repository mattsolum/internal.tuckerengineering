<?php defined('BASEPATH') OR exit('No direct script access allowed');

class StructPayment
{
	//LOTS OF ID's
	//Maybe we could get away with fewer?
	public $client_id;
	public $job_id;
	public $payment_id;
	public $ledger_id;
	
	public $type;		//Credit Card, Check, Cash, etc.
	public $number;		//Check number, credit card number, etc. Not needed for cash.
	
	public $date;
	public $date_posted;
	
	public $amount			= 0.0;
	
	public function set_from_json($json)
	{
		if(is_string($json))
		{
			$json = json_decode($json);
		}
		
		$this->client_id	=	$json->client_id;
		$this->job_id		=	$json->job_id;
		$this->payment_id	=	$json->payment_id;
		$this->ledger_id	=	$json->ledger_id;
		
		$this->type			=	$json->type;
		$this->number		=	$json->number;
		
		$this->date			=	$json->date;
		$this->date_posted	=	$json->date_posted;
		
		$this->amount		=	$json->amount;
	}
	
	
	public function is_valid()
	{
		if(	!$this->client_id || !$this->type || $this->amount == 0)
		{
			return FALSE;
		}
		
		if( ($this->type == 'credit' || $this->type == 'check') && $this->number == '')
		{
			return FALSE;
		}
		
		return TRUE;
	}
	
	public function amount()
	{
		return $amount;
	}
	
	public function stringify()
	{
		$first = $this->ledger_id . ' ' . $this->type;
		$first .= ($this->number != '')?' *' . $this->number:'';
		$last = number_format($this->amount, 2);
		
		for($i = 34 - strlen($first); $i > 0; $i--)
		{
			$first .= ' ';
		}
		
		for($i = 8 - strlen($last); $i > 0; $i--)
		{
			$last = ' ' . $last;
		}
		
		return $first . ' = ' . $last;
		
	}
	
	public function __toString()
	{
		return (string)$this->amount;
	}
}