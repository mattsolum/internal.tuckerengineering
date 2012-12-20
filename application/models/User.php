<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Model {
	
	private $CI 			= NULL;
	private $user_id		= NULL;
	private $name			= NULL;
	private $permissions	= NULL;
	
	private $hasher			= NULL;
	
	public function __construct()
	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->file(APPPATH . 'libraries/phpass/PasswordHash.php', FALSE);
		
		$this->hasher = new PasswordHash(8, FALSE);
	}
	
	public function auth($u, $p)
	{
		$user = $this->get_user($u);
		
		if($user === FALSE)
		{
			return FALSE;
		}
		
		$passphrase = $this->make_passphrase($u, $p);
		
		if($this->hasher->CheckPassword($passphrase, $user['password']))
		{
			$this->user_id 		= $user['user_id'];
			$this->name 		= $user['name'];
			$this->username		= $user['username'];
			
			$this->permissions 	= $this->get_permissions($this->user_id);
			
			return TRUE;
		}
		
		return FALSE;
	}
	
	private function get_user($u)
	{
		$u = $this->sanitize_username($u);
		$where = array();
		
		if(preg_match('/^[0-9]+$/', $u))
		{
			//$u is user_id
			$where['user_id'] = $u;
		}
		else
		{
			//$u is username
			$where['username'] = $u;	
		}
		
		$query = $this->CI->db->get_where('users', $where);
		
		if($query->num_rows > 0)
		{
			$return = $query->result_array();
			
			return $return[0];
		}
		
		return FALSE;
	}
	
	private function get_permissions($user_id)
	{
		$where = array('user_id' => $user_id);
		$perm = array();
		
		$query = $this->CI->db->get_where('permissions', $where);
		
		if($query->num_rows() > 0)
		{
			foreach($query->result() AS $row)
			{
				$perm[$row->function] = $row->permissions;
			}
			
			return $perm;
		}
		
		return FALSE;
	}
	
	
	//Permissions are stored as an int between 0 and 3
	//
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
		$f = $this->format_function($f);
		
		foreach($this->permissions AS $function => $perm)
		{
			$function = str_replace('/', '\/', $function);
			if(preg_match("/^$function/", $f) && $perm > $index)
			{
				return TRUE;
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
		
		if(substr($f, -1) == '/')
		{
			$f = substr($f, 0, strlen($f) - 1);
		}
		
		return strtolower($f);
	}
	
	private function sanitize_username($u)
	{
		return preg_replace('/[^a-zA-Z0-9@\. _-]/', '', $u);
	}
	
	private function hash($u, $p)
	{	
		$pphrase = $this->make_passphrase($u, $p);
		
		return $this->hasher->HashPassword($pphrase);
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
	
	public function get_name()
	{
		return $this->name;
	}
}