<?php defined('BASEPATH') OR exit('No direct script access allowed');

class History extends CI_Model {
	
	private $CI = NULL;
	
	public function __construct()
	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->model('Note');	
	}

	public function cl_create($data)
	{
		$this->created('client', $data);
	}

	public function cl_update($data)
	{
		$this->updated('client', $data);
	}

	public function pr_create($data)
	{
		$this->created('property', $data);
	}

	public function pr_update($data)
	{
		$this->updated('property', $data);
	}

	public function jb_create($data)
	{
		$this->created('job', $data);
	}

	public function jb_update($data)
	{
		$this->updated('job', $data);
	}

	private function created($type, $data)
	{
		$note = new StructNote();
		$note->user->id = 0;
		$note->type 	= strtolower($type);
		$note->text 	= 'Created by ' . $this->CI->User->get_name() . '.';
		$note->type_id 	= $data->id;

		$this->CI->Note->commit($note);
	}

	private function updated($type, $data)
	{
		$note = new StructNote();
		$note->user->id = 0;
		$note->type 	= strtolower($type);
		$note->text 	= 'Updated by ' . $this->CI->User->get_name() . '.';
		$note->type_id 	= $data->id;

		$this->CI->Note->commit($note);
	}
}