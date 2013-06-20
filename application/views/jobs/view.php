<?PHP $this->load->view('sections/header'); ?>
<?PHP $this->load->view('sections/second_level_nav', array('links' => $this->Navigation->build_job_tools($job->id))); ?>
<div id="body_wrapper">
	<h1 class="client">#<?PHP echo($job->id); ?> <?PHP echo($job->service()); ?></h1>
	<div id="balance"<?PHP if ($job->balance() < 0) echo(' class="negative_balance"'); ?>>
		<?PHP echo('$' . number_format($job->balance(), 2)); ?>
	</div>
	<address class="job"><?PHP echo($job->location->location_string()); ?></address>
</div>
<?PHP $this->load->view('sections/footer') ?>