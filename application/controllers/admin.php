<?php

class Admin extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Navigation');

		$this->User->check_auth();
	}

	public function index()
	{	
		$this->load->view('admin/index');
	}
	
	public function config()
	{
		$this->load->model('Setting');
		
		$data = array('results' => null);
		
		$data['results'] = $this->Setting->get_all();
		
		$this->load->view('config', $data);
	}

	public function database($sub = '', $id = '')
	{

		switch($sub)
		{
			case 'backup':
				$this->database_backup($id);
				break;
			case 'restore':
				$this->database_restore($id);
				break;
			case 'migrate':
				$this->database_migrate($id);
				break;
			default:
				$this->load->view('admin/database');
		}
	}

	public function logs($date = NULL)
	{
		$data = '';
		$dir_path = APPPATH . 'logs/';
		$files = array();

		if($date == NULL)
		{
			$date = date('Y-m-d');
		}

		if(is_dir($dir_path))
		{
			$file_path = $dir_path . 'log-' . $date . '.php';

			$files = scandir($dir_path);

			if(file_exists($file_path))
			{
				$data = file_get_contents($file_path);

				$data = trim(substr($data, strpos($data, "\n", 1)));
			}
		}

		$data = implode("\n", array_reverse(explode("\n", $data)));

		$this->load->view('admin/logs', array('date' => $date, 'files' => $files, 'log' => $data));
	}

	public function users($sub = '', $id = '')
	{
		switch($sub)
		{
			case 'create':
				$this->user_create($id);
				break;
			case 'edit':
				$this->user_edit($id);
				break;
			default:
				$this->load->view('admin/users');
		}
	}

	private function user_create($id)
	{
		$this->load->model('User');

		if($this->input->post('user_name') != NULL)
		{
			$user = new StructUser();

			$user->name = $this->input->post('user_name');
			$user->location->set_addr_1($this->input->post('user_addr_1'));
			$user->location->subpremise = $this->input->post('user_subpremise');
			$user->location->locality =	$this->input->post('user_locality');
			$user->location->postal_code =	$this->input->post('user_postal_code');
			$user->location->admin_level_1 = $this->input->post('user_admin_level_1');
			$user->location->admin_level_2 = 'United States';

			$user->set_email($this->input->post('user_email'));
			if($this->input->post('user_password') == '')
			{
				$password = $this->User->generate_password(12);
				
			}
			else
			{
				$password = $this->input->post('user_password');
			}

			$user->set_password($password);

			if($user->is_valid() && !$this->User->get_user($user->get_email()))
			{
				if($this->User->commit_user($user))
				{
					$this->load->model('Mail');
					$this->Mail->send_account_created($user, $password);
					$this->load->view('admin/user_create_success');
				}
				else
				{
					log_message('error', 'Failed to commit user in admin/user/create');
					$this->load->view('admin/user_create', array('user' => $user));
				}
			}
			else
			{
				log_message('error', 'User is not valid in admin/user/create');
				$this->load->view('admin/user_create', array('user' => $user));
			}
		}
		else
		{
			$this->load->view('admin/user_create');	
		}
	}

	private function database_migrate($id)
	{
		$this->load->library('migration/migrator');

		if($id == '')
		{
			if($this->input->post('migrator') != NULL)
			{

			}
			else
			{

			}

			$this->load->view('admin/database_migrate_prepare');
		}
		else
		{
			$this->load->view('admin/database_migrate_status', array('mig_id' => $id));
		}
	}

	private function database_backup()
	{
		$this->load->view('admin/database');
	}

	private function database_restore()
	{
		$this->load->view('admin/database');
	}
}