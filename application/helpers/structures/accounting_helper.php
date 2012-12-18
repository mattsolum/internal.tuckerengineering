<?php defined('BASEPATH') OR exit('No direct script access allowed');

//Stores a list of ledger items
class StructAccounting
{
	//Itemized
	public $credits = NULL;
	public $debits	= NULL;
	
	public function __construct($json = NULL)
	{
		$this->credits = array();
		$this->debits  = array();
		if(is_string($json))
		{
			$this->set_from_json($json);
		}
	}
	
	public function set_client_id($id)
	{
		foreach($this->debits AS $debit)
		{
			$this->debit->client_id = $id;
		}
	}
	
	public function set_job_id($id)
	{
		foreach($this->debits AS $debit)
		{
			$this->debit->job_id = $id;
		}
		
		return TRUE;
	}
	
	public function set_from_json($json)
	{
		if(is_string($json))
		{
			$json = json_decode($json);
		}
		
		if($json !== NULL)
		{
			foreach($json->credits AS $credit)
			{
				$this->credits[] = new StructCredit($credit);
			}
			
			foreach($json->debits AS $debit)
			{
				$this->debits[] = new StructDebit($debit);
			}
		}
	}
	
	public function sort_debits()
	{
		$this->debits = $this->quicksort_by_property($this->debits, 'amount');
	}
	
	private function quicksort_by_property($arr, $property, $left = 0, $right = NULL)
	{
		// when the call is recursive we need to change
		//the array passed to the function yearlier
		static $array = array();
		if( $right == NULL )
		{
			$array = $arr;
			$right = count($array)-1;//last element of the array
		}
		 
		$i = $left;
		$j = $right;
		 
		$tmp = $array[(int)(($left+$right)/2)]->$property;
		 
		// partion the array in two parts.
		// left from $tmp are with smaller values,
		// right from $tmp are with bigger ones
		do
		{
			while( $array[$i]->$property < $tmp )
			$i++;
			 
			while( $tmp < $array[$j]->$property )
			$j--;
			 
			// swap elements from the two sides
			if( $i <= $j )
			{
				$w = $array[$i];
				$array[$i] = $array[$j];
				$array[$j] = $w;
				 
				$i++;
				$j--;
			}
		}while( $i <= $j );
		 
		// devide left side if it is longer the 1 element
		if( $left < $j )
		$this->quicksort_by_property(NULL, $property, $left, $j);
		 
		// the same with the right side
		if( $i < $right )
		$this->quicksort_by_property(NULL, $property, $i, $right);
		 
		// when all partitions have one element
		// the array is sorted
		 
		return $array;
	}
	
	public function debits_total()
	{
		return $this->debit_total();
	}
	
	public function debit_total()
	{
		$total = 0;
		
		foreach($this->debits AS $debit)
		{
			$total += $debit->amount;
		}
		
		return $total;
	}
	
	public function credits_total()
	{
		return $this->credit_total();
	}
	
	public function credit_total()
	{
		$total = 0;
		
		foreach($this->credits AS $credit)
		{
			$total += $credit->amount;
		}
		
		return $total;
	}
		
	public function total()
	{
		return $this->credit_total + $this->debit_total;
	}
	
	public function is_valid()
	{
		//todo
		return TRUE;
	}
	
	public function __toString()
	{
		$debit_total 	= 0;
		$credit_total 	= 0;
		
		$string 		= 'Debits: ';
		
		foreach($this->debits AS $debit)
		{
			$debit_total += $debit->amount;
			
			$string .= (string)$debit . ', ';
		}
		
		$string = substr($string, 0, strlen($string) - 2) . ' : Total $' . number_format($debit_total, 2);
		
		$string .= "\n\nCredits: ";
		
		//Now credits
		foreach($this->credits AS $credit)
		{
			$credit_total += $credit->amount;
			
			$string .= (string)$credit . ', ';
		}
		
		$string = substr($string, 0, strlen($string) - 2) . ' : Total $' . number_format($credit_total, 2);
		
		return $string;
	}
}