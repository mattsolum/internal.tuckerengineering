<script src="<?=BASE_URL?>resources/js/jquery.js"></script>
<script src="<?=BASE_URL?>resources/js/jqueryUI.js"></script>
<script src="<?=BASE_URL?>resources/js/jquery.mousewheel.js"></script>
<script src="<?=BASE_URL?>resources/js/combobox.js"></script>
<script src="<?=BASE_URL?>resources/js/placeholderText.js"></script>
<script src="<?=BASE_URL?>resources/js/schedulePaneScrolling.js"></script>

<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>

<script type="text/javascript">
	var map;
	function initialize() {
		var myOptions = {
			zoom: 13,
			center: new google.maps.LatLng(30.2678, -97.7426),
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			panControl: false,
			mapTypeControl: false,
			streetViewControl: false
		};
		map = new google.maps.Map(document.getElementById('map_canvas'), myOptions);
	}
	
	google.maps.event.addDomListener(window, 'load', initialize);
</script>