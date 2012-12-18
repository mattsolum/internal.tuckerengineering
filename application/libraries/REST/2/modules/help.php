<?PHP

class HelpAPI extends PrototypeAPI
{
	
	function __construct(&$API)
	{
		parent::__construct($API);
	}
	
	public function get()
	{
		$modules_directory = APPPATH . 'libraries/REST/2/modules/';
		
		$files = array_slice(scandir($modules_directory), 2);
		$help = array();
		
		foreach($files AS $file)
		{
			if(stristr($file, '.php'))
			{
				$type = ucfirst(str_replace('.php', '', $file));
				$classname = $type . 'API';
				
				if(!class_exists($classname))
				{
					include $modules_directory . $file;
				}
				
				if(class_exists($classname))
				{
					$methods = get_class_methods($classname);
					
					foreach ($methods as $method)
					{
						if(!strstr($method, '__') && $method != 'set')
						{
							preg_match('/(get|head|options|trace|connect|patch|post|put|delete)/i', $method, $matched);
							$method	= str_replace($matched[0], '', $method);
							
							$method = strtolower($type) . '/' . str_replace('_', '/', $method);
							
							if(!isset($help[$type]['method']))
							{
								$help[$type]['method'] = array();
							}
							
							$index = $this->get_index($help[$type]['method'], $method);
							
							if(!isset($help[$type]['method'][$index]))
							{
								$help[$type]['method'][$index] = array();
							}
							
							$help[$type]['method'][$index]['method_name'] = $method;
							$help[$type]['method'][$index]['request'][] = strtoupper($matched[0]);
						}
					}
				}
			}
		}
		
		return $help;
	}
	
	private function get_index($methods, $method_name)
	{
		foreach($methods AS $key => $method)
		{
			if($method['method_name'] == $method_name)
			{
				return $key;	
			}
		}
		
		return count($methods);
	}
	
}