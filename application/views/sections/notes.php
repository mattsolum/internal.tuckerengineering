<ul id="notes">
	<?PHP
		$date_format = setting('application.date_format');
		foreach($notes AS $key => $note)
		{
	?>
		<li>
			<?PHP
			if($note->user->id == $this->User->get_user_id() || $this->User->delete_enabled('/notes'))
			{
			?>
			<a class="note_delete" href="<?PHP echo(site_url('notes/delete/' . $note->id)); ?>" title="Delete note"></a>
			<?PHP
			}
			?>
			<span class="name"><?PHP echo($note->user->name); ?></span>
			<span class="text"><?PHP echo($note->text); ?></span>
			<span class="date"><?PHP echo(date($date_format, gmt_to_local($note->date_added, 'UM6', date('I', $note->date_added)))); ?></span>
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