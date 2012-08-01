$(document).ready(function(){
	$('#scheduleDeviders').mousewheel(function(event, delta) {
		var marginLeft = $(this).css("margin-left");
		var newMarginLeft = parseInt(marginLeft) + Math.ceil(delta * 5);
		var containerWidth = $('#scheduleContainer').width();
		
		if(newMarginLeft >= (-3855 + containerWidth) && newMarginLeft <= 2)
		{
			$(this).css("margin-left", newMarginLeft + "px");
			
			$('.ui-draggable-dragging').css("margin-left", 40 * Math.round( ( mousePositionX - $('#scheduleContainer').offset().left - newMarginLeft ) / 40 ) + "px");
		}
	});
	
	var scheduleScrollInterval = 0;
	var mousePositionX;
	var mousePositionY;
	
	$(document).mousemove(function(e) {
		mousePositionX = e.pageX;
		mousePositionY = e.pageY;
	});
	
	$('#scheduleContainer').hover(function(e) {
		scheduleScrollInterval = setInterval(scrollSchedule, 100);
	},
	function(e) {
		clearInterval(scheduleScrollInterval);
	});
	
	function scrollSchedule() {
		var schedule = $('#scheduleContainer');
		var panel = $('#scheduleDeviders');
		var offset = schedule.offset();
		var width = schedule.width();
		var containerWidth = schedule.width();
		
		var leftBoundary = offset.left;
		var rightBoundary = leftBoundary + width;
		
		var delta = 0;
		
		var scrollAreaWidth = 64;
		var scaleFactor = 0.75;
		
		if ((mousePositionX - leftBoundary) < scrollAreaWidth) {
			delta = (scrollAreaWidth - Math.floor(mousePositionX - leftBoundary)) * scaleFactor;
		} else if (rightBoundary - mousePositionX < scrollAreaWidth) {
			delta = (scrollAreaWidth - Math.floor(rightBoundary - mousePositionX)) * (-1 * scaleFactor);
		}
		
		var newMarginLeft = parseInt(panel.css('margin-left')) + delta;
		
		if(delta != 0 && newMarginLeft >= (-3855 + containerWidth) && newMarginLeft <= 2)
		{
			panel.css("margin-left", newMarginLeft + "px");
			$('.ui-draggable-dragging').css("margin-left", Math.round((parseInt($('.ui-draggable-dragging').css('margin-left')) - delta) / 40)*40 + "px");
		}
	}
});