<?php defined('BASEPATH') OR exit('No direct script access allowed');


class StructUser
{
	//User Information
	public $id				= NULL;
	public $name			= NULL;
	public $office_id		= NULL;
	public $location		= NULL;
	
	private $email			= NULL;
	private $permissions	= NULL;
	private $password		= NULL;
	private $hash			= NULL;
	
	
	public function __construct($json = NULL)
	{
		$this->permissions = array();
		$this->location = new StructProperty();
		
		if($json !== NULL)
		{
			$this->set_from_json($json);
		}
	}
	
	public function set_from_json($json)
	{
		if(is_string($json))
		{
			$json = json_decode($json);
		}
		
		$this->id			= $json->id;
		$this->name			= (isset($json->name))?$json->name:NULL;
		$this->office_id	= (isset($json->office_id))?$json->office_id:NULL;
		
		if(isset($json->email))
		{
			$this->email	= $json->email;
		}
		
		if(isset($json->permissions))
		{
			$this->permissions	= $json->permissions;
		}
		
		if(isset($json->hash))
		{
			$this->hash			= $json->hash;
		}
	}
	
	public function is_valid()
	{
		if($this->email == NULL || $this->name == NULL || $this->hash == NULL)
		{
			log_message('Error', 'User email, name, or hash is not set: ' . $this);
			return FALSE;
		}
	
		if(!validate_email($this->email) || strlen($this->hash) < 20)
		{
			log_message('Error', 'User email is not valid or the hash is shorter than 20 characters: ' . $this);
			return FALSE;
		}
		
		return TRUE;
	}
	
	private function hash()
	{
		if(!isset($this->email) || !isset($this->password))
		{
			return FALSE;
		}
		
		$passphrase = $this->make_passphrase($this->email, $this->password);
		
		$CI =& get_instance();
		
		$CI->load->library('session');
		
		$hasher = new PasswordHash(8, FALSE);
		
		$this->hash = $hasher->HashPassword($passphrase);
		
		return TRUE;
	}
	
	private function make_passphrase($u, $p)
	{
		$passphrase = '';
		$for = min(strlen($u), strlen($p));
		
		for($i = 0; $i < $for; $i++)
		{
			$passphrase .= substr($u, 0, 1);
			$passphrase .= substr($p, 0, 1);
			
			$u 	 	= substr($u, 1);
			$p 	 	= substr($p, 1);
		}
		
		$passphrase .= $u . $p;
		
		//To avoid DOS attacks passphrase cannot be longer than 72 character
		//So... Just use the middle 72 characters.
		if(strlen($passphrase) > 72)
		{
			$start = ceil((strlen($pphrase) - 72) / 2);
		
			$passphrase = substr($pphrase, $start, 72);
		}
		
		return $passphrase;
	}
	
	public function set_permission($f, $level)
	{
		if(!preg_match('/^[0-9]+$/', $level))
		{
			return FALSE;
		}
		
		if($level > 3)
		{
			$level = 3;
		}
		
		$f = $this->format_function($f);
		
		$this->permissions[$f] = $level;
		
		//Sort by the length of the array key descending
		//This way when we are searching for a match
		//We will get the largest match possible first
		//and permissions can be explicitly excluded
		$keys = array_map('strlen', array_keys($this->permissions));
		array_multisort($keys, SORT_DESC, $this->permissions);
		
		return TRUE;
	}
	
	//Permissions are stored as an int between 0 and 3
	public function read_enabled($f)
	{
		return $this->enabled($f, 0);
	}
	
	public function write_enabled($f)
	{
		return $this->enabled($f, 1);
	}
	
	public function delete_enabled($f)
	{
		return $this->enabled($f, 2);
	}
	
	private function enabled($f, $index)
	{

		if($f == '')
		{
			return TRUE;
		}

		//Lets format the function we are checking permissions for
		//the same way the functions in the database are stored.
		$f = $this->format_function($f);
		
		//Now we loop through each permission the logged in user has
		//Until we find a match for the begining part of the permission
		//we are checking. 
		//These permissions are sorted by longest to shortest
		//so we should find the greatest possible match first.
		//This way permissions can be explicitly excluded or included
		foreach($this->permissions AS $function => $perm)
		{
			//Check if the first part of function we are checking 
			//matches the function we are checking it against.
			//If it does, check if the permissions this user is granted
			//are greater than the required permissions.
			if(substr($f, 0, strlen($function)) == $function)
			{
				if($perm > $index)
				{
					return TRUE;
				}
				else
				{
					return FALSE;	
				}
			}
		}
		
		return FALSE;
	}
	
	private function format_function($f)
	{
		if(substr($f, 0, 1) != '/')
		{
			$f = '/' . $f;
		}
		
		if(substr($f, -1) == '/' && strlen($f) > 1)
		{
			$f = substr($f, 0, strlen($f) - 1);
		}
		
		$f = str_replace(' ', '', $f);
		
		return strtolower($f);
	}
	
	private function sanitize_email($e)
	{
		return preg_replace('/[^a-zA-Z0-9@\. _-]/', '', $e);
	}
	
	public function set_email($e)
	{
		$this->email = $this->sanitize_email($e);
		$this->hash();
		return TRUE;
	}
	
	public function get_email()
	{
		return $this->email;
	}
	
	public function set_password($p)
	{
		$this->password = $p;
		$this->hash();
		return TRUE;
	}
	
	public function get_hash()
	{
		return $this->hash;
	}
	
	public function set_hash($hash)
	{
		return $this->hash = $hash;
	}
	
	public function get_permissions()
	{
		return $this->permissions;
	}
}