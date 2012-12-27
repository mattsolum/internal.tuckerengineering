<nav id="second_level_nav">
	<ul>
		<?PHP
			foreach($links AS $title => $link)
			{
		?>
		<li<?PHP if($this->Navigation->here($link)) echo(' class="here"'); ?>>
			<a href="<?PHP echo(site_url($link)); ?>"><?PHP echo($title); ?></a>
		</li>
		<?PHP
			}
		?>
	</ul>
</nav>