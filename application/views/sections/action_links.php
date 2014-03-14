<ul id="action_links">
	<?PHP
	foreach($links AS $title => $link)
	{
	?>
	<li class="<?PHP echo(url_title($title, '_', TRUE)); ?>">
		
		<a href="<?PHP echo(site_url($link)); ?>" title="<?PHP echo($title); ?>">
			<?PHP
				if(file_exists(APPPATH . '../resources/img/' . url_title($title, '_', TRUE) . '.svg'))
				{
					echo(file_get_contents(APPPATH . '../resources/img/' . url_title($title, '_', TRUE) . '.svg'));
				}
			?>
			<?PHP echo($title); ?>
		</a>

	</li>
	<?PHP
	}
	?>
	<li class="clear"></li>
</ul>