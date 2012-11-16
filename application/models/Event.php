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
	
	public function trigger($event, &$data = NULL)
	{
		//Organize everything
		$event = $this->sanitize_event_name($event);
		
		$event_object = new StructEvent($event, $data);
		
		//Find any registered listeners
		if(isset($this->listeners[$event]))
		{
			foreach($this->listeners[$event] AS $listener)
			{
				$this->activate_listener($listener, $event_object);
			}
		}
	}
	
	public function register($event_name, $callback, $custom_var = NULL)
	{
		$trace = debug_backtrace();
		
		$package_name = array();
		$extension = NULL;
		
		if(strstr($trace[0]['file'], 'extensions'))
		{
			preg_match('/(?<=extensions\/)[a-zA-Z0-9_]+?(\/)/', $trace[0]['file'], $package_name);
			$extension = TRUE;
		}
		else
		{
			preg_match('/(?<=models\/)[a-zA-Z0-9_]+?(\/|.)/', $trace[0]['file'], $package_name);	
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
	
	private function activate_listener($listener, $e)
	{
		//Add any data the listener wanted handed back
		$e->custom = $listener->custom_var;
			
		//Check if it is an extension or an internal class
		//And then ship it off to the right place.
		if($listener->extension == TRUE)
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
		
		//In order to register an event listener the model has to be loaded.
		if(isset($this->CI->$package) && method_exists($this->CI->$package, $callback))
		{
			$this->CI->$package->$callback($e);
		}
	}
}