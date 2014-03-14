<?PHP $this->load->view('sections/header'); ?>
<?PHP $this->load->view('sections/second_level_nav', array('links' => $this->Navigation->build_job_tools($job->id))); ?>
<div id="body_wrapper">
	<h1 class="job"><?PHP echo(ucfirst(strtolower($job->service()))); ?><br />#<?PHP echo($job->id); ?></h1>
	<ul id="accounting">
		<li<?PHP if ($job->balance() < 0) echo(' class="negative_balance"'); ?>>
			<?PHP echo(($job->balance() < 0)?'- ':'+ '); echo('$' . number_format(abs($job->balance()), 2)); ?>
		</li>
		<li>
			<a href="<?PHP echo(site_url('jobs/apply_payment/' . $job->id)); ?>">Apply payment</a>
		</li>
		<li>
			<a href="<?PHP echo(site_url('jobs/refund/' . $job->id)); ?>">Refund</a>
		</li>
		<li>
			<a href="<?PHP echo(site_url('jobs/invoice/' . $job->id)); ?>">View invoice</a>
		</li>
	</ul>
	<address class="job"><a href="<?PHP echo(site_url('properties/' . $job->location->slug())); ?>"><?PHP echo($job->location->location_string()); ?></a></address>

	<section id="client_information">
		<h3>Client</h3>
		<h2 class="clear"><?PHP echo($job->client->name); ?> (<a href="<?PHP echo(site_url('clients/' . url_title($job->client->name, '_', TRUE))); ?>">view</a>)</h2>
		<h4><?PHP echo($job->client->location->location_string()); ?></h4>
		<?PHP 
		if(count($job->client->contact > 0))
		{
		?>
		<ul id="client_contact">
			<?PHP
			foreach($job->client->contact AS $key => $contact)
			{
			?>
			<li<?PHP if($key == count($job->client->contact) - 1) echo(' class="clear-after"'); ?>>
				<span class="contact_label"><?PHP echo($contact->type); ?></span>
				<?PHP echo($contact->info); ?>
			</li>
			<?PHP
			}
			?>
		</ul>
		<?PHP
		}
		?>
		<div class="clear"></div>
	</section>

	<?PHP if($job->requester->id != $job->client->id): ?>
	<section id="requester_information">
		<h3>Requester</h3>
		<h2 class="clear"><?PHP echo($job->requester->name); ?> (<a href="<?PHP echo(site_url('clients/' . url_title($job->requester->name, '_', TRUE))); ?>">view</a>)</h2>
		<?PHP 
		if(count($job->requester->contact > 0))
		{
		?>
		<ul id="client_contact">
			<?PHP
			foreach($job->requester->contact AS $key => $contact)
			{
			?>
			<li<?PHP if($key == count($job->requester->contact) - 1) echo(' class="clear-after"'); ?>>
				<span class="contact_label"><?PHP echo($contact->type); ?></span>
				<?PHP echo($contact->info); ?>
			</li>
			<?PHP
			}
			?>
		</ul>
		<?PHP
		}
		?>
		<div class="clear"></div>
	</section>
	<?PHP endif; ?>

	<?PHP if(count($job->assets) > 0): ?>
	<ul id="assets">
	<?PHP foreach($job->assets AS $asset): ?>
		<li>
			<a href="#"><?php echo($asset->filename); ?></a>
		</li>
	<?PHP endforeach; ?>
	</ul>
	<?PHP endif; ?>

	<?PHP $this->load->view('sections/accounting', array('accounting' => $job->accounting)); ?>

	<?PHP $this->load->view('sections/notes', array('notes' => $job->notes, 'uri' => 'notes/job/' . $job->id)); ?>
</div>
<?PHP $this->load->view('sections/footer') ?>