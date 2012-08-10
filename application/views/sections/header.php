<!DOCTYPE HTML>
<html>
<head>

	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		
		<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
		<link rel="stylesheet" type="text/css" href="<?=BASE_URL?>resources/css/parse.php?file=style.css" media="all" />
		
		<?PHP $this->load->view('sections/genericJSIncludes'); ?>
		    
	<title>internal.TuckerEngineering.net</title>

</head>
	<body>
		
		<header id="page_header">
			<nav>
				<ul>
					<li>
						<a href="#" class="here">Schedule</a>
					</li>
					<li>
						<a href="#">Clients</a>
					</li>
					<li>
						<a href="#">Jobs</a>
					</li>
					<li>
						<a href="#">Admin</a>
					</li>
				</ul>
			</nav>
			
			<section>
				<a class="account_controls" href="#">Matthew Solum</a>
				
				<form id="search" method="post" action="#">
					<label for="searchQuery">Search</label>
					<input type="text" id="searchQuery" name="searchQuery" rel="Search" />
					<input type="submit" value="search" />
				</form>
			</section>
		</header>
		
		