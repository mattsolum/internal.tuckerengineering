<?php defined('BASEPATH') OR exit('No direct script access allowed');

class StructContact
{
	public $id = NULL;
	public $type = '';
	public $info = '';
	public $note = '';

	public function is_valid($strict = TRUE)
	{
		if($strict == TRUE && $this->id == NULL)
		{
			return FALSE;
		}
		
		if($this->type == '' || $this->info == '')
		{
			return FALSE;
		}

		return TRUE;
	}
}