<ul id="flash_messages"> 
<?PHP
	foreach($messages AS $flash)
	{
?>
	<li class="flash_<?PHP echo($flash->get_type()); ?>">
		<a href="#" title="dismiss this message">
			<?PHP echo($flash->get_message()); ?>
		</a>
	</li>
<?PHP
	}
?>
</ul>