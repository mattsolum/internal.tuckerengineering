<?PHP
	$client_slug = url_title($client->name, '_', TRUE);
?>

<?PHP $this->load->view('sections/header'); ?>
<?PHP $this->load->view('sections/second_level_nav', array('links' => $this->Navigation->build_client_tools($client_slug))); ?>
<div id="body_wrapper">
	<h1 class="client"><?PHP echo($client->name); ?></h1>
	<div id="balance"<?PHP if ($client->balance < 0) echo(' class="negative_balance"'); ?>>
		<?PHP echo('$' . number_format($client->balance, 2)); ?>
	</div>
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
			<?PHP echo(str_replace('@', '<br />@', $contact->info)); ?>
		</li>
		<?PHP
		}
		?>
	</ul>
	<?PHP
	}
	?>
	<?PHP $this->load->view('sections/notes', array('notes' => $client->notes, 'uri' => 'notes/client/' . $client_slug)); ?>
	<div class="clear"></div>
</div>
<?PHP $this->load->view('sections/footer') ?>