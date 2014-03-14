<?PHP $this->load->view('sections/header'); ?>
<div id="body_wrapper">
	<ul class="steps">
		<li class="current">
			<a href="#">Job</a>
		</li>
		<li>
			<a href="">Client</a>
		</li>
		<li>
			<a href="">Requester</a>
		</li>
	</ul>
	<h1>Create new job</h1>
	<?PHP $this->load->view('jobs/form', array('action' => 'jobs/create/client', 'job' => $job)); ?>
</div>
<?PHP $this->load->view('sections/footer') ?>
