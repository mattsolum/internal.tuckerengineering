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
		return $this->CI->migrator->get_id();
	}

	public function status_get()
	{
		if($this->API->id != NULL)
		{
			$filename = './application/libraries/migration/resources/status/' . preg_replace('/[^0-9]/', '', $this->API->id) . '_status.txt';
			
			if(file_exists($filename))
			{
				$data = file_get_contents($filename);
				return array('status' => explode("\n", $data));
			}
			else
			{
				$this->error = 'Data for given ID does not exist.';
				return FALSE;
			}
		}

		$this->error = 'ID required.';
		return FALSE;
	}

	public function percent_get()
	{
		
		if($this->API->id != NULL)
		{
			$filename = './application/libraries/migration/resources/status/' . preg_replace('/[^0-9]/', '', $this->API->id) . '_done.txt';

			if(file_exists($filename))
			{
				$data = file_get_contents($filename);
				return $data;
			}
			else
			{
				$this->error = 'Data for the given ID does not exist.';
				return FALSE;
			}
		}

		$this->error = 'ID required.';
		return FALSE;
	}
}