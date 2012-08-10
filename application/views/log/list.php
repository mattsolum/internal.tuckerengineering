<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		
		<link rel="stylesheet" type="text/css" href="resources/css/reset.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="resources/css/style.css" media="screen" />
		
	<title>View logs</title>

</head>
	<body>
		<?PHP if($type !== ''): ?><h1><?=$type?></h1><?PHP endif; ?>
		<table id="log">
			<?PHP foreach($entry as $log): ?>
				<?PHP $this->load->view('list', $log); ?>
			<?PHP end foreach; ?>
		</table>
	</body>
</html>