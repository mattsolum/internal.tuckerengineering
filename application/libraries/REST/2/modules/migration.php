<?PHP

class MigrationAPI extends PrototypeAPI
{
	
	public function __construct(&$API)
	{
		parent::__construct($API);
		$this->CI->load->library('migration/Migrator');
		$this->CI->load->helper('directory');
	}

	public function get()
	{
		$id = preg_replace('/[^0-9]/', '', $this->API->id);

		if($id != NULL)
		{
			$filename = './resources/migration/data/' . $id . '.txt';
			if(file_exists($filename))
			{
				return file_get_contents($filename);
			}
			else
			{
				return '0';
			}
		}
		else
		{
			return $this->id_get();
		}
	}

	public function id_get()
	{
		return microtime(true) * 10000;
	}

	public function put()
	{
		return $this->post();
	}

	public function post()
	{
		$id = preg_replace('/[^0-9]/', '', $this->API->id);

		if($id != NULL && $this->input->POST('status') != FALSE)
		{
			$filename = './resources/migration/data/' . $id . '.txt';
			return file_put_contents($filename, $this->input->POST('status'));
		}
	}

	public function client_post()
	{
		
	}
}