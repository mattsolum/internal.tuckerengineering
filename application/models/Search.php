<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends CI_Model {
	private $CI = NULL;
	
	public function __construct()
	{
		parent::__construct();
		$this->CI =& get_instance();	
	}
	
	/**
	 * Creates or updates an entry
	 *
	 * @author Matthew Solum
	 * @param $search
	 * @return boolean
	 */
	public function commit($search)
	{
		if(get_class($search) != 'StructSearch' || !$search->is_valid())
		{
			log_message('error', 'Error in model Search method commit: object passed is invalid');
			return FALSE;
		}

		//code
		if($this->exists($search))
		{
			return $this->update($search);
		}
		else
		{
			return $this->create($search);
		}
	}
	
	/**
	 * Adds a new search
	 *
	 * @author Matthew Solum
	 * @param $search		
	 * @return FALSE on failure, search_id on success
	 */
	private function create($search)
	{
		$this->CI->db->trans_start();

		$data = array();
		$data['id']				= $search->id;
		$data['type']			= $search->type;
		$data['title']			= $search->title;
		$data['link']			= $search->link;
		$data['keywords'] 		= $search->body;
		$data['date_added']		= ($search->date_added != 0)?$search->date_added:now();
		$data['date_updated']	= ($search->date_updated != 0)?$search->date_updated:now();

		$this->CI->db->insert('search', $data);

		//End the transaction
		$this->CI->db->trans_complete();
		
		//Handle errors in the jobs transaction.
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in Search method create: transaction failed.');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	/**
	 * Reads a search given a search_id
	 *
	 * @author Matthew Solum
	 * @param $search_id
	 * @return Array
	 */
	private function read($id)
	{
		//code
	}
	
	/**
	 * Updates a search
	 *
	 * @author Matthew Solum
	 * @param $search
	 * @return bool
	 */
	private function update($search)
	{
		$this->CI->db->trans_start();

		$data = array();
		$data['title']			= $search->title;
		$data['link']			= $search->link;
		$data['keywords'] 		= $search->body;
		$data['date_updated']	= now();

		$this->db->where('id', $search->id);
		$this->db->where('type', $search->type);
		$this->CI->db->update('search', $data);

		//End the transaction
		$this->CI->db->trans_complete();
		
		//Handle errors in the jobs transaction.
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in Search method update: transaction failed.');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	/**
	 * Deletes a search given a search_id
	 *
	 * @author Matthew Solum
	 * @param $search_id
	 * @return bool
	 */
	public function delete($id, $type)
	{
		$this->db->trans_start();

		$where = array('id' => $id, $type => $type);

		$this->db->delete('search', $where);

		//End the transaction
		$this->CI->db->trans_complete();
		
		//Handle errors in the jobs transaction.
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in Search method delete: transaction failed.');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	/**
	 * Checks to see if a search exists
	 *
	 * @author Matthew Solum
	 * @param $search
	 * @return bool
	 */
	private function exists($search)
	{
		$where = array('id' => $search->id, 'type' => $search->type);

		$query = $this->db->get_where('search', $where);

		if($query->num_rows() > 0)
		{
			return TRUE;
		}

		return FALSE;
	}
	
	/**
	 * Full text search
	 *
	 * @author Matthew Solum
	 * @param $q
	 * @return Array of search_results
	 */
	public function find($search)
	{
		$q = $this->parse_query($search);

		if($q == FALSE)
		{
			return array();
		}

		$query = $this->CI->db->query($q);

		if($query->num_rows() > 0)
		{
			$result = array();

			foreach($query->result() AS $row)
			{
				$search = new StructSearch();

				$search->id 			= $row->id;
				$search->type 			= $row->type;
				$search->title			= $row->title;
				$search->body			= $row->keywords;
				$search->link			= $row->link;
				$search->date_added		= $row->date_added;
				$search->date_updated	= $row->date_updated;

				$result[] = $search;
				unset($search);
			}

			return $result;
		}

		return array();
	}

	/**
	 * Parses a user query into a MySQL query
	 * @param  string $query
	 * @return string
	 */
	private function parse_query($query)
	{
		$phrase_matches 	= array();
		$operator_matches 	= array();
		$negated_words		= array();

		$processed = array('like' => array(), 'notlike' => array(), 'fulltext' => array(), 'generic' => array());

		preg_match_all('/-?"(.*?)"/', $query, $phrase_matches);
		preg_match_all('/-?\[([a-z]+):(.*?)\]/', $query, $operator_matches);
		preg_match_all('/-([a-zA-Z0-9-]+)/', $query, $negated_words);


		foreach($phrase_matches[0] AS $key => $phrase)
		{	
			$like = $phrase_matches[1][$key];

			if(substr($phrase, 0, 1) == '-')
			{
				$processed['notlike'][] = array('keywords' => $like);
				$query = str_replace($phrase, '', $query);
			}
			else
			{
				$processed['like'][] = array('keywords' => $like);
			}
		}

		foreach($negated_words[0] AS $key => $word)
		{
			$processed['notlike'][] = array('keywords' => $negated_words[1][$key]);
			$query = str_replace($word, '', $query);
		}

		foreach($operator_matches[0] AS $key => $value)
		{
			$negated  = (substr($value, 0, 1) == '-');
			$operator = array('method' => $operator_matches[1][$key], 'data' => $operator_matches[2][$key], 'negated' => $negated);


			$processed['generic'][] = $this->process_operator($operator);

			$query = str_replace($value, '', $query);
		}

		$processed['fulltext'][] = trim($query);

		return $this->assemble_query($processed);
	}

	private function assemble_query($processed)
	{
		$query 		= '';
		$fulltext 	= '';

		$str = preg_replace('/[^a-zA-Z0-9 \.$@-]/', '', implode(' ', $processed['fulltext']));

		$fulltext .= 'MATCH (keywords) AGAINST ("';
		$fulltext .= $str;
		$fulltext .= '")';

		foreach($processed['like'] AS $value)
		{
			$keys = array_keys($value);

			$query .= 'AND ';
			$query .= preg_replace('/[^a-zA-Z_]/', '', $keys[0]);
			$query .= ' LIKE "%' . trim(preg_replace('/[^a-zA-Z0-9 \.$@-]/', '', $value[$keys[0]])) . '%" ';
		}

		foreach($processed['notlike'] AS $value)
		{
			$keys = array_keys($value);

			$query .= 'AND ';
			$query .= preg_replace('/[^a-zA-Z_]/', '', $keys[0]);
			$query .= ' NOT LIKE "%' . trim(preg_replace('/[^a-zA-Z0-9 \.$@-]/', '', $value[$keys[0]])) . '%" ';
		}

		foreach($processed['generic'] AS $value)
		{
			if($value != NULL && is_array($value))
			{
				$query .= 'AND ';
				$query .= preg_replace('/[^a-zA-Z_ !=<>]/', '', $value['left']);

				$test = trim(preg_replace('/[^a-zA-Z0-9 \.$@-]/', '', $value['right']));

				if(!is_numeric($test))
				{
					$test = '"' . $test . '"';
				}

				$query .= ' ' . $test . ' ';
			}
		}

		$order_by = '';

		if($str != '')
		{
			$query .= 'AND ' . $fulltext . ' > 0';
			$order_by = $fulltext .' DESC, ';
		}
		
		$query = preg_replace('/^AND/', '', $query);

		if($query == '')
		{
			return FALSE;
		}
		
		$query = 'WHERE ' . $query;
		$query = 'SELECT * FROM search ' . $query . ' ORDER BY ' . $order_by .'date_updated DESC';
		$query = preg_replace('/\s+/', ' ', $query);

		return trim($query);
	}

	private function process_operator($operator)
	{
		//It might be an internal method. Just make sure it is designated for this use.
		if(method_exists($this, 'op_' . $operator['method']))
		{
			$method = 'op_' . $operator['method'];
			return $this->$method($operator);
		}
		else
		{
			$result = array();
			$data = $this->CI->Event->trigger('search_op_' . $operator['method'], $operator);

			//Gotta deal with the way events return data.
			if($data != NULL)
			{
				//Each item is the data returned from an individual listener
				foreach($data AS $item)
				{
					//Lets make sure it is formatted correctly.
					if(is_array($item))
					{
						//If it just returned an array of left and right sides of a query
						//Set the result to itme
						if(isset($item['left']))
						{
							$result[] = $item;
						}
						else
						{
							//Otherwise it returned several pairs
							foreach($item AS $comparison)
							{
								$result[] = $comparison;
							}
						}
					}
				}
			}

			if(count($result) > 0)
			{
				return $result;
			}
		}
	}

	private function op_type($operator)
	{
		$query 	= array();
		$left	= 'type ';

		if($operator['negated'] == TRUE)
		{
			$left .= '!';
		}

		$left .= '=';

		$query['left'] 		= $left;
		$query['right'] 	= $operator['data'];

		return $query;
	}

	/**
	 * Callback for commit events.
	 *
	 * Formats a search object from the provided data based
	 * on its classname and passes it to the commit function.
	 * 
	 * @param  mixed $object
	 * @return NULL
	 */
	public function commit_handler($e)
	{
		//log_message('error', '--- Search->commit_handler() called ' . $e->event);
		$this->CI->load->helper('url');

		if($e->segment(-1) != 'delete')
		{
			switch($e->segment(0))
			{
				case 'client':
					$search = $this->client_prepare($e);
					break;
				case 'job':
					$search = $this->job_prepare($e);
					break;
				case 'property':
					$search = $this->property_prepare($e);
					break;
				default:
					return NULL;
			}
		}
		else
		{
			$this->delete_handler($e);
		}

		$this->commit($search);
	}
	
	private function client_prepare($e)
	{
		$this->load->model('Client');
		//log_message('error', '--- Search->client_prepare() called');
		$search = new StructSearch();

		if(is_object($e->data) && get_class($e->data) == 'StructClient')
		{
			$client = $this->CI->Client->get($e->data->id);
		}
		elseif(is_numeric($e->data) || is_string($e->data))
		{
			$client = $this->CI->Client->get($e->data);
		}

		$search->id 	= $client->id;
		$search->type 	= 'client';
		$search->title	= $client->name;
		$search->link	= site_url('clients/' . url_title($client->name, '_', TRUE));
		$search->body	= (string)$client;

		return $search;

	}

	private function job_prepare($e)
	{
		$this->load->model('Job');
		$search = new StructSearch();

		if(is_object($e->data) && get_class($e->data) == 'StructJob')
		{
			$job = $this->CI->Job->get($e->data->id);
		}
		elseif(is_numeric($e->data))
		{
			$job = $this->CI->Job->get($e->data);
		}

		$search->id 	= $e->data->id;
		$search->type 	= 'job';
		$search->title	= 'Job #' . $e->data->id . ', ' . $e->data->service() . ', at ' . $e->data->location->number . ' ' . $e->data->location->route . ' for ' . $e->data->client->name;
		$search->link	= site_url('jobs/' . $e->data->id);
		$search->body	= (string)$e->data;

		return $search;
	}

	private function property_prepare($e)
	{
		$this->load->model('Property');
		//log_message('error', '--- Search->property_prepare() called');
		$search = new StructSearch();

		if(is_object($e->data) && get_class($e->data) == 'StructProperty')
		{
			$property = $this->CI->Property->get($e->data->id);
		}
		elseif(is_numeric($e->data))
		{
			$property = $this->CI->Property->get($e->data);
		}

		$subpremise = ($property->subpremise != '')?', #' . $property->subpremise:'';
		$search->id 	= $property->id;
		$search->type 	= 'property';
		$search->title	= $property->number . ' ' . $property->route . $subpremise;
		$search->link	= site_url('properties/' . $property->id);
		$search->body 	= (string)$property;

		return $search;
	}

	/**
	 * Callback for delete events
	 * 
	 * @param  int $object
	 * @return NULL
	 */
	private function delete_handler($e)
	{
		switch (get_class($e->data)) {
			case 'StructClient':
				$this->delete($e->data->id, 'client');
				break;
			case 'StructProperty':
				$this->delete($e->data->id, 'property');
				break;
			case 'StructJob':
				$this->delete($e->data->id, 'job');
				break;
			default:
				return NULL;
		}
	}
}