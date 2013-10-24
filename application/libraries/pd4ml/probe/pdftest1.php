<?PHP
	$evaluation = 1;
	$java = '"C:\Program Files (x86)\Java\jre7\bin\java.exe"';
	$url = getUrlBase() . "xdemo.php";

	if ( $evaluation == 1 ) {
		$jar = "../pd4ml_demo.jar";
	} else {
		$jar = "../pd4ml.jar";
	}
	
	header("Pragma: cache");
	header("Expires: 0");
	header("Cache-control: private");
    header('Content-type: application/pdf'); 
    header('Content-disposition: inline'); 
 
	if ( strpos(php_uname(), 'Windows' ) !== FALSE) { 
		// server platform: Windows
		$jar = preg_replace('/\//', "\\", $jar);
		$cmdline = "$java -Xmx512m -cp $jar Pd4Cmd \"$url\" 800 A4";
	} else {
		// server platform: UNIX-derived
		$cmdline = "$java -Xmx512m -Djava.awt.headless=true -cp $jar Pd4Cmd \"$url\" 800 A4";
	}

	// see for more command-line parameters: http://pd4ml.com/html-to-pdf-command-line-tool.htm

	passthru( $cmdline );
	
	function getUrlBase() {
		$http = 'http';

		if($_SERVER["HTTPS"] == "on"){
			$http .= "s";
		}
		
		$url = $http . "://".$_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		
		$filename = explode("/", $url);
		
		$base = "";
		
		for( $i = 0; $i < (count($filename) - 1); ++$i ) {
			$base .= $filename[$i].'/';
		}	
		
		return $base; 
	}
?>