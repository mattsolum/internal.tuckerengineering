<?PHP $this->load->view('sections/header'); ?>

			<section id="appointmentHeader">
				<h1>New job</h1>
				<img src="<?=BASE_URL?>resources/img/maps/9801wparmerln.png" alt="" />
				<h2>9801 Stratford Rd</h2>
				<nav class="three">
					<ul>
						<li>
							<a href="#">Schedule</a>
						</li>
						<li>
							<a href="#">Location</a>
						</li>
						<li>
							<a href="#" class="currentStep">Client</a>
						</li>
						<li>
							<a href="#">Checkout</a>
						</li>
					</ul>
				</nav>
			</section>

<?PHP $this->load->view('forms/job.php'); ?>
	
<?PHP $this->load->view('sections/footer'); ?>