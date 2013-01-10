<?PHP

class AutocompleteAPI extends PrototypeAPI
{
	public function __construct(&$API)
	{
		parent::__construct($API);
	}

	public function get() {
		$this->CI->load->model('Search');
		$search = trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', urldecode($this->API->id)));
		$results = array();

		if($search != '')
		{
			$this->CI->db->like('keywords', ' ' . $search);
			$query = $this->CI->db->get('search');

			if($query->num_rows() > 0)
			{
				foreach($query->result() AS $row)
				{
					$matches = array();
					preg_match('/(?<=\W|^)' . $search . '\w*(\W\w+)?/i', $row->keywords, $matches);

					if(isset($matches[0]) && !in_array(preg_replace('/[^a-zA-Z0-9 ]/', '', $matches[0]), $results))
					{
						$results[] = preg_replace('/[^a-zA-Z0-9 ]/', '', $matches[0]);
					}
				}

			}
		}

		usort($results, 'length_sort');
		return $results;
	}

	public function clients_get() {
		return $this->client_get();
	}

	public function client_get() {
		$search = trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', urldecode($this->API->id)));
		$results = array();

		if($search != '')
		{
			$this->CI->db->like('name', $search, 'after');
			$query = $this->CI->db->get('clients');

			if($query->num_rows() > 0)
			{
				foreach($query->result() AS $row)
				{
					$results[] = $row->name;
				}
			}
		}

		usort($results, 'length_sort');
		return array_reverse($results);
	}
}

function length_sort($a,$b){
    return strlen($b)-strlen($a);
}