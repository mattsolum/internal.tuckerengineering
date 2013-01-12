<?PHP $this->load->view('sections/header'); ?>
<div id="body_wrapper">
	<h1>Create new job - step 3 of 3</h1>
	<?PHP $this->load->view('jobs/form', array('action' => 'jobs/create/final', 'job' => $job)); ?>
</div>
<?PHP $this->load->view('sections/footer') ?>
