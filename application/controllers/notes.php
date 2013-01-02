<?php defined('BASEPATH') OR exit('No direct script access allowed');

class notes extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Note');

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

	public function client($id)
	{
		$this->load->model('Client');
		if(!is_numeric($id))
		{
			$id_post = str_replace('_', ' ', $id);
			$final_id = $this->Client->get_id($id_post);
		}
		else
		{
			$final_id = $id;
		}
		

		if($this->input->post('note') != FALSE && $final_id != FALSE)
		{
			$message = $this->input->post('note');

			$note = new StructNote();
			$note->user->id = $this->User->get_user_id();
			$note->type 	= 'client';
			$note->text 	= $message;
			$note->type_id 	= $final_id;

			$this->Note->commit($note);
		}

		redirect(site_url('clients/' . $id));
	}

	public function delete($id)
	{
		$note = $this->Note->get_by_note($id);

		if($note != FALSE)
		{
			if($note->user->id == $this->User->get_user_id() || $this->User->delete_enabled('/notes'))
			{
				$this->Note->delete($id);
			}

			$this->redirect($note);
		}

		redirect($this->input->server('HTTP_REFERER'));
	}

	private function redirect($note)
	{
		switch($note->type)
		{
			case 'client':
				redirect(site_url('clients/' . $note->type_id));
				break;
			case 'job':
				redirect(site_url('jobs/' . $note->type_id));
				break;
			case 'property':
				redirect(site_url('properties/' . $note->type_id));
				break;
			default:
				redirect(site_url());
		}
	}
}