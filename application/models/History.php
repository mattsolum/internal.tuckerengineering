<?php defined('BASEPATH') OR exit('No direct script access allowed');

class History extends CI_Model {
	
	private $CI = NULL;
	
	public function __construct()
	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->model('Note');	
	}

	public function recent_activity()
	{
		//TODO
	}

	public function commited($e)
	{
		switch ($e->segment(-1))
		{
			case 'create':
				$this->created($e);
				break;
			case 'update':
				$this->updated($e);
		}
	}

	private function created($e)
	{
		$type = strtolower(str_replace('Struct', '', get_class($e->data));

		$note = new StructNote();
		$note->user->id = 0;
		$note->type 	= $type;
		$note->text 	= 'Created by ' . $this->CI->User->get_name() . '.';
		$note->type_id 	= $e->data->id;

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