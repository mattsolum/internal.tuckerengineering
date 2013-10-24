<html>
<head>
<style>
BODY { font-family: Tahoma; font-size: 10pt }
</style>
</head>
<body>
PD4ML HTML conversion demo: embed TTFs and write PDF to a file (with debug=on)<p> 
<pre>
<?PHP
	$evaluation = 1;
	$java = '"C:\Program Files (x86)\Java\jre7\bin\java.exe"';
	$url = getUrlBase() . "xdemocjk.php";
	$fontsdir = "../fonts/";
	
	if ( $evaluation == 1 ) {
		$jar = "../pd4ml_demo.jar";
	} else {
		$jar = "../pd4ml.jar";
	}
	
    $pdfname = uniquename();

    echo "file: $pdfname<p>";
    
	if ( strpos(php_uname(), 'Windows' ) !== FALSE) { 
		// server platform: Windows
		$fontsdir = preg_replace('/\//', "\\", $fontsdir);
		$jar = preg_replace('/\//', "\\", $jar);
		$cmdline = "$java -Xmx512m -cp $jar Pd4Cmd \"$url\" 800 A4 -debug -ttf $fontsdir -out $pdfname";
	} else {
		// server platform: UNIX-derived
		$cmdline = "$java -Xmx512m -Djava.awt.headless=true -cp $jar Pd4Cmd \"$url\" 800 A4 -debug -ttf $fontsdir -out $pdfname 2>&1";
	}

	if (!file_exists($fontsdir . "pd4fonts.properties")) {
		index_fonts($java, $jar, $fontsdir);
	}
	
	echo "Command line:<br>$cmdline<p>";
	// see for more command-line parameters: http://pd4ml.com/html-to-pdf-command-line-tool.htm
	
	echo "Debug output:<br>";
	passthru( $cmdline );
	
	// utility finctions
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
	
	function uniquename() {
		$prefix = "pd4ml_";
		$suffix = ".pdf";
		
		$dir_handle = @opendir(".") or die("Unable to open $path");

		$max = 0;
		while ($file = readdir($dir_handle)) {
			preg_match("/^$prefix?(.*?)$suffix$/", $file, $treffer);
			$id = $treffer[1];
			if ( $id != "" && $id > $max ) {
				$max = $id;
			}		
		}
		closedir($dir_handle);

		return $prefix . ($max + 1) . $suffix;
	}
	
	function index_fonts($java, $jar, $fontpath) {
		echo "reindex fonts... ";
		if ( strpos(php_uname(), 'Windows' ) !== FALSE) { 
			// server platform: Windows
			exec("$java -Xmx512m -jar $jar -configure.fonts $fontpath" );
		} else {
			// server platform: UNIX-derived
			exec("$java -Xmx512m -Djava.awt.headless=true -jar $jar -configure.fonts $fontpath" );
			echo "$java -Xmx512m -Djava.awt.headless=true -jar $jar -configure.fonts $fontpath<br>";
		}
		echo "done.<p>";
	}

	// the following calls are needed only for "Note" below 
	chdir("..");
	chdir("fonts");
    $fonts = getcwd();
?>
</pre>
<a href="<?=$pdfname?>">Open PDF</a><p>
<a href="deletetmp.php">Delete temporal PDF files</a><p>

Note: the script uses a font from <?=$fonts?><br> 
To add more fonts, copy them to the fonts/ directory and delete<br>
pd4fonts.properties (font name-file mappign) to let PD4ML generate a new one.
<p>
More info about <a href="http://pd4ml.com/support/pd4ml-html-css-pdf-tips-tricks-f7/ttf-embedding-t42.html">TTF embedding</a><br>

</body>
</html>	
