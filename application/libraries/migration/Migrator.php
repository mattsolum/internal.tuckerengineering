<?PHP

class Migrator {
	private $CI			= NULL;

	private $id 		= NULL;
	private $errors 	= array();
	private $data		= NULL;
	private $base_path	= './application/libraries/migration/';

	public function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->helper('file');
	}

	public function get_datamodels()
	{
		return get_filenames($this->base_path . 'resources/data/');
	}

	public function set_file($filename)
	{
		str_replace('/', '', $filename);

		if(!file_exists('./resources/data/' . $filename))
		{
			return FALSE;
		}

		if(preg_match('/\.xml$/', $filename))
		{
			$this->data = simplexml_load_file($this->base_path . 'resources/data/' . $filename);
		}
		else $this->data = file_get_contents($this->base_path . 'resources/data/' . $filename);

		return TRUE;
	}

	public function migrate()
	{

	}

	public function get_id()
	{
		if($this->id == NULL)
		{
			$this->id = ceil(microtime(TRUE) * 10000);
		}

		return $this->id; 
	}

	private function increment()
	{
		if($this->id == NULL)
		{
			return FALSE;
		}
		$status = 0;

		$filename = $this->base_path . 'resources/status/' . $this->id . '_done.txt';

		if(file_exists($filename))
		{
			$status = int_val(file_get_contents($filename));
		}

		$status += 1;

		return file_put_contents($filename, $status);
	}

	private function set_status($type, $message)
	{
		if($this->id == NULL)
		{
			return FALSE;
		}
		$error = '';

		$filename = $this->base_path . 'resources/status/' . $this->id . '_status.txt';

		if(file_exists($filename))
		{
			$error = file_get_contents($filename);
		}
		
		$error .= "/n" . strtoupper($type) . ' - ' . str_replace("\n", '', $message);

		return file_put_contents($filename, $error);
	}
}