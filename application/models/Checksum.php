<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Checksum extends CI_Model {
	
	private $CI = NULL;
	
	public function __construct()
	{
		parent::__construct();
		$this->CI =& get_instance();
	}

	/**
	 * Compares the hash of the current data with the old hash
	 * and returns TRUE if they are the same and FALSE if they 
	 * are not.
	 *
	 * @param  mixed $data 	The object that is currently being considered for updating
	 * @return boolean      Whether the hashes match or not.
	 */
	public function compare($data)
	{
		$current_hash = $this->hash($data);
		$type = $this->data_type($data);

		$old = $this->read($data);

		log_message('error', 'Checksum::compare; #' . $data->id . ' ' . $type . '; ' . $old->hash . ' == ' . $current_hash);

		if($old === FALSE || $current_hash == $old->hash)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Stores the new hash of given data.
	 * 
	 * @param  mixed $data   The data to be hashed
	 * @return boolean       Success or failure
	 */
	public function store($data)
	{
		if($this->exists($data))
		{
			return $this->update($data);
		}
		else
		{
			return $this->create($data);
		}
	}

	private function data_type($data)
	{
		return strtolower(str_replace('Struct', '', get_class($data)));
	}

	private function exists($data)
	{
		$where = array('id' => $data->id, 'type' => $this->data_type($data));

		$query = $this->CI->db->get_where('checksums', $where);

		if($query->num_rows() > 0)
		{
			return TRUE;
		}

		return FALSE;
	}

	private function create($data)
	{
		$values = array(
						'id' => $data->id,
						'type' => $this->data_type($data),
						'hash' => $this->hash($data)
					);

		$this->CI->db->trans_start();

		$query = $this->CI->db->insert('checksums', $values);

		//End the transaction
		$this->CI->db->trans_complete();
		
		//Handle errors in the jobs transaction.
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in Checksum method create: transaction failed.');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	private function read($data)
	{
		$where = array(
						'id' => $data->id,
						'type' => $this->data_type($data)
						);
		$query = $this->CI->db->get_where('checksums', $where);

		if($query->num_rows() > 0)
		{
			return $query->row(0);
		}
		else
		{
			return FALSE;
		}
	}

	private function update($data)
	{
		$values = array(
						'hash' => $this->hash($data)
					);

		$this->CI->db->trans_start();

		$this->CI->db->where('id', $data->id);
		$this->CI->db->where('type', $this->data_type($data));
		$query = $this->CI->db->update('checksums', $values);

		//End the transaction
		$this->CI->db->trans_complete();
		
		//Handle errors in the jobs transaction.
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in Checksum method update: transaction failed.');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	public function hash($data)
	{
		$this->clear_generic($data);

		$type = $this->data_type($data);
		$method = 'prepare_' . strtolower($type);

		//log_message('error', var_export($data, true));

		if(method_exists($this, $method))
		{
			$this->$method($data);
		}

		//echo($data . "\n\n");

		//echo(' ' . MD5(trim((string)$data)) . "\n\n\n");
		return MD5(trim((string)$data));
	}

	private function prepare_client(&$data)
	{
		$data->balance = 0;
	}

	private function clear_generic(&$data)
	{
		foreach($data as $key => $value)
		{
			if($key == 'notes' && is_object($data))
			{

				//Backwards for-loop so that the length doesn't change while
				//I am looping through
				for($i = count($data->notes) - 1; $i >= 0; $i--)
				{
					if($data->notes[$i]->user->id == 0)
					{
						unset($data->notes[$i]);
					}
				}
			}
			else if(is_array($value) || is_object($value))
			{
				if(is_array($data))
				{
					$this->clear_generic($data[$key]);
				}
				else
				{
					$this->clear_generic($data->$key);
				}
			}
		}
	}
}