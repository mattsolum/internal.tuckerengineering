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

		$method_name = strtolower($this->type) . '_valid';

		if(method_exists($this, $method_name))
		{
			return $this->$method_name();
		}

		return TRUE;
	}

	public function set($type, $info, $note = '')
	{
		$type = strtolower(preg_replace('/[^a-zA-Z0-9 _-]/', '', $type));
		$this->type = $type;

		$method_name = 'prepare_' . $type;

		if(method_exists($this, $method_name))
		{
			$info = $this->$method_name($info);
		}

		$this->info = $info;
	}

	private function prepare_email($info)
	{
		return trim($info);
	}

	private function prepare_phone($info)
	{
		$info = preg_replace('/[^0-9]/', '', $info);
		$phone = '';

		for($i = 0; $i < strlen($info); $i++)
		{
			$phone .= $info[$i];

			if($i == 2 || $i == 5)
			{
				$phone .= '-';
			}
		}

		return $phone;
	}

	private function prepare_fax($info)
	{
		return $this->prepare_phone($info);
	}

	private function email_valid() {
		return (preg_match("/^[a-z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/", $this->info))?TRUE:FALSE;
	}

	private function phone_valid() {
		return (preg_match("/^[(]?\d{3}[)]?\s?-?\s?\d{3}\s?-?\s?\d{4}$/", $this->info))?TRUE:FALSE;
	}

	private function fax_valid() {
		return $this->phone_valid();
	}
}