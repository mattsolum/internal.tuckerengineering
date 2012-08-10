<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Error extends CI_Model {
	
	$CI = NULL;
	$_levels = array('ERROR' => '1', 'INFO' => '2', 'DEBUG' => '3', 'ALL' => '4');
	$_date_fmt = 'Y-m-d H:i:s';
	
	public function Error()
	{
		parent::__construct();
		$this->CI =& get_instance();
		
		$log_levels = $this->CI->config->item('log_levels');
		
		if(is_array($log_levels) && count($log_levels) > 0)
		{
			$this->_levels = $log_levels;
		}	
		
		$date_format = $this->CI->config->item('log_date_format');
		
		if ($date_format != '')
		{
			$this->_date_fmt = $date_format;
		}
	}
	
	public function get_log($id)
	{
		$query = $this->CI->db->get_where('log', array('log_id' => $id));
		
		if($query->num_rows() > 0)
		{
			$result = $query->row();
		
			$log['id'] 		= $result->log_id;
			$log['level'] 	= $result->level;
			$log['message']	= $result->message;
			$log['source']	= $result->source;
			$log['date'] 	= $result->date;
		}
		else 
		{
			log_message('error', "log_id '$id' not found.");
			
			return FALSE;
		}
	}
	
	//Works similarly to the Log library in that a level string is passed
	//And it will act on the level provided AND all the levels lower than it
	//But, you can also pass an array for level of the levels you want and it will
	//Select log entries exclusively by what was passed in the array
	public function get_logs($level = 'ALL', $start = 0, $limit = 50)
	{
		$select_levels = array();
	
		if(is_string($level))
		{
			$level = strtoupper($level);
			$levels = array_keys($this->_levels);
			
			if($this->_check_level($level))
			{
				for ($i = $this->_levels[$level]; $i > 1; $i--)
				{
					$select_levels[] = $levels[$i];
				}
			}
		}
		else if(is_array($level))
		{
			foreach($level as $level_name)
			{
				if($this->_check_level($level_name))
				{
					$select_levels[] = $level_name;
				}
			}
		}
		
		foreach($select_levels as $level_name)
		{
			$this->CI->db->or_where('level', $level_name);
		}
		
		$query = $this->CI->db->get('logs', $limit, $start);
		
		if($query->num_rows() > 0)
		{
			$logs = array();
			
			foreach($query->result_array() as $key => $row)
			{
				$logs[$key]['id'] 		= $row['log_id'];
				$logs[$key]['level'] 	= $row['level'];
				$logs[$key]['message'] 	= $row['message'];
				$logs[$key]['date'] 	= $row['date'];
			}
			
			return $logs;
		}
		else
		{
			log_message('error', "Error method get_logs failed. Parameters: level " . print_r($select_levels, true) . ", start $start, limit $limit.");
			
			return FALSE;
		}
	}
	
	//Level is a string
	private function _check_level($level)
	{
		if(isset(strtoupper($this->_levels[$level])))
		{
			return TRUE;
		}
		
		return FALSE;
	}
	
	public function add($level, $message)
	{
		$level = strtoupper($level);
		
		if($this->_check_level($level) && $this->_levels[$level] < $this->CI->config->item('log_threshold'))
		{
			$insert['level'] 	= $level;
			$insert['message'] 	= $message;
			$insert['date'] 	= date($this->_date_fmt);
			
			$this->CI->db->insert('logs', $insert);
			
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	public function delete($id)
	{
		$this->CI->db->delete('logs', array('log_id' => $id));
		
		return TRUE;
	}
}