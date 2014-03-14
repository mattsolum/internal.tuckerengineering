<?PHP $this->load->view('sections/header'); ?>
<div id="body_wrapper">
	<ul class="steps">
		<li class="filled">
			<a href="<?PHP echo(site_url('jobs/create/job')); ?>">Job</a>
		</li>
		<li class="filled">
			<a href="<?PHP echo(site_url('jobs/create/client')); ?>">Client</a>
		</li>
		<li class="current">
			<a href="#">Requester</a>
		</li>
	</ul>
	<h1>Create new job</h1>
	<?PHP $this->load->view('jobs/requester_form', array('action' => 'jobs/create/final', 'job' => $job)); ?>
</div>
<?PHP $this->load->view('sections/footer') ?>
