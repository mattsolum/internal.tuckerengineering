<?PHP

class WKHTMLTOPDF {
	private $exe 			= 'wkhtmltopdf';
	private $format 		= 'pdf';
	private $cache			= 'storage/';
	private $dir 			= '';
	private $cmd			= null;
	private $d_cmd 			= null;

	private $opts_prefix 	= '--';
	private $opts_suffix 	= ' ';

	public function __construct()
	{
		$this->dir = substr(BASEPATH, 0, -7) . APPPATH . 'libraries/wkhtmltox/';

		$this->exe = $this->dir . $this->exe;

		$this->d_cmd = array (
								'page-size' => 'Letter',
								'zoom' 		=> 1.25,
								'dpi'		=> 300
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

	private function get_cmd($in, $out)
	{
		$cmd = $this->exe . ' ';
		$ncmd = '';

		foreach($this->cmd AS $opt => $value)
		{
			$ncmd = $this->opts_prefix . $opt;

			if(is_array($value))
			{
				$ncmd .= $this->opts_suffix;

				foreach($value AS $k => $v)
				{
					$ncmd .= trim($v) . ' ';
					$ncmd = trim($ncmd) . ' ';
				}
			}
			elseif ($value != '')
			{
				$ncmd .= $this->opts_suffix . trim($value) . ' ';
			}

			$cmd .= trim($ncmd) . ' ';
		}

		return trim($cmd) . ' ' . $in . ' ' . $out;
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

	private function get_cache($url)
	{
		$id = $this->make_id($url);

		$filename = $this->dir . $this->cache . $id . '.pdf';

		if(file_exists($filename))
		{
			return file_get_contents($filename);
		}
	}

	private function make_id($url)
	{
		return md5($url);
	}

	private function save_html($html)
	{
		$id = $this->make_id($html);
		$filename = $this->dir . 'html/' . $id . '.htm';

		return file_put_contents($filename, $html);
	}

	public function pdf_from_url($url)
	{
		$id = $this->make_id($url);
		$filename = $this->dir . $this->cache . $id . '.pdf';

		$result = exec($this->get_cmd($url, $filename), $result_arr, $return_var);

		return file_get_contents($filename);
	}

	public function pdf_from_html($html)
	{

		$id = $this->make_id($html);
		$this->save_html($html);

		$url = $this->dir . 'html/' . $id . '.htm';
		$filename = $this->dir . $this->cache . $id . '.pdf';

		$result = exec($this->get_cmd($url, $filename), $result_arr, $return_var);

		return file_get_contents($filename);
	}

	public function rtf_from_url($url)
	{
		$this->set_option('cp>url', '"' . $url . '"');
		$this->set_format('rtf');

		$result = exec($this->get_cmd(), $result_arr, $return_var);
	    return implode("\n", $result_arr);
	}

}