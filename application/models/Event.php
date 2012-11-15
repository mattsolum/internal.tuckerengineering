<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Event extends CI_Model
{
	private $CI = NULL;
	
	private $listeners
	
	public function Event()
	{
		parent::__construct();
		$this->CI =& get_instance();
		
		$this->listeners = array();
		
		$this->CI->load->helper('structures/listener');
		$this->CI->load->helper('structures/event');
	}	
	
	public function trigger($event, $data = NULL)
	{
		$event = $this->sanitize_event_name($event);
		
		if(isset($this->listeners[$event]))
		{
			foreach($this->listeners[$event] AS $listener)
			{
				if($listener->extension == TRUE)
				{
					$package = $listener->package_name;
					$callback = $listener->callback;
					
					$event_object = new StructEvent($event, $data, $listener->custom_var);
					
					
					$this->CI->Extensions->$package->$callback($event_object);
				}
				else
				{
					
				}
			}
		}
	}
	
	public function register_listener($event_name, $callback, $custom_var = NULL)
	{
		$trace = debug_backtrace();
		
		$package_name = array();
		$extension = NULL;
		
		if(strstr($trace[1]['file'], 'extensions'))
		{
			preg_match('/(?=extensions|third_party\/)[a-zA-Z0-9_]+?(.|\/)/', $trace[1]['file'], $package_name);
			$extension = TRUE;
		}
		else
		{
			preg_match('/(?=models\/)[a-zA-Z0-9_]+?(.|\/)/', $trace[1]['file'], $package_name);	
			$extension = FALSE;
		}
		
		$package_name = $this->sanitize_package_name(substr($package_name[0], 0, strlen($package_name[0]) - 1));
		
		$event_name = $this->sanitize_event_name($event_name);
		
		$callback = $this->sanitize_callback_name($callback);
		
		$this->listeners[$event_name][] = new StructListener($package_name, $extension, $callback, $custom_var);
	}
	
	private function sanitize_event_name($name)
	{
		strtolower(preg_replace('/[^a-zA-Z_]/', '', $name));
		return $name;
	}	
	
	private function sanitize_package_name($name)
	{
		preg_replace('/[^a-zA-Z_]/', '', $name);
		return $name;
	}
	
	private function sanitize_callback_name($name)
	{
		preg_replace('/[^a-zA-Z_]/', '', $name);
		return $name;
	}
	
}