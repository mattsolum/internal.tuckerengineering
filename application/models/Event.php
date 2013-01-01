<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Event extends CI_Model
{
	private $CI = NULL;
	
	private $listeners;
	
	public function Event()
	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->helper('structures/Event');
		$this->CI->load->helper('loader');
		
		$this->listeners = array();
	}	
	
	/**
	 * Triggers an event and passes data to the listener.
	 * 
	 * @param  str $event
	 * @param  mixed $data
	 * @return mixed
	 */
	public function trigger($event, &$data = NULL)
	{
		log_message('error', '--- Event->trigger() called for ' . $event);
		//Organize everything
		$event = $this->sanitize_event_name($event);
		
		$event_object = new StructEvent($event, $data);
		
		//Find any registered listeners
		//All events end in a full stop. 
		//The user or developer should never actualy run in to this,
		//but it is there so that we can match entire segments.

		$query = $this->CI->db->query("SELECT * FROM listeners WHERE '$event' LIKE CONCAT('%', event, '%')");
		
		if($query->num_rows() > 0)
		{
			$return = array();
			foreach($query->result() AS $listener)
			{
				log_message('error', '--- Event->trigger() calling listener ' . $listener->package . ' for ' . $listener->event);
				$item = $this->activate_listener($listener, $event_object);
				
				if($item != NULL)
				{
					$return[] = $item;
				}
			}

			if(count($return) > 0)
			{
				return $return;
			}
		}

		return NULL;
	}
	
	
	/**
	 * Registers an event listener.
	 * If $package is not set it will try to guess the package
	 * from the backtrace.
	 * 
	 * @param  str $event_name
	 * @param  str $callback
	 * @param  str $package = NULL
	 * @return VOID
	 */
	public function register($event_name, $callback, $package = NULL)
	{
		if($package != NULL)
		{
			$package_name = $this->sanitize_package_name($package);
		}
		else
		{
			$package_name = $this->parse_package_name();
		}
		
		$event_name = $this->sanitize_event_name($event_name);
		
		$callback = $this->sanitize_callback_name($callback);
		
		if(!$this->is_registered($event_name, $callback, $package_name))
		{
			//All events end in a full stop. 
			//The user or developer should never actualy run in to this,
			//but it is there so that we can match entire segments.
			$data = array('event' => $event_name, 'callback' => $callback, 'package' => $package_name);
			$this->CI->db->insert('listeners', $data);
		}
		
		//$this->listeners[$event_name][] = new StructListener($package_name, $extension, $callback);
	}
	
	/**
	 * Unregisters an event listener.
	 * If no arguments are passed it will try to guess the package
	 * based on the backtrace and unregister all listeners for that package.
	 * 
	 * @param  str $event_name 	= NULL
	 * @param  str $callback 	= NULL
	 * @param  str $package 	= NULL
	 * @return BOOL
	 */
	public function unregister($event_name = NULL, $callback = NULL, $package = NULL)
	{
		$where = array();
		//If everything is set to NULL it will guess the package based on the trackback
		//and then unregister ALL listeners for the pacakge.
		
		$where['package'] = ($package == NULL)?$this->parse_package_name():$this->sanitize_package_name($package);
		
		//All events end in a full stop. 
		//The user or developer should never actualy run in to this,
		//but it is there so that we can match entire segments.
		if($event_name != NULL) $where['event'] 	= $this->sanitize_event_name($event_name);
		if($callback != NULL) 	$where['callback'] 	= $this->sanitize_callback_name($callback);
		
		//DATABASE STUFF!
		//TRANSACTIONS!
		$this->CI->db->trans_start();
		
		$this->CI->db->delete('listeners', $where);
		
		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			//SOMETHING FAILED!
			//Wat do?
			return FALSE;
		}
		
		return TRUE;
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
	
	private function parse_package_name()
	{
		$trace = debug_backtrace();
		
		//Return the last segment of the URI
		//This does not account for models or extensions in subfolders
		//Also, it occurs to me that there is a security hole here.
		//Because something could presumably register a listener from
		//anywhere and run the code on another element.
		//Maybe we should add a check to make sure the request is at least coming from a relatively secure area of our directory structure?
		$last_segment = array_pop(explode('/', $trace[1]['file']));
		
		$package_name = substr($last_segment, 0, strpos($last_segment, '.'));
		
		return $this->sanitize_package_name($package_name);
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
			return $this->activate_extension($listener, $e);
		}
		else
		{
			return $this->activate_local($listener, $e);
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
			return $this->CI->Extension->$package->$callback($e);
		}

		return NULL;
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
			return $this->CI->$package->$callback($e);
		}

		return NULL;
	}
}