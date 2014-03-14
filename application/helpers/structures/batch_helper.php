<?php defined('BASEPATH') OR exit('No direct script access allowed');

class StructBatch
{
	public $client 		= NULL;
	public $payment		= NULL;
	public $jobs		= NULL;
	public $date 		= NULL;
	
	public function __construct()
	{
		$this->jobs 	= array();
		$this->payment 	= new StructPayment();
		$this->client 	= new StructClient();
	}
}