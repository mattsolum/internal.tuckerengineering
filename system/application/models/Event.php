<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Event extends Model
{
	private $CI = NULL;
	
	public function Event()
	{
		parent::__construct();
		$this->CI =& get_instance();	
	}	
	
	public function trigger_event($event, $data = NULL)
	{
		
	}
	
	public function register_listener($callback)
	{
		
	}
	
	private function load_package()
	{
		
	}
	
	private function get_listeners($event)
	{
		
	}
	
	private function create($event, $opts = NULL)
	{
		$data = array();
		
		//Read through options and parse them into the table
		if($opts != NULL)
		{
			if(isset($opts['register']))
			{
				$data['register'] = preg_replace('/[^a-zA-Z_]/', '', $opts['register']);
				
				unset($opts['register']);
			}
			
			if(isset($opts['user_id']))
			{
				$data['user_id'] = preg_replace('/[^0-9]/', '', $opts['user_id']);
				
				unset($opts['user_id']);
			}
			
			//If, after removing the two special keywords,
			//the options array still has any data
			//We parse through it as conditions
			if(count($opts) > 0)
			{
				$conditions = '';
				foreach($opts AS $key => $value)
				{
					$conditions .= preg_replace('/[^a-zA-Z_->]/', '', $key);
					
					if(preg_match('/^[<>=]+/', $value, $matched))
					{
						$conditions .= $matched[0];
						str_replace($matches[0], '', $value);
					}
					else $conditions .= '=';
					
					$conditions .= preg_replace('/[^a-zA-Z0-9_ /', '', $value);
					$conditions .= ';';
				}
				
				$data['conditions'] = $conditions;
				unset($conditions);
			}
		}
		
		$data['event_type'] = preg_replace('/[^a-zA-Z_]/', '', $event);
		
		
		//Begin transaction and place $data into table.
		$this->CI->db->trans_start();
		
		$this->CI->db->insert('event_listeners', $data);
		
		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in Event method create: transaction failed.');
			return FALSE;
		}
		
		return TRUE;
	}
	
	private function read($event)
	{
		
	}
	
	private function edit($event, $opts = NULL)
	{
	
	}
	
	private function delete()
	{
		
	}
	
}