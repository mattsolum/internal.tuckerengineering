<html>
<head>
<style>
BODY { font-family: Tahoma; font-size: 10pt }
</style>
</head>
<body>
Deleting temporarily created pd4ml*.pdf files<p> 
<?PHP
		$prefix = "pd4ml";
		$suffix = ".pdf";
		
		$dir_handle = @opendir(".") or die("Unable to open $path");

		$max = 0;
		while ($file = readdir($dir_handle)) {
			preg_match("/^$prefix?(.*?)$suffix$/", $file, $treffer);
			$id = $treffer[1];
			if ( $id != "" ) {
				echo( $file . "... OK<br>" );
				unlink($file);
			}		
		}
		closedir($dir_handle);
?>
</body>
</html>	
