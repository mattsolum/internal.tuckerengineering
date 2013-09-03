<?PHP $this->load->view('sections/header'); ?>
<?PHP $this->load->view('sections/second_level_nav', array('links' => $this->Navigation->build_invoice_tools($invoice->slug()))); ?>
<div id="body_wrapper">
	<h1 class="invoice">Invoice # <?PHP echo($invoice->slug()); ?></h1>
	<ul id="accounting">
		<li<?PHP if ($invoice->balance() < 0) echo(' class="negative_balance"'); ?>>
			<?PHP echo(($invoice->balance() < 0)?'- ':'+ '); echo('$' . number_format(abs($invoice->balance()), 2)); ?>
		</li>
		<li>
			<a href="<?PHP echo(site_url('invoices/apply_payment/' . $invoice->slug())); ?>">Apply payment</a>
		</li>
	</ul>

	<section id="client_information">
		<h3>Client</h3>
		<h2 class="clear"><?PHP echo($invoice->client->name); ?> (<a href="<?PHP echo(site_url('clients/' . url_title($invoice->client->name, '_', TRUE))); ?>">view</a>)</h2>
		<h4><?PHP echo($invoice->client->location->location_string()); ?></h4>
		<?PHP 
		if(count($invoice->client->contact > 0))
		{
		?>
		<ul id="client_contact">
			<?PHP
			foreach($invoice->client->contact AS $key => $contact)
			{
			?>
			<li<?PHP if($key == count($invoice->client->contact) - 1) echo(' class="clear-after"'); ?>>
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

	<table id="list">
		<thead>
			<tr>
				
			</tr>
		</thead>
		<tbody class="show_limited">
			<?PHP 
				foreach($invoice->jobs AS $key => $job)
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
</div>
<?PHP $this->load->view('sections/footer') ?>