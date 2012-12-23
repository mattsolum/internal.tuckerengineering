<?php defined('BASEPATH') OR exit('No direct script access allowed');

class StructInvoice
{
	public $id 			= NULL;

	public $client		= NULL;
	public $jobs		= NULL;

	public $date_added	= NULL;
	public $date_sent	= NULL;

	public function __construct($json = NULL)
	{
		$this->jobs 	= array();
		$this->client 	= new StructClient();

		if($json != NULL)
		{
			$this->set_from_json($json);
		}
	}

	/**
	 * Loads data from a json dump of this object
	 * accepts a raw string of json or an interpreted object
	 *
	 * @author Matthew Solum
	 * @param string/object $json
	 */
	public function set_from_json($json)
	{
		if(is_string($json))
		{
			$json = json_decode($json);
		}
		
		if($json != NULL)
		{	
			if(isset($json->client))
			{
				$this->client->set_from_json($json->client);
			}

			if(isset($json->jobs))
			{
				foreach ($json->jobs as $job)
				{
					$this->jobs[] = new StructJob($job);
				}
			}
		}
	}

	public function is_valid()
	{
		if(!$this->client->is_valid())
		{
			return FALSE;
		}

		if(count($this->jobs) == 0)
		{
			return FALSE;
		}

		foreach($this->jobs as $job)
		{
			if(!$job->is_valid())
			{
				return FALSE;
			}
		}

		return TRUE;
	}

	public function __toString()
	{
		$str  = $this->id . '-' . $this->client->id . " ::\n";
		$str .= "Client:\n\t";
		$str .= str_replace("\n", "\n\t", (string)$this->client) . "\n";
		$str .= "Jobs:\n";

		foreach($this->jobs AS $key => $job)
		{
			$str .= "\t" . str_replace("\n", "\n\t", (string)$job) . "\n\n";
		}

		$str .= 'Total: $' . number_format($this->debits_total(), 2);

		return $str;
	}

	public function sort_jobs()
	{
		$this->jobs = $this->quicksort_by_property($this->jobs, 'id');
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

	/**
	 * Sums the totals of all included jobs
	 * @return float
	 */
	public function debits_total()
	{
		$total = 0;

		foreach($this->jobs AS $job)
		{
			$total += $job->accounting->debits_total();
		}

		return $total;
	}

	/**
	 * Sums the balance of all included jobs
	 * @return float
	 */
	public function balance()
	{
		$total = 0;

		foreach($this->jobs AS $job)
		{
			$total += $job->balance();
		}

		return $total;
	}

	/**
	 * Gets the date, as GMT unix timestamp, of the last payment 
	 * applied to a job on this invoice. 
	 * 
	 * If no credits have been applied to any
	 * job in this invoice it will return FALSE.
	 * 
	 * @return BOOL FALSE on failure, int on success
	 */
	public function date_paid()
	{
		$date_paid = FALSE;

		foreach($this->jobs AS $job)
		{
			foreach($job->credits AS $credit)
			{
				if($credit->date_added > $date_paid)
				{
					$date_paid = $credit->date_added;
				}
			}
		}

		return $date_paid;
	}
}