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
	public $number;	//Check number, credit card number, etc. Not needed for cash.
	
	public $date;
	public $date_posted;
	
	public $amount			= 0.0;
	
	
	public function is_valid()
	{
		if(	!$this->client_id || !$this->type || $this->amount == 0)
		{
			return FALSE;
		}
		
		return TRUE;
	}
	
	public function __toString()
	{
		$txt = '';
		
		$txt .= $this->type;
		$txt .= ($this->number != '')?' #' . $this->number:'';
		$txt .= ' $' . number_format($this->amount, 2); 
	}
}