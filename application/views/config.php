<?PHP $this->load->view('sections/header'); ?>
<?PHP $this->load->view('admin_nav'); ?>
<div class="config">
	<h1>Name</h1>
	<h1>Value</h1>
</div>

<script src="<?PHP echo(BASE_URL) ?>resources/js/config_edit.js"></script>

<form method="POST" action="#" id="config">
<fieldset id="newItem">
	<input type="text" name="newName" /><input type="text" name="newValue" /><button name="addItem" value="addItem">Add Item</button>
</fieldset>
<ul>
<?PHP $this->load->view('forms/config', array('config' => $results)) ?>
</ul>
</form>

<?PHP $this->load->view('sections/footer'); ?>