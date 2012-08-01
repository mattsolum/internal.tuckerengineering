$(document).ready(function(){
	checkAutocompletePosition = function(inputObj) {
		$('.ui-autocomplete').children('li').attr('style','');
		
		var offset = $('.ui-autocomplete').offset(),
			heightMenu = $('.ui-autocomplete').outerHeight();
			heightComp = inputObj.outerHeight(),
			spaceAbove = undefined,
			spaceBelow = undefined;
			
		$('.ui-autocomplete').attr('style','').width(inputObj.outerWidth() + $('.ui-button').outerWidth() - 2);
			
		//alert('offset: ' + offset.top + ', heightMenu: ' + heightMenu + ', heightComp: ' + heightComp); 
		
		if(offset.top + heightMenu > $(window).height() + $(window).scrollTop()) {
			// not enough room below component; check if above is better
			spaceBelow = $(window).height() + $(window).scrollTop() - offset.top;
			spaceAbove = inputObj.offset().top;
			
			if(spaceAbove > spaceBelow) {
				$('.ui-autocomplete').position({
				        "my": "left bottom",
				        "at": "left top",
				        "of": inputObj
				});
			}
		}
	};
	
	(function( $ ) {
		$.widget( "ui.combobox", {
			_create: function() {
				var self = this,
					select = this.element.hide(),
					selected = select.children( ":selected" ),
					value = selected.val() ? selected.text() : "";
				var input = this.input = $( "<input>" )
					.attr('rel', select.attr('rel'))
					.attr('type', 'text')
					.insertAfter( select )
					.val( value )
					.autocomplete({
						delay: 0,
						minLength: 0,
						source: function( request, response ) {
							var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
							response( select.children( "option" ).map(function() {
								var text = $( this ).text();
								if ( this.value && ( !request.term || matcher.test(text) ) )
									return {
										label: text.replace(
											new RegExp(
												"(?![^&;]+;)(?!<[^<>]*)(" +
												$.ui.autocomplete.escapeRegex(request.term) +
												")(?![^<>]*>)(?![^&;]+;)", "gi"
											), "<strong>$1</strong>" ),
										value: text,
										option: this
									};
							}) );
						},
						select: function( event, ui ) {
							ui.item.option.selected = true;
							self._trigger( "selected", event, {
								item: ui.item.option
							});
						},
						open: function( event, ui ) {
							input.data('hold', 'true');
							
							// open function is called before autocomplete menu is displayed, 
							// so use timeout of 0 trick to let autocomplete finish 
							setTimeout(function() { checkAutocompletePosition(input); }, 0);
						},
						change: function( event, ui ) {
							if ( !ui.item ) {
								var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( $(this).val() ) + "$", "i" ),
									valid = false;
								select.children( "option" ).each(function() {
									if ( $( this ).text().match( matcher ) ) {
										this.selected = valid = true;
										return false;
									}
								});
								if ( !valid ) {
									// remove invalid value, as it didn't match anything
									$( this ).val( "" );
									select.val( "" );
									input.data( "autocomplete" ).term = "";
									return false;
								}
							}
						}
					})
					.addClass( "ui-widget ui-widget-content ui-corner-left" );
	
				input.data( "autocomplete" )._renderItem = function( ul, item ) {
					return $( "<li></li>" )
						.hide()
						.data( "item.autocomplete", item )
						.append( "<a>" + item.label + "</a>" )
						.appendTo( ul );
				};
	
				this.button = $( "<button type='button'>&nbsp;</button>" )
					.attr( "tabIndex", -1 )
					.attr( "title", "Show All Items" )
					.insertAfter( input )
					.button({
						icons: {
							primary: "ui-icon-triangle-1-s"
						},
						text: false
					})
					.removeClass( "ui-corner-all" )
					.addClass( "ui-corner-right ui-button-icon" )
					.click(function() {
						// close if already visible
						if ( input.autocomplete( "widget" ).is( ":visible" ) ) {
							input.autocomplete( "close" );
							return;
						}
	
						// work around a bug (likely same cause as #5265)
						$( this ).blur();
	
						// pass empty string as value to search for, displaying all results
						input.autocomplete( "search", "" );
						input.focus();
					});
			},
	
			destroy: function() {
				this.input.remove();
				this.button.remove();
				this.element.show();
				$.Widget.prototype.destroy.call( this );
			}
		});
	})( jQuery );
	
	$('select').combobox();
	
});
