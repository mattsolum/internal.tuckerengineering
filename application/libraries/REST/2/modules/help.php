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
							$method = preg_replace_callback(
												'/(get|post|put|delete)/', 
												create_function(
												            // single quotes are essential here,
												            // or alternative escape all $ as \$
												            '$matches',
												            'return \' [\' . strtoupper($matches[1]) . \']\';'
												        ), 
												$method
											);
							
							
						
							$help[$type]['method'][] = trim(str_replace('_', '/', $method));
						}
					}
				}
			}
		}
		
		return $help;
	}
	
}