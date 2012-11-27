<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Event extends CI_Model
{
	private $CI = NULL;
	
	private $listeners;
	
	public function Event()
	{
		parent::__construct();
		$this->CI =& get_instance();
		
		$this->listeners = array();
		
		$this->CI->load->helper('structures/listener');
		$this->CI->load->helper('structures/event');
	}	
	
	//Changed this so that the "Event object" is JUST the data packet passed by the event triggerer. No other information passed.
	public function trigger($event, &$data = NULL)
	{
		//Organize everything
		$event = $this->sanitize_event_name($event);
		
		$event_object = $data;
		
		//Find any registered listeners
		if(isset($this->listeners[$event]))
		{
			foreach($this->listeners[$event] AS $listener)
			{
				$this->activate_listener($listener, $event_object);
			}
		}
	}
	
	
	public function register($event_name, $callback, $package = NULL)
	{
		$trace = debug_backtrace();
		
		//will eventualy hold results from a regex query
		$package_name = array();
		
		if($package != NULL)
		{
			$package_name = $this->sanitize_package_name($package);
		}
		else
		{
			//No longer necessary, should re-write a better REGEX expression to handle all cases.
			if(strstr($trace[0]['file'], 'extensions'))
			{
				preg_match('/(?<=extensions\/)[a-zA-Z0-9_]+?(\/)/', $trace[0]['file'], $package_name);
			}
			else
			{
				//This method is limited because it does not allow packages in sub-folders
				//Someone may want to work with this.
				//I'm not going to, I'm lazy.
				preg_match('/(?<=models\/)[a-zA-Z0-9_]+?(\/|.)/', $trace[0]['file'], $package_name);
			}
			
			$package_name = $this->sanitize_package_name(substr($package_name[0], 0, strlen($package_name[0]) - 1));
		}
		
		$event_name = $this->sanitize_event_name($event_name);
		
		$callback = $this->sanitize_callback_name($callback);
		
		$this->listeners[$event_name][] = new StructListener($package_name, $extension, $callback);
	}
	
	private function is_registered($event_name, $callback, $package)
	{
		$where = array('event' => $event_name, 'callback' => $callback, 'package' => $package);
		$query = $this->CI->db->get_where('listeners');
		
		if($query->num_rows() > 0)
		{
			return TRUE;
		}
		
		return FALSE;
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
	
	private function activate_listener($listener, $e)
	{		
		//Check if it is an extension or an internal class
		//And then ship it off to the right place.
		if(!model_exists($listener->package))
		{
			$this->activate_extension($listener, $e);
		}
		else
		{
			$this->activate_local($listener, $e);
		}
	}
	
	private function activate_extension($listener, $e)
	{
		//PHP doesn't like using properties as property or method names
		$package = $listener->package;
		$callback = $listener->callback;
		
		//The only way for an item to register itself as a listener is to be loaded and run...
		//So, the check that it is set is potentialy unecessary.
		//This could change if I add another method of registering an event listener.
		if(isset($this->CI->Extension->$package) && method_exists($this->CI->Extension->$package, $callback))
		{	
			$this->CI->Extension->$package->$callback($e);
		}
	}
	
	private function activate_local($listener, $e)
	{
		$package = $listener->package;
		$callback = $listener->callback;
		
		//Due to the check prior to this in activate_listener we will assume the file exists
		//This method of check, though, ignores the idea that the model may have been renamed
		//when it was loaded. Someone may want to make this handle that case.
		if(!isset($this->CI->$package))
		{
			$this->CI->load->model($package);
		}
		
		//In order to fail gracefully we check if the method actualy exists before calling it
		if(method_exists($this->CI->$package, $callback))
		{
			$this->CI->$package->$callback($e);
		}
	}
}