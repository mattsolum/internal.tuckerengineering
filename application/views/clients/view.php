<?PHP
	$client_slug = url_title($client->name, '_', TRUE);
?>

<?PHP $this->load->view('sections/header'); ?>
<?PHP $this->load->view('sections/second_level_nav', array('links' => $this->Navigation->build_client_tools($client_slug))); ?>
<div id="body_wrapper">
	<h1 class="client"><?PHP echo($client->name); ?></h1>
	<ul id="accounting">
		<li<?PHP if ($client->balance < 0) echo(' class="negative_balance"'); ?>>
			<?PHP echo(($client->balance < 0)?'- ':'+ '); echo('$' . number_format(abs($client->balance), 2)); ?>
		</li>
		<li>
			<a href="<?PHP echo(site_url('clients/apply_payment/' . $client_slug)); ?>">Apply payment</a>
		</li>
		<li>
			<a href="<?PHP echo(site_url('clients/make_invoice/' . $client_slug)); ?>">Make invoice</a>
		</li>
	</ul>
	
	<address class="client"><?PHP echo($client->location->location_string()) ?></address>
	
	<?PHP 
	if(count($client->contact > 0))
	{
	?>
	<ul id="client_contact">
		<?PHP
		foreach($client->contact AS $key => $contact)
		{
		?>
		<li<?PHP if($key == count($client->contact) - 1) echo(' class="clear-after"'); ?>>
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
	<table id="list">
		<thead>
			<tr>
				
			</tr>
		</thead>
		<tbody class="show_limited">
			<?PHP 
				foreach($jobs AS $key => $job)
				{
			?>
			<tr<?PHP if($key % 2 == 1) echo(' class="odd"'); ?>>
				<td>
					<a href="<?PHP echo(base_url() . 'jobs/' . $job->id); ?>">#<?PHP echo($job->id); ?></a>
				</td>
				<td>
					<a href="<?PHP echo(base_url() . 'jobs/' . $job->id); ?>"><?PHP echo($job->service() . ' at ' . $job->location->number . ' ' . $job->location->route); ?></a>
				</td>
				<td>
					<a href="<?PHP echo(base_url() . 'jobs/' . $job->id); ?>">$<?PHP echo(number_format($job->balance(),2)); ?></a>
				</td>
			</tr>
			<?PHP
				}
			?>
		</tbody>
	</table>
	<a href="#" id="showAllButton" class="<?PHP if($num_jobs <= 10) echo('deactivate'); ?>"><?PHP echo(($num_jobs <= 10)?'Showing all ':'Show all ' . $num_jobs); ?> jobs</a>
	<?PHP $this->load->view('sections/notes', array('notes' => $client->notes, 'uri' => 'notes/client/' . $client_slug)); ?>
	<div class="clear"></div>
</div>
<?PHP $this->load->view('sections/footer') ?>