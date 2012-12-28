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

	public function database_migrate($id)
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

	public function database_backup()
	{
		$this->load->view('admin/database');
	}

	public function database_restore()
	{
		$this->load->view('admin/database');
	}
}