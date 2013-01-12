<?PHP $this->load->view('sections/header'); ?>
<div id="body_wrapper">
	<h1>Create new job - step 2 of 3</h1>
	<?PHP $this->load->view('jobs/requester_form', array('action' => 'jobs/create/job', 'job' => $job)); ?>
</div>
<?PHP $this->load->view('sections/footer') ?>
