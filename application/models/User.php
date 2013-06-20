<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Model {
	
	private $CI 			= NULL;
	private $user			= NULL;
	
	private $hasher			= NULL;
	
	public function __construct()
	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->file(APPPATH . 'libraries/phpass/PasswordHash.php', FALSE);
		$this->CI->load->library('session');

		$this->user = new StructUser();
		
		$this->hasher = new PasswordHash(8, FALSE);
		
		$this->sess_auth();
	}

	public function get_current_user() {
		return $this->user;
	}
	
	public function clear_for_this_session()
	{
		$user = $this->get_user(0);

		if($user == FALSE)
		{
			$this->user = new StructUser();
			$this->user->id = 0;
			$this->user->name = 'Io';
		}

		else $this->user = $user;
	}

	public function get_user_id()
	{
		return $this->user->id;
	}
	
	public function get_name()
	{
		return $this->user->name;
	}
	
	public function get_email()
	{
		return $this->user->get_email();
	}
	
	public function is_authed()
	{
		return $this->user->id != NULL;
	}
	
	public function read_enabled($f)
	{
		return ($this->is_authed() && $this->user->read_enabled($f));
	}
	
	public function write_enabled($f)
	{
		return ($this->is_authed() && $this->user->write_enabled($f));
	}
	
	public function delete_enabled($f)
	{
		return ($this->is_authed() && $this->user->delete_enabled($f));
	}
	
	public function check_auth($permissions = '')
	{
		if($permissions == '')
		{
			$permissions = uri_string();
		}

		if(!$this->is_authed())
		{
			$this->CI->Messages->flash('You need to be logged in to view this information.', 'error');
			redirect('user/auth/' . urlencode(base64_encode(uri_string())));
			return FALSE;
		}

		/*if($this->read_enabled('/admin'))
		{
			$this->CI->output->enable_profiler(TRUE);
		}/**/

		if($this->read_enabled($permissions))
		{
			$this->CI->session->set_userdata('last_page', uri_string());

			return TRUE;
		}
		else
		{
			log_message('Error', 'User ' . $this->user->name . ' does not have permissions to view ' . uri_string());
			$last_page = $this->CI->session->userdata('last_page');
			
			if($last_page == FALSE)
			{
				$last_page = 'user/auth';
			}

			redirect($last_page);
			return FALSE;
		}
	}

	private function sess_auth()
	{
		if($this->CI->session->userdata('user_id') != NULL)
		{
			$user = $this->get_user($this->CI->session->userdata('user_id'));
			
			if($user != FALSE)
			{
				$this->user = $user;
			}
		}
	}
	
	public function auth($u, $p)
	{
		$user = $this->get_user($u);
		
		if($user === FALSE)
		{
			return FALSE;
		}
		
		$passphrase = $this->make_passphrase($u, $p);
		
		if($this->hasher->CheckPassword($passphrase, $user->get_hash()))
		{
			$this->user = $user;
			
			$this->CI->session->set_userdata('user_id', $this->user->id);
			
			return TRUE;
		}
		
		return FALSE;
	}
	
	public function de_auth()
	{
		if(!$this->is_authed())
		{
			return TRUE;
		}
		
		$this->user = NULL;
		$this->CI->session->sess_destroy();
		
		return TRUE;
	}
	
	private function get_permissions($user_id)
	{
		$where = array('user_id' => $user_id);
		$perm = array();
		
		$this->CI->db->order_by('LENGTH(function)', 'DESC');
		
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
	
	private function sanitize_email($u)
	{
		return preg_replace('/[^a-zA-Z0-9@\. _-]/', '', $u);
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
	
	public function commit_permission($user_id, $f, $level)
	{
		$this->CI->db->trans_start();
			
		if($this->permission_exists($user_id, $f))
		{
			$this->update_permission($user_id, $f, $level);
		}
		else
		{
			$this->create_permission($user_id, $f, $level);
		}
		
		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in User method commit_permission: failed.');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	private function create_permission($user_id, $f, $level)
	{
		$data = array('user_id' => $user_id, 'function' => $f, 'permissions' => $level);
		
		if($this->CI->db->insert('permissions', $data))
		{
			return TRUE;
		}
		
		log_message('Error', 'Error in User method create_permission: failed.');
		return FALSE;
	}
	
	private function update_permission($user_id, $f, $level)
	{
		$data = array('permissions' => $level);
		
		$this->CI->db->where('user_id', $user_id);
		$this->CI->db->where('function', $f);
		
		if($this->CI->db->update('permissions', $data))
		{
			return TRUE;
		}
		
		log_message('Error', 'Error in User method update_permission: failed.');
		return FALSE;
	}
	
	private function delete_permissions_by_user($user_id)
	{
		$this->CI->db->trans_start();
		
		$where = array('user_id' => $user_id);
		
		$this->CI->db->delete('permissions', $where);
		
		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in User method delete_permissions_by_user: failed.');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	private function permission_exists($user_id, $f)
	{
		$where = array('user_id' => $user_id, 'function' => $f);
		
		$query = $this->CI->db->get_where('permissions', $where);
		
		if($query->num_rows() > 0)
		{
			return TRUE;
		}
		
		return FALSE;
	}
	
	private function get_default_permissions()
	{
		$pstr = setting('users.default_permissions');
		
		if($pstr !== FALSE)
		{
			$perms = explode('|', $pstr);
			
			$result = array();
			
			foreach($perms AS $perm)
			{
				$separates = explode(' ', $perm);
				$result[$separates[0]] = $separates[1];
			}
			
			return $result;
		}
		
		return array();
	}
	
	public function get_user($u)
	{
		$u = $this->sanitize_email($u);
		$where = array();
		
		if(preg_match('/^[0-9]+$/', $u))
		{
			//$u is user_id
			$where['user_id'] = $u;
		}
		else
		{
			//$u is email
			$where['email'] = $u;	
		}
		
		$query = $this->CI->db->get_where('users', $where);
		
		if($query->num_rows > 0)
		{
			$row = $query->row(0);
			
			$user = new StructUser();
			
			$user->id		= $row->user_id;
			$user->name		= $row->name;
			
			$user->set_email($row->email);
			$user->set_hash($row->password);
			
			foreach($this->get_permissions($user->id) AS $func => $perm)
			{
				$user->set_permission($func, $perm);
			}
			
			return $user;
		}
		
		return FALSE;
	}
	
	public function commit_user($user)
	{
		$data = array();
		$id = $this->user_exists($user->get_email());
		
		$this->CI->db->trans_start();
		
		if($id !== FALSE)
		{
			//User exists. Update it.
			$data['name'] 		= $user->name;
			$data['password']	= $user->get_hash();
			
			$this->CI->db->where('email', $user->get_email());
			$this->CI->db->update('users', $data);
			
			$this->delete_permissions_by_user($user->id);
		}
		else
		{
			//User does not exist. Create a new one.
			$data['user_id']             = $this->get_next_id();
			$data['email']			= $user->get_email();
			$data['name'] 			= $user->name;
			$data['password']		= $user->get_hash();
			$data['date_added'] 	= now();

			$id = $data['user_id'];
			
			$this->CI->db->insert('users', $data);
			
			$id = $this->user_exists($user->get_email());
		}
		
		//Apply default permissions if there are none
		if(count($user->get_permissions()) == 0)
		{
			foreach($this->get_default_permissions() AS $key => $val)
			{
				$user->set_permission($key, $val);
			}
		}
		
		//Commit permissions
		foreach($user->get_permissions() AS $func => $perm)
		{
			$this->commit_permission($id, $func, $perm);
		}
		
		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in User method commit_user: failed.');
			return FALSE;
		}
		else
		{
			return $id;
		}
	}
	
	private function user_exists($email)
	{
		$query = $this->CI->db->get_where('users', array('email' => $email));
		
		if($query->num_rows() > 0)
		{
			$row = $query->row(0);
			
			return $row->user_id;
		}
		return FALSE;
	}
	
	public function reset_password($user_id)
	{
		$new_password = $this->generate_password(12);
		
		$user = $this->get_user($user_id);
		
		$user->set_password($new_password);
		
		$this->commit_user($user);
		
		return $new_password;
	}
	
	public function generate_password($length)
	{
		$valid_chars = str_shuffle('abcdefghijklmnopABCDEFGHIJKLMNOP1234567890');
	
		// start with an empty random string
		$random_string = "";
		
		// count the number of chars in the valid chars string so we know how many choices we have
		$num_valid_chars = strlen($valid_chars);
		
		// repeat the steps until we've created a string of the right length
		for ($i = 0; $i < $length; $i++)
		{
			// pick a random number from 1 up to the number of valid chars
			$random_pick = mt_rand(1, $num_valid_chars);
			
			// take the random character out of the string of valid chars
			// subtract 1 from $random_pick because strings are indexed starting at 0, and we started picking at 1
			$random_char = $valid_chars[$random_pick-1];
			
			// add the randomly-chosen char onto the end of our string so far
			$random_string .= $random_char;
		}
		
		// return our finished random string
		return $random_string;
	}

	private function get_next_id()
	{
		$this->CI->db->order_by('user_id', 'DESC');
		$this->CI->db->limit(1);

		$query = $this->CI->db->get('users');

		if($query->num_rows() > 0)
		{
			$result = $query->row(0);

			return $result->user_id + 1;
		}

		return 0;
	}
}