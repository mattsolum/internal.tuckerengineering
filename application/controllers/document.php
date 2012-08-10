<?php

class Document extends CI_Controller {
	
	function index()
	{	
		$this->load->view('index');
	}
	
	/**
	 * View... view. Displays a single record
	 *
	 * @author Matthew Solum
	 * @param $document_id
	 * @return NULL
	 */
	public function view($document_id)
	{
		//code
	}
	
	/**
	 * Displays a single record for editing
	 *
	 * @author Matthew Solum
	 * @param $document_id
	 * @return null
	 */
	public function edit($document_id)
	{
		//code
	}
	
	/**
	 * Dialogue box used to send document as email attachment
	 *
	 * @author Matthew Solum
	 * @param $resource_id
	 * @return NULL
	 */
	public function send($resource_id)
	{
		//code
	}
	
}

/* End of file document.php */
/* Location: ./system/application/controllers/document.php */