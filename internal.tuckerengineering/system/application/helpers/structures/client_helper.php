<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Client
{
	//CI Base Class
	private $base = NULL;
	//Client Information
	public $id = NULL;
	public $name = '';
	//Address
	public $street_1 = '';
	public $street_2 = '';
	public $city = '';
	public $state = '';
	public $zip = '';
	//Contact Information
	public $contact = array();
	//Client this client represents
	public $parent = NULL;
	//Notes
	public $note = '';
}