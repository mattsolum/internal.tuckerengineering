<?PHP

class PD4ML {
	private $jar 			= 'pd4ml_demo.jar';
	private $java 			= '"C:\Program Files (x86)\Java\jre7\bin\java.exe"';
	private $format 		= 'pdf';
	private $storage		= 'storage/';
	private $library_dir 	= '';
	private $cmd			= null;
	private $d_cmd 			= null;

	public function __construct()
	{
		$this->library_dir = substr(BASEPATH, 0, -7) . APPPATH . 'libraries/pd4ml/';

		$this->jar = $this->library_dir . $this->jar;

		$this->d_cmd = array (
								'Xmx512m' 		=> null,
								'cp'	 		=> array (
													'jar' => $this->jar,
													'Pd4Cmd' => 'Pd4Cmd',
													'url' => '',
													'htmlwidth' => '830',
													'pageformat' => '612x792'
												),
								'insets'		=> '40,20,20,20,pt',
								'outformat' 	=> 'pdf'
							);

		$this->set_defaults();
	}

	private function set_option($name, $value)
	{
		if(strpos($name, '>'))
		{
			$path = explode('>', $name);
			return $this->cmd[$path[0]][$path[1]] = $value;
		}
		return $this->cmd[$name] = $value;
	}

	private function set_defaults()
	{
		$this->cmd = $this->d_cmd;
	}

	private function verify()
	{
		if(strlen($this->cmd['cp']['url']) == 0)
		{
			return FALSE;
		}

		if(strlen($this->cmd['cp']['htmlwidth']) == 0)
		{
			return FALSE;
		}

		return TRUE;
	}

	private function get_cmd()
	{
		$cmd = $this->java . ' ';
		$ncmd = '';

		foreach($this->cmd AS $opt => $value)
		{
			$ncmd = '-' . $opt . ' ';

			if(is_array($value))
			{
				foreach($value AS $k => $v)
				{
					$ncmd .= trim($v) . ' ';
					$ncmd = trim($ncmd) . ' ';
				}
			}
			else
			{
				$ncmd .= trim($value) . ' ';
			}

			$cmd .= trim($ncmd) . ' ';
		}

		return trim($cmd);
	}

	public function set_format($format)
	{
		$format = strtolower($format);

		if($format == 'rtf' || $format == 'pdf')
		{
			return $this->set_option('outformat', $format);
		}

		trigger_error('Only RTF and PDF formats are supported by this library.');

		return false;
	}

	public function exec($url)
	{
		var_dump($this->get_cmd());
	}

	public function pdf_from_url($url)
	{
		$this->set_option('cp>url', '"' . $url . '"');
		$this->set_format('pdf');

		$result = exec($this->get_cmd(), $result_arr, $return_var);
	    return implode("\n", $result_arr);
	}

	public function rtf_from_url($url)
	{
		$this->set_option('cp>url', '"' . $url . '"');
		$this->set_format('rtf');

		$result = exec($this->get_cmd(), $result_arr, $return_var);
	    return implode("\n", $result_arr);
	}

}