<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2009, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Logging Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Logging
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/general/errors.html
 */
class CI_Log {

	var $log_path;
	var $_threshold	= 1;
	var $_date_fmt	= 'Y-m-d H:i:s';
	var $_enabled	= TRUE;
	var $_levels	= array('ERROR' => '1', 'INFO' => '2', 'DEBUG' => '3', 'ALL' => '4');
	static $sql;

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function CI_Log()
	{	
		$config =& get_config();
		
		/*
		$this->log_path = ($config['log_path'] != '') ? $config['log_path'] : BASEPATH.'logs/';
		
		if ( ! is_dir($this->log_path) OR ! is_really_writable($this->log_path))
		{
			$this->_enabled = FALSE;
		}
		*/
		
		if (is_numeric($config['log_threshold']))
		{
			$this->_threshold = $config['log_threshold'];
		}
			
		if ($config['log_date_format'] != '')
		{
			$this->_date_fmt = $config['log_date_format'];
		}
		
		if(is_array($config['log_levels']) && count($config['log_levels']) > 0)
		{
			$this->_levels = $config['log_levels'];
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Write Log File
	 *
	 * Generally this function will be called using the global log_message() function
	 *
	 * @access	public
	 * @param	string	the error level
	 * @param	string	the error message
	 * @param	bool	whether the error is a native PHP error
	 * @return	bool
	 */		
	function write_log($level = 'error', $msg, $php_error = FALSE)
	{		
		
		if(!isset(self::$sql))
		{
			if ( file_exists(APPPATH.'config/config'.EXT) )
			{
				require_once(APPPATH.'config/database'.EXT);
				self::$sql = new PDO("{$db['default']['dbdriver']}:dbname={$db['default']['database']};host={$db['default']['hostname']}", $db['default']['username'], $db['default']['password']);
			}
		}
		
		
		if ($this->_enabled === FALSE)
		{
			return FALSE;
		}
	
		$level = strtoupper($level);
		
		if ( ! isset($this->_levels[$level]) OR ($this->_levels[$level] > $this->_threshold))
		{
			return FALSE;
		}
	
		/*
		$filepath = $this->log_path.'log-'.date('Y-m-d').EXT;
		$message  = '';
		
		if ( ! file_exists($filepath))
		{
			$message .= "<"."?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?".">\n\n";
		}
			
		if ( ! $fp = @fopen($filepath, FOPEN_WRITE_CREATE))
		{
			return FALSE;
		}

		$message .= $level.' '.(($level == 'INFO') ? ' -' : '-').' '.date($this->_date_fmt). ' --> '.$msg."\n";
		
		flock($fp, LOCK_EX);	
		fwrite($fp, $message);
		flock($fp, LOCK_UN);
		fclose($fp);
	
		@chmod($filepath, FILE_WRITE_MODE); 
		*/
		
		$msg = preg_replace('/[^a-zA-Z0-9 -_\.!]/', '', $msg);
		$source = preg_replace('/[^a-zA-Z0-9 -_\.\/]/', '', $_SERVER['REQUEST_URI']);
		
		$date = now();
		
		$query = self::$sql->query("INSERT INTO log (level, message, source, date) VALUES ('$level', '$msg', '$source', '$date')");
				
		return TRUE;
	}

}
// END Log Class

/* End of file Log.php */
/* Location: ./system/libraries/Log.php */