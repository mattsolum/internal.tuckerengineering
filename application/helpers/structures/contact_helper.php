<?php defined('BASEPATH') OR exit('No direct script access allowed');

class StructContact
{
	public $id = NULL;
	public $type = '';
	public $info = '';
	public $note = '';

	public function is_valid()
	{
		if($this->id == NULL || $this->type == '' || $this->info == '')
		{
			return FALSE;
		}

		return TRUE;
	}
}