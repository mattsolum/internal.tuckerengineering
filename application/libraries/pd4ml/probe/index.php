<html>
<head>
<title>PD4ML PHP wrapper runtime environment test</title>
<style>
BODY { font-family: Tahoma; font-size: 10pt }
</style>
</head>
<body>
<p>PD4ML PHP wrapper runtime environment test</p> 
<?PHP
	$java = '"C:\Program Files (x86)\Java\jre7\bin\java.exe"';

	$testdoc = 2;

	echo "current directory: " . getcwd() . "<br>";
	if ( strpos(php_uname(), 'Windows' ) !== FALSE) {
		echo "platform: Windows";
	} else {
		echo "platform: UNIX-derived";
	}
	 	
	echo "<p>";
	
	$result = exec('echo Test passthru.');
    if ( $result == "" ) {
	    echo "PHP is in safe mode: cannot run Java... <font color=tomato>FATAL</font><p>
	    If you have full control over the server and your security policy allows that,<br>
	    change <font color=blue><tt>safe_mode=on</tt></font> to <font color=blue><tt>safe_mode=off</tt></font> in php.ini<p>
	    Otherwise the only solution is to use PD4ML CGI wrapper.";
	    
		$testdoc = 0;
    } else {
	    echo "passthru() execution enabled... OK";
    } 

    if ( $testdoc > 0 ) {
		echo "<br>";
    
	    $result = exec($java . '2>&1', $result_arr, $return_var);
	    var_dump($result_arr);
	    var_dump($return_var);
	    if ( $result == "" ) {
			echo "java executable is not in the default PATH... <font color=tomato>FATAL</font><p> 
			First, make sure JDK is installed. If JDK is there, probably you need to give full path to the java<br>
			 interpreter in the command lines (i.e. <font color=blue><tt>/usr/local/bin/java</tt></font>). In the case adjust \$java variable<br>
			 in index.php and other scripts correspondingly in order to proceed the probe.";
			$testdoc = 0;
	    } else {
			echo "java is present and runnable... OK";
	    } 

    	if ( $testdoc > 0 ) {
	    	echo "<br>";

			$problem = "";
			$myFile = "testfile.txt";

			$fh = fopen($myFile, 'w') or $problem = "cannot open file for write... WARNING";
	
			if ( $problem == 0 ) {
				$stringData = "dummy text\n";
				fwrite($fh, $stringData);
				fclose($fh);
		
				$fh = fopen($myFile, 'r') or $problem = "cannot open file to read... WARNING";
				$theData = fread($fh, 5);
				fclose($fh);
				if ( strlen($theData) != 5 ) {
					$problem = "file read problem... WARNING";
				}	

				unlink($myFile) or $problem = "cannot delete test file from the current directory... WARNING";
			}
		
	    	if ( $problem == "" ) {
				echo "current directory allows to read and write files... OK"; 
	    	} else {
				echo $problem;
				$testdoc = 1;
	    	}
    	} 
	
		echo "<p>";
	    
		if ( $testdoc > 0 ) {
			echo "Test: <font size=1>(<a href=\"xdemo.php\">PHP/HTML source</a>)</font><br>";
			echo "<a href=\"pdftest1a.php\">In-memory PDF generation</a><br>";
			echo "<a href=\"pdftest1.php\">In-memory PDF generation (open inline)</a><br>";
		}
		
		if ( $testdoc > 1 ) {
			echo "<a href=\"pdftest2.php\">PDF generation to a file</a><br>";
			echo "<a href=\"pdftest3.php\">PDF generation to a file (with TTF embedding)</a><br>";
		} 
	
		echo "<p>";
		
		if ( $testdoc != 0 ) {
			echo "Useful online info:<br>";
			echo "<a href=\"http://pd4ml.com/pxphp.htm\">PD4ML PHP wrapper description</a><br>";
			echo "<a href=\"http://pd4ml.com/pxhtml-to-pdf-command-line-tool.htm\">Pd4Cmd command-line parameters</a><br>";
			echo "<a href=\"http://pd4ml.com/pxhtml.htm\">HTML tags supported by PD4ML</a><br>";
			echo "<a href=\"http://pd4ml.com/pxcss.htm\">CSS properties supported by PD4ML</a><br>";
		} 
    }
?>
</body>
</html>