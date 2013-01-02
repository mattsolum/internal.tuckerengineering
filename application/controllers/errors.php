<?php defined('BASEPATH') OR exit('No direct script access allowed');

class errors extends CI_Controller {

	public function page_not_found()
	{
		$this->load->view('sections/404');
	}
}