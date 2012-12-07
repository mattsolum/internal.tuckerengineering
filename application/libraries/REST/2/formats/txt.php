<?PHP
	
class ApiTxt
{
	public $mime = 'text';
	public $data = '';
	
	function __construct($data, $error)
	{	
		$str = $this->recurse($data);
		
		$str = str_replace('; ', ";\n", $str);
		$str = str_replace(':: ', "::\n", $str);
		
		$str = trim($str);
		
		$this->data = $str;
	}
	
	private function recurse($data)
	{
		$str = '';
		if(is_array($data))
		{
			foreach ($data as $key => $value) {
				$str .= "\n" . $this->recurse_txt($value);
			}
		}
		else @$str .= (string)$data;
		
		return $str;
	}
}