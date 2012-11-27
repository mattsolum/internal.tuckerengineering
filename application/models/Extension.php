<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Extension extends CI_Model
{
	private $CI = NULL;
	private $location = NULL;
	
	public function Extension()
	{
		parent::__construct();
		$this->CI =& get_instance();
		
		$this->location = APPPATH . setting('application.extensions.location');
		
		//Load activated extensions
		$files = scandir($this->location);
		
		foreach($files AS $file)
		{
			if(!strstr($file, '.'))
			{
				$this->load_extension($file);	
			}
		}
	}
	
	private function load_extension($package_name)
	{
		if(file_exists($this->location . $package_name .'/' . $package_name . '.php'))
		{
			include($this->location . $package_name .'/' . $package_name . '.php');
			$this->$package_name = new $package_name();
		}
	}
	
	public function activate($package_name)
	{
		if(file_exists($this->location . $package_name . '.zip'))
		{
			$zip = new ZipArchive;     
			$res = $zip->open($this->location . $package_name . '.zip');
			if ($res === TRUE) 
			{
				mkdir($this->location . $package_name . '/');
				$zip->extractTo($this->location . $package_name . '/');
				$zip->close();
				
				$this->load_extension($package_name);
				$this->$package_name->install();
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			return FALSE;	
		}
		//From Marty on internoetics.com
	}
	
	public function deactivate($package_name)
	{
		//The zip file for an extension is never deleted, so
		//to deactive an extension just delete the extension folder
		$this->$package_name->uninstall();
		return $this->rmdirr($this->location . $package_name . '/');
	}
	
	/**
	* Delete a file, or a folder and its contents
	*
	* @author Aidan Lister <aidan@php.net>
	* @version 1.0.2
	* @param string $dirname Directory to delete
	* @return bool Returns TRUE on success, FALSE on failure
	*/
	private function rmdirr($dirname)
	{
		// Sanity check
		if (!file_exists($dirname))
		{
			return false;
		}
		
		// Simple delete for a file
		if (is_file($dirname))
		{
			return unlink($dirname);
		}
		
		// Loop through the folder
		$dir = dir($dirname);
		while (false !== $entry = $dir->read())
		{
			// Skip pointers
			if ($entry == '.' || $entry == '..')
			{
				continue;
			}
		
			// Recurse
			$this->rmdirr("$dirname/$entry");
		}
		
		// Clean up
		$dir->close();
		return rmdir($dirname);
	}
	
	public function validate($package_name)
	{
		//Whether or not the extension
		//was activated when we began the check
		$activated = TRUE;
		
		//If something goes wrong it is set to false
		$valid = TRUE;
		
		if(!is_dir($this->location . $package_name .'/'))
		{
			if(!file_exists($this->location . $package_name . '.zip'))
			{
				$valid = FALSE;
			}
			else
			{
				//The extension exists, it just wasn't active
				$activated = FALSE;
				$this->activate($package_name);
				
				//Inside the extension directory, does the main file exist?
				if(!file_exists($this->location . $package_name .'/' . $package_name . '.php'))
				{
					$valid = FALSE;
				}
				else 
				{
					include($this->location . $package_name .'/' . $package_name . '.php');
					
					//The file exists, does the classname exist?	
					if(!class_exists($package_name)) $valid = FALSE;
				}
			}
		}
		
		//Put the extension back the way we found it.
		if($activated == FALSE) $this->deactivate($package_name);
		
		return $valid;
	}
}