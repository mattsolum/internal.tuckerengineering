#!/usr/bin/perl -w
use CGI qw(:standard);
use Cwd;
use strict;

my $email = "fnord\@cgi101.com";
my $url = "http://www.cgi101.com";

print header;
print start_html("PD4ML Perl wrapper runtime environment test");
print <<EndHTML;
<html>
<head>
<style>
BODY { font-family: Tahoma; font-size: 10pt }
</style>
</head>
<body>
EndHTML

	my $java = "java";

	my $testdoc = 2;

	print "current directory: " . getcwd() . "<br>";
	my $osname = $^O;
	if( $osname eq 'MSWin32' ) {	
		print "platform: Windows";
	} else {
		print "platform: UNIX-derived";
	}
	 	
	print "<p>";
	
	my $result = `echo Test exec`;

	print "<p>";
	
    if ( $result eq "" ) {
	    print "Perl interpreter does not allow to run external applications: cannot run Java... <font color=tomato>FATAL</font><p>";
		$testdoc = 0;
    } else {
	    print "system() execution enabled... OK";
    } 

    if ( $testdoc > 0 ) {
		print "<br>";
    
	    $result = `$java`;
	    if ( $result eq "" ) {
			print "java executable is not in the default PATH... <font color=tomato>FATAL</font><p> 
			First, make sure JDK is installed. If JDK is there, probably you need to give full path to the java<br>
			 interpreter in the command lines (i.e. <font color=blue><tt>/usr/local/bin/java</tt></font>). In the case adjust \$java variable<br>
			 in probe.pl and other scripts correspondingly in order to proceed the probe.";
			$testdoc = 0;
	    } else {
			print "java is present and runnable... OK";
	    } 

     	if ( $testdoc > 0 ) {
	    	print "<br>";

			my $problem = "";
			my $myFile = "testfile.txt";

			open(MF, ">$myFile") or $problem = "cannot open file for write... WARNING";
	
			if ( $problem eq "" ) {
				my $stringData = "dummy text\n";
				my $readData = "";
				print MF $stringData;
				close(MF);
		
				my $n; 
				my $data;
				open(MF, $myFile) or $problem = "cannot open file to read... WARNING";
				while (($n = read MF, $data, 4) != 0) {
  					$readData .= $data;
				}
				close(MF);

				if( $readData eq "" ) {
					$problem = "file read problem... WARNING";
				}	

				unlink($myFile) or $problem = "cannot delete test file from the current directory... WARNING";
			}
		
	    	if ( $problem eq "" ) {
				print "current directory allows to read and write files... OK"; 
	    	} else {
				print $problem;
				$testdoc = 1;
	    	}
    	} 
	
		print "<p>";
	    
		if ( $testdoc > 0 ) {
			print "Test:<br>";
			print "<a href=\"pd4ml.pl?url=http://pd4ml.com\">PDF generation</a><br>";
		}
    }


print end_html;
