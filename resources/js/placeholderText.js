$(document).ready(function(){
	
	$('input').each(function(index){
		
		if($(this).attr('title') != undefined)
		{
			var defaultText = $(this).attr("title");
			
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
});