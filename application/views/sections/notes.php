<ul id="notes">
	<?PHP
		foreach($notes AS $key => $note)
		{
	?>
		<li>
			<span class="name"><?PHP echo($note->user->name); ?></span>
			<span class="text"><?PHP echo($note->text); ?></span>
		</li>
	<?PHP
		}
	?>
	<li class="add_note">
		<form method="POST" action="<?PHP echo(site_url($uri)); ?>">
			<input type="text" name="note" title="Add note" id="note_input" />
			<input type="submit" class="hide" />
		</form>
	</li>
</ul>