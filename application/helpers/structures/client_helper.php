<?php defined('BASEPATH') OR exit('No direct script access allowed');


class StructClient
{
	//Client Information
	public $id = NULL;
	public $name = '';
	//Address
	public $location = NULL;
	//Contact Information
	public $contact = array();
	//Client that *this* client represents
	public $parent = NULL;
	//Notes
	public $note = '';
}