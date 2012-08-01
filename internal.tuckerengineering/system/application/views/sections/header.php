<!DOCTYPE HTML>
<html>
<head>

	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		
		<link rel="stylesheet" type="text/css" href="<?=BASE_URL?>resources/css/parse.php?file=style.css" media="all" />
		
		<script src="<?=BASE_URL?>resources/js/jquery.js"></script>
		
		<script>
			$(document).ready(function(){
				
				var defaultSearchText = "Search";
				
				$("#search label").addClass("hide");
				$("#searchQuery").attr("value", defaultSearchText).addClass("blur");
				
				$("#searchQuery").focus(function() {
					$(this).removeClass("blur");
					if($(this).attr("value") == defaultSearchText) $(this).attr("value","");
				});
				
				$("#searchQuery").blur(function() {
					$(this).addClass("blur");
					if($(this).attr("value") == "") $(this).attr("value", defaultSearchText);
				});
			});
		</script>
		
	<title>internal.TuckerEngineering.net</title>

</head>
	<body>
		
		<header>
			<nav>
				<ul>
					<li>
						<a href="<?PHP echo BASE_URL ?>">Home</a>
					</li>
					<li>
						<a href="#">Schedule</a>
					</li>
					<li>
						<a href="#">Clients</a>
					</li>
					<li>
						<a href="#" class="here">Jobs</a>
					</li>
				</ul>
			</nav>
			
			<section>
				logged in as <a href="#">Matthew Solum</a>
			</section>
			
			<form id="search" method="post">
				<label for="searchQuery">Search</label>
				<input type="text" id="searchQuery" name="searchQuery" />
				<input type="submit" value="search" />
			</form>
		</header>
		
		<article>
		
		