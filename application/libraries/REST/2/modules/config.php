<?PHP

class ConfigAPI extends PrototypeAPI
{
	
	function __construct()
	{
		parent::__construct();
		
		$this->CI->load->model('Setting');
	}
	
	public function post($data) 
	{
		$data['name'] = ($data->id)?$this->id:strtolower($this->CI->input->post('name'));
		$data['value'] = $this->CI->input->post('value');
		
		if($data['name'] && $data['value'])
		{
			$this->CI->Setting->set($data['name'], $data['value']);
			return TRUE;
		}
		else
		{
			$this->error = 'Name or value is missing.';
			return FALSE;
		}
	}
	
	public function put($data)
	{
		$this->post($data);
	}
	
	public function get($data) 
	{
		$items = $this->CI->Setting->filter($data->id);
		
		if($items !== FALSE)
		{
			return $items;
		}
		else
		{
			$this->error = "No items found like '" . $data->id . "'.";
			return FALSE;	
		}
	}
	
	public function delete() 
	{
		if($this->CI->Setting->delete($this->id))
		{
			return TRUE;
		}
		else return FALSE;
	}
}