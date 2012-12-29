<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>TE Internal</title>

		<script src="<?PHP echo base_url(); ?>resources/js/jquery.js" type="text/javascript"></script>
		<script src="<?PHP echo base_url(); ?>resources/js/jquery.formLabels1.0.js" type="text/javascript"></script>
		<script type="text/javascript">
		$(function(){
			$.fn.formLabels();
		});

		$(document).ready(function(){
			$('#account_controls').removeClass('nojs');

			$('#user').click(function(e){
				if($(e.target).attr('id') == 'aculink')
				{
					e.preventDefault();

					$(this).toggleClass('show');
				}

				e.stopPropagation();
			});

			$('html').click(function(e){
				$('#user').removeClass('show');
			});
		});
		</script>

		<link rel="stylesheet" href="<?PHP echo base_url(); ?>resources/css/reset.css">
		<link rel="stylesheet" href="<?PHP echo base_url(); ?>resources/css/style.css">
	</head>
	<body>
		<header>
			<?PHP $this->load->view('sections/top_level_nav'); ?>
			<?PHP $this->load->view('sections/account_controls'); ?>
			<?PHP $this->load->view('sections/search_bar'); ?>
		</header>