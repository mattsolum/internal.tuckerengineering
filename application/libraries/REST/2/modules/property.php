<?PHP

class PropertyAPI extends PrototypeAPI
{
	
	function __construct()
	{
		parent::__construct();
		$this->CI->load->model('Map');
		$this->CI->load->model('Property');
		$this->CI->load->helper('loader');
	}
	
	public function get($data)
	{ 
		if($data->id != '' && preg_match('/^[0-9]+$/', $data->id))
		{
			//If the ID is a number
			$property = $this->CI->Property->get($data->id);
		}
		else if ($data->id != '')
		{
			$property = new StructProperty();
			
			$property->location = $this->CI->Map->parse_address($data->id);
			
			$id = $this->CI->Property->exists($property);
			if($id !== FALSE)
			{
				$property = $this->CI->Property->get($id);
			}
			else
			{
				$property = FALSE;
			}
		}
		
		if(isset($property) && $property != FALSE)
		{
			return $property;
		}
		else 
		{	
			$this->error = 'No property exists with the id \'' . $this->id . '\'.';
			return FALSE;
		}
	}
}