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
		$q .= $this->parse_query($search);

		if($query == FALSE)
		{
			return array();
		}

		$query = $this->CI->db->query($q);

		if($query->num_rows() > 0)
		{
			$result = array();

			foreach($query AS $row)
			{
				$search = new StructSearch();

				$search->id 			= $row->id;
				$search->type 			= $row->type;
				$search->title			= $row->title;
				$search->body			= $row->text;
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
				$keys = array_keys($value);

				$query .= 'AND ';
				$query .= preg_replace('/[^a-zA-Z_ ]/', '', $keys[0]);

				$test = trim(preg_replace('/[^a-zA-Z0-9 \.$@-]/', '', $value[$keys[0]]));

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
			$method = $operator['method'];
			return $this->$method($operator);
		}
		else
		{
			return $this->CI->Event->trigger('search_op_' . $operator['method'], $operator);
		}
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
	public function commit_handler($object)
	{
		$this->CI->load->helper('url');

		switch(get_class($object))
		{
			case 'StructClient':
				$search = $this->client_prepare($object);
				break;
			case 'StructJob':
				$search = $this->job_prepare($object);
				break;
			case 'StructProperty':
				$search = $this->property_prepare($object);
				break;
			default:
				return NULL;
		}

		$this->commit($search);
	}
	
	private function client_prepare($client)
	{
		$search = new StructSearch();

		$search->id 	= $client->id;
		$search->type 	= 'client';
		$search->title	= $client->name;
		$search->link	= site_url('clients/' . strtolower(str_replace(' ', '_', $client->name)));
		$search->body	= (string)$client;

		return $search;

	}

	private function job_prepare($job)
	{
		$search = new StructSearch();

		$search->id 	= $job->id;
		$search->type 	= 'job';
		$search->title	= 'Job #' . $job->id . ', ' . $job->service() . ', at ' . $job->location->number . ' ' . $job->location->route . ' for ' . $job->client->name;
		$search->link	= site_url('jobs/' . $job->id);
		$search->body	= (string)$job;

		return $search;
	}

	private function property_prepare($property)
	{
		$search = new StructSearch();

		$search->id 	= $property->id;
		$search->type 	= 'property';
		$search->title	= $property->number . ' ' . $property->route . ($property->subpremise != '')?', #' . $property->subpremise:'';
		$search->link	= site_url('properties/' . $property->id);
		$search->body	= (string)$property;

		return $search;
	}

	/**
	 * Callback for delete events
	 * 
	 * @param  int $object
	 * @return NULL
	 */
	public function delete_handler($object)
	{
		switch (get_class($object)) {
			case 'StructClient':
				$this->delete($object->id, 'client');
				break;
			case 'StructProperty':
				$this->delete($object->id, 'property');
				break;
			case 'StructJob':
				$this->delete($object->id, 'job');
				break;
			default:
				return NULL;
		}
	}
}