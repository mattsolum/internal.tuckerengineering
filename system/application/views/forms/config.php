<?PHP foreach($config as $key => $result): ?>
	<li<?PHP if($key % 2 == 1) echo(' class="odd"'); ?> rel="<?PHP echo($result['name']); ?>">
		<input type="text" name="name" value="<?PHP echo($result['name']); ?>" />
		<input type="text" name="value" value="<?PHP echo($result['value']); if($result['note'] != '') echo("\t\t//" . $result['note']);?>" />
		<a href="#delete" class="delete" rel="<?PHP echo($result['name']); ?>" title="Delete <?PHP echo($result['name']); ?>">delete</a>
		<a href="#save" class="save" rel="<?PHP echo($result['name']); ?>" title="Save changes to <?PHP echo($result['name']); ?>">save</a>
	</li>
<?PHP endforeach; ?>