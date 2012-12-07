<?PHP
	
class ApiJson
{
	public $mime = 'application/json';
	private $API;
		
	function __construct(&$API)
	{	
		$this->API =& $API;
	}

	public function format($data, $error)
	{	
		$final = array();
		$final['result'] = ($error)?'error':'success';
		
		if(is_array($data) && isset($data['pagination']))
		{
			$final['pagination'] = $data['pagination'];
			unset($data['pagination']);
		}
		
		$final['data'] = $data;
		
		return json_encode($final);
	}
}