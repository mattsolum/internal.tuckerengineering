<?PHP

class AdminAPI extends PrototypeAPI
{
	public function __construct(&$API)
	{
		parent::__construct($API);
	}

	public function get()
	{
		$dir_path = str_replace('system/', '', BASEPATH) . APPPATH . 'logs/';
		$data = '';

		if($this->API->id != NULL)
		{
			$date = preg_replace('/[^0-9-]/', '', $this->API->id);
		}
		else
		{
			$date = date('Y-m-d');
		}

		$file_name = 'log-' . $date . '.php';

		if(is_dir($dir_path))
		{
			if(file_exists($dir_path . $file_name))
			{
				$data = file_get_contents($dir_path . $file_name);
				$data = trim(substr($data, strpos($data, "\n", 1)));
			}
		}

		$data = array_reverse(explode("\n", $data));

		return $data;
	}
}