<ul id="flash_messages">
	<li class="show_all">
		<a href="#showall" title="Show all messages">
			<img src="<?PHP echo(site_url()); ?>resources/img/list.svg" alt="show all" />
		</a>
	</li>
<?PHP
	foreach($messages AS $flash)
	{
?>
	<li class="flash_<?PHP echo($flash->get_type()); ?>">
		<a href="#dismiss" title="dismiss this message">
			<?PHP echo($flash->get_message()); ?>
		</a>
	</li>
<?PHP
	}
?>
</ul>