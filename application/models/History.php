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

	public function committed($e)
	{
		//log_message('error', '--- History->committed() called');
		switch ($e->segment(-1))
		{
			case 'create':
				$this->created($e);
				break;
			case 'update':
				$this->updated($e);
				break;
		}
	}

	private function created($e)
	{
		//log_message('error', '--- History->created() called');
		$type = strtolower(str_replace('Struct', '', get_class($e->data)));

		$name = $this->CI->User->get_name();
		$message = 'This ' . $type . ' record was created by ' . $name . '.';

		if($this->CI->User->get_user_id() == 0)
		{
			//Belongs to the system.
			$message = 'I imported this ' . $type . ' record.';
		}

		$note = new StructNote();
		$note->user->id = 0;
		$note->type 	= $type;
		$note->text 	= $message;
		$note->type_id 	= $e->data->id;

		$this->CI->Note->commit($note);
	}

	private function updated($e)
	{
		//log_message('error', '--- History->updated() called');
		$type = strtolower(str_replace('Struct', '', get_class($e->data)));

		$name = $this->CI->User->get_name();

		if($this->CI->User->get_user_id() == 0)
		{
			$name = 'myself';
		}

		$note = new StructNote();
		$note->user->id = 0;
		$note->type 	= strtolower($type);
		$note->text 	= 'This ' . $note->type . ' record was updated by ' . $name . '.';
		$note->type_id 	= $e->data->id;

		$this->CI->Note->commit($note);
	}
}