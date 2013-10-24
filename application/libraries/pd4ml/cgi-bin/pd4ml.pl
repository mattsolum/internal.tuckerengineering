#!/usr/bin/perl -w
use CGI qw(:standard);
use strict;

MAIN:
{
	my $evaluation = 1;
	my $inline = 1;
	my $url = param('url');
	my $java = "java";

	my $jar = "../pd4ml.jar";
	if ( $evaluation == 1 ) {
		$jar = "../pd4ml_demo.jar";
	}

	# HTTP header
    print "Content-type: application/pdf\n"; 
	print "Pragma: cache\n";
	print "Expires: 0\n";
	print "Cache-control: private\n";
	if ( $inline == 1 ) {
	    print "Content-disposition: inline\n"; 
	} else {
	    print "Content-disposition: attachment; filename=pdftest.pdf\n";
	}
	print "\n";
	$|++;
	
	# content
	my $cmdline = "";
	my $osname = $^O;
	if( $osname eq 'MSWin32' ) {	
		# server platform: Windows
		$jar =~ s/\//\\/g;
		$cmdline = "$java -Xmx512m -cp $jar Pd4Cmd \"$url\" 900 A4";
	} else {
		# server platform: UNIX-derived
		$cmdline = "$java -Xmx512m -Djava.awt.headless=true -cp $jar Pd4Cmd \'$url\' 900 A4";
	}
    # see for more command-line parameters: http://pd4ml.com/html-to-pdf-command-line-tool.htm

#	print debug output to error.log of the server
	print STDERR $cmdline . "\n"; 
	
	my $doc = `$cmdline`;
	binmode STDOUT;
    print $doc;
}   