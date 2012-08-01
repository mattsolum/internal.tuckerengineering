function submitConfig(e)
{	
	if((e.target.tagName == 'INPUT' && (e.target.getAttribute('name') == 'name' || e.target.getAttribute('name') == 'value')) || e.target.tagName == 'A')
	{
		var id = $(e.target).parent().attr('rel');
		
		var name = $(e.target).parent().children("input[name='name']").val();
		var value = $(e.target).parent().children("input[name='value']").val();
		
		$.ajax({
			url: 'http://local/internal.tuckerengineering/api/v1/1234/config.json',
			type: 'POST',
			data: 'name=' + name + '&value=' + value,
			contentType: 'application/x-www-form-urlencoded'
		});
	}
}

function loadConfig()
{
	$('#config ul').load('http://local/internal.tuckerengineering/api/v1/1234/config/all.htm?view=forms%2Fconfig');
}

function deleteConfig(target)
{
	var li = $(target).parent();
	var currentTarget = li.next();
	
	for(var i = 0; i < li.nextAll().length; i++)
	{
		if(currentTarget.hasClass('odd'))
		{
			currentTarget.removeClass('odd');
		}
		else
		{
			currentTarget.addClass('odd');	
		}
		
		currentTarget = currentTarget.next();
	}
	
	li.hide('slow', function(){$(this).remove()});
}

function scrollbarWidth() {
    var div = $('<div style="width:50px;height:50px;overflow:hidden;position:absolute;top:-200px;left:-200px;"><div style="height:100px;"></div>');
    // Append our div, do our calculation and then remove it
    $('body').append(div);
    var w1 = $('div', div).innerWidth();
    div.css('overflow-y', 'scroll');
    var w2 = $('div', div).innerWidth();
    $(div).remove();
    return (w1 - w2);
}

function sizeNewItem()
{
	$("input[name='newName']").width($('#config ul input').width() + 'px');
	
	$("input[name='newValue']").width($('#newItem').width() - 160 - $("input[name='newName']").width() + scrollbarWidth() + 'px');
}

$(document).ready(function(){
	
	sizeNewItem();
	
	$('#config').keyup(function (e) {
		if(e.which == 13)
		{
			submitConfig(e);
		}
	});
	
	
	$("#config").click(function(e){
	
		if(e.target.tagName == 'A')
		{
			if(e.target.getAttribute('href') == '#delete')
			{
				var id = e.target.getAttribute('rel');
				
				$.ajax({
					url: 'http://local/internal.tuckerengineering/api/v1/1234/config/:' + id + '.json',
					type: 'DELETE',
					contentType: 'application/x-www-form-urlencoded'
				});
				
				deleteConfig(e.target);
			}
			else if (e.target.getAttribute('href') == '#save')
			{
				submitConfig(e);
			}
		}
	});
	
	$("button[name='addItem']").click(
		function(e){
			e.preventDefault();
			
			$.ajax({
				url: 'http://local/internal.tuckerengineering/api/v1/1234/config.json',
				type: 'POST',
				dataType: 'json',
				data: 'name=' + $("input[name='newName']").val() + '&value=' + $("input[name='newValue']").val(),
				success: loadConfig
			});
			
			$("input[name='newName']").val('');
			$("input[name='newValue']").val('');
			
		}
	);
	
	$(window).resize(sizeNewItem);
});