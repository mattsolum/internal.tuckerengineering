<?PHP $this->load->view('sections/header'); ?>
<script type="text/javascript">
$(document).ready(function(){
	$(window).click(function(e){
		$.fn.MSDebug('Mouse!');
	});
});
</script>
<?PHP $this->load->view('sections/footer'); ?>