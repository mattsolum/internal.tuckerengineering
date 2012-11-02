<?php defined('BASEPATH') OR exit('No direct script access allowed');

//Stores a list of ledger items
class StructAccounting
{
	//Summary totals
	public $total 	= 0.0;
	
	//Itemized
	
	
	//Iteration
	private $iteration = array();
	
	public function __SET($name, $value)
	{
		if($name != 'iteration')
		{
			if($name == 'total')
			{
				
			}
			else
			{
				//Before I forget again I ought to comment this section
				//The idea is you can iterate through the object itsself
				//to get each individual item someone is billed for
				if(get_class($value) == 'structLedger')
				{
					//As long as the value is the expected type
					//go ahead and assign it to the given name
					$this->$name = $value;
					
					//Now put a pointer in the iteration array
					//but only if it is not already there
					if(!array_search($name, $this->iteration))
					{
						$this->iteration[] = $name;
						
						//Go ahead and sort it so that life is
						//dandy
						sort($this->iteration);
						
						//Summarize totals up all the items
						//and assigns the value to 'total'
						$this->summarize();
					}
				} else trigger_error('Attempted to assign a variable that is not of class "structLedger" value to StructAccounting.');
			}
		}
	}

	//To cut down on the amount of effort it might take to 
	//format a string as a PHP variable when adding 
	public function add($name, $value)
	{
		$name = preg_replace('[^a-zA-Z0-9 -_]', '', $name);
		$name = preg_replace('/\s\s+/', ' ', $name);
		$name = str_replace(' ', '_', $name);
		$name = strtolower($name);

		$this->__SET($name, $value);
	}
	
	private function summarize()
	{
		$this->total = 0;
		
		foreach($this->iteration AS $key)
		{
			$this->total += $this->$key;
		}
	}
	
	//Iteration functions
	//Allows the object to be looped through
	//like an array
	public function rewind()
	{
		reset($this->iteration);
	}
	
	public function current()
	{
		$name = current($this->iteration);
		return($this->$name);
	}
	
	public function key()
	{
		return key($this->iteration);
	}
	
	public function next()
	{
		$name = next($this->iteration);
		return($this->$name);
	}
	
	public function valid()
	{
		$key = key($this->iteration);
		return ($key !== NULL && $key !== FALSE);	
	}
}