<?PHP

class SearchAPI extends PrototypeAPI
{
	public function __construct(&$API)
	{
		parent::__construct($API);
	}

	public function get()
	{
		$this->CI->load->model('Search');
		$search = trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', urldecode($this->API->id)));
		$results = $this->CI->Search->find($search);

		return $results;
	}
}