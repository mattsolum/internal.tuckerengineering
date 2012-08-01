<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Log extends Controller {
	
	function __construct()
	{
		parent::Controller();	
	}
	
	function index($display)
	{
		$this->display($display);
	}
	
	function display($type = 'all', $page = 0)
	{
		
	}
}