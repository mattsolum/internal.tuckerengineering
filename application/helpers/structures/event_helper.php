<?php defined('BASEPATH') OR exit('No direct script access allowed');

class StructEvent
{
	public $event = NULL;
	public $data = NULL;
	
	public function __construct($event, &$data)
	{
		$this->event = $event;
		$this->data =& $data;
	}

	/**
	 * Returns the nth segment of the event name. Can be negative.
	 *
	 * Negative numbers count segments from the end.
	 * 
	 * @param  integer $n
	 * @return string
	 */
	public function segment($n)
	{
		$segments = explode('.', $this->event);

		$n = $n % count($segments);

		if($n < 0)
		{
			$n = count($segments) + $n;
		}

		return $segments[$n];
	}
}