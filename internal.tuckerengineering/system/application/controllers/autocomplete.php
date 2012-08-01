<?php

class Autocomplete extends Controller {

	function client()
	{
		parent::Controller();	
	}
	
	function index()
	{
		$this->load->view('ac_test');
	}
	
	function client_name($text)
	{	
		$result = '';
		
		$query = $this->db->query("SELECT * FROM ac_test WHERE name like \"$text%\" LIMIT 1");
		
		foreach( $query->result() AS $line )
		{
			$result .= $line->name;	
		}
		
		$this->load->view('ajax/autocomplete', array('result' => $result));
	}
	
}