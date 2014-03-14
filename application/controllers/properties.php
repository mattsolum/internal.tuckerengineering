<?php

class Properties extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Navigation');
		$this->load->model('Property');
		$this->load->model('Job');
		$this->load->model('Client');

		$this->User->check_auth();
	}

	public function _remap($method)
	{
		$param_offset = 2;

		// Default to index
		if ( ! method_exists($this, $method))
		{
			// We need one more param
			$param_offset = 1;
			$method = 'index';
		}

		// Since all we get is $method, load up everything else in the URI
		$params = array_slice($this->uri->rsegment_array(), $param_offset);

		// Call the determined method with all params
		call_user_func_array(array($this, $method), $params);
	} 

	function index($property_id = NULL)
	{	
		if($property_id == NULL)
		{
			$this->load->view('properties/index');
		}
		else
		{
			if(preg_match('/^[0-9]+$/', $property_id))
			{
				$property = $this->Property->get($property_id);

				if($property != FALSE)
				{
					redirect('properties/' . $property->slug());	
				}
				else
				{
					$this->Messages->flash('The property with ID "' . $id . '" could not be found.', 'error');
					redirect('properties');
				}
			}
			else
			{
				$property = $this->Property->get_by_string(str_replace('_', ' ', $property_id));

				if($property != FALSE)
				{
					$jobs = $this->Job->get_by_property_id($property->id);
					$this->load->view('properties/view', array('property' => $property, 'jobs' => $jobs));
				}
				else
				{
					$this->Messages->flash('The property with ID ' . $id . ' could not be found.', 'error');
					redirect('properties');
				}
			}
		}
	}

	
}

/* End of file job.php */
/* Location: ./system/application/controllers/job.php */