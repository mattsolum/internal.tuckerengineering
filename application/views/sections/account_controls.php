<ul id="account_controls" class="nojs">
	<li id="user">
		<a href="<?PHP echo(site_url('user')); ?>"><?PHP echo($this->User->get_name()); ?></a>
		<ul>
			<?PHP
				foreach($this->Navigation->build_user_links() AS $title => $link)
				{
			?>
			<li>
				<a href="<?PHP echo(site_url($link)); ?>"><?PHP echo($title); ?></a>
			</li>
			<?PHP
				}
			?>
		</ul>
	</li>
	<li id="messages">
		<a href="<?PHP echo(site_url('user/messages')); ?>">0</a>
	</li>
</ul>