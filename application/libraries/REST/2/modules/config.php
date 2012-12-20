<?PHP

class ConfigAPI extends PrototypeAPI
{
	
	function __construct(&$API)
	{
		parent::__construct($API);
		
		$this->CI->load->model('Setting');
	}
	
	public function post() 
	{
		$data['name'] = ($this->API->id)?$this->API->id:strtolower($this->CI->input->post('name'));
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
	
	public function put()
	{
		return $this->post();
	}
	
	public function get() 
	{
		$items = $this->CI->Setting->filter($this->API->id);
		
		if($items !== FALSE)
		{
			return $items;
		}
		else
		{
			$this->error = "No items found like '" . $this->API->id . "'.";
			return FALSE;	
		}
	}
	
	public function delete() 
	{
		if($this->CI->Setting->delete($this->API->id))
		{
			return TRUE;
		}
		else return FALSE;
	}
}