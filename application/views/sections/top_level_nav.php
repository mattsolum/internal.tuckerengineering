<nav id="top_level">
	<ul>
		<?PHP 
		foreach($this->Navigation->build_top_level() AS $title => $link)
		{
		?>
		<li<?PHP if($this->Navigation->here($link)){echo('class="here"');}?>>
			<a href="<?PHP echo($link); ?>" title="<?PHP echo($title); ?>"><?PHP echo($title); ?></a>
		</li>
		<?PHP
		}
		?>
	</ul>
</nav>