<?PHP

class AutocompleteAPI extends PrototypeAPI
{
	public function __construct(&$API)
	{
		parent::__construct($API);
	}

	public function get() {
		$search = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', urldecode($this->API->id));
		$results = array();

		if($search != '')
		{
			$this->CI->db->like('title', $search, 'after');
			$query = $this->CI->db->get('search');

			if($query->num_rows() > 0)
			{
				foreach($query->result() AS $row)
				{
					$results[] = $row->title;
				}
			}
		}

		return $results;
	}
}