$(document).ready(function(){
	
	$('input[type="text"]').each(function(index){
		
		if($(this).attr('rel') != undefined)
		{
			var defaultText = $(this).attr("rel");
			
			$(this).attr("value", defaultText).addClass("blur");
			
			$(this).focus(function() {
				$(this).removeClass("blur");
				if($(this).val() == defaultText) $(this).val('');
			});
			
			$(this).blur(function() {
				if( ( $(this).val() == "" || $(this).val().toLowerCase() == defaultText.toLowerCase() ) && !$('.ui-menu').is(':visible') ) {
					$(this).addClass("blur").val(defaultText);
				}
			});
			
			$('label[for="' + $(this).attr("id") + '"]"').addClass("hide");
		}
	});
	
	$('ul.schedule li').draggable({
		grid:[40,49], 
		stack: "ul.schedule li"
	});
});