<?PHP
	
class ApiJson
{
	public $mime = 'application/json';
	public $data = '';
	
	function __construct($data, $error)
	{	
		$final = array();
		$final['result'] = ($error)?'error':'success';
		
		if(is_array($data) && isset($data['pagination']))
		{
			$final['pagination'] = $data['pagination'];
			unset($data['pagination']);
		}
		
		$final['data'] = $data;
		
		$this->data = json_encode($final);
	}
}