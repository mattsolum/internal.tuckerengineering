(function($){
	var opts;

	var methods = {
		find: function($this) {
			search = $this.val().toLowerCase();

			if(search.length >= opts.minLength)
			{
				if(Object.prototype.toString.call($this.data('SCSource')) === '[object Array]')
				{
					this.find_in_array($this);
				}
				else if(typeof $this.data('SCSource') == 'string' || $this.data('SCSource') instanceof String)
				{
					this.find_ajax($this);
				}
				else if(Object.prototype.toString.call($this.data('SCSource')) === '[object Function]')
				{
					$this.data('SCSource')($this);
				}
			}
			else if(search.length > 0) {
				this.update($this);
			} else {
				if($this.val().length == 0 && Object.prototype.toString.call($this.data('SCSource')) === '[object Array]') {
					$this.data('SCResults', $this.data('SCSource'));
				}
				else {
					$this.data('SCResults', new Array());
				}

				this.update($this);
			}
		},

		find_in_array: function($this) {
			var result = new Array();
			var searchLen = $this.val().length;

			for(var i = 0; i < $this.data('SCSource').length; i++)
			{
				if($this.data('SCSource')[i].substr(0, searchLen).toLowerCase() == $this.val().toLowerCase())
				{
					result.push($this.data('SCSource')[i]);
				}
			}

			$this.data('SCResults', result);
			$this.data('SCIndex', 0);
			this.update($this);

			if($this.data('SCResults').length > 0) {
				$this.data('SCList').show();
			}
		},

		find_ajax: function($this) {
			var uri = encodeURIComponent($this.val());
			var url = $this.data('SCSource') + uri + opts.sourceSuffix;
			var val = '';

			var SC = this;

			$.getJSON(url, function(json){
				var result;

				if(opts.dataContainer !== null)
				{
					result = json[opts.dataContainer];
					
				}
				else
				{
					result = json;
				}

				$this.data('SCResults', result);
				$this.data('SCIndex', 0);
				SC.update($this);

				if($this.data('SCResults').length > 0) {
					$this.data('SCList').show();
				}
			});
		},

		update: function($this) {
			if($this.val().length < opts.minLength && $this.data('SCList').css('display') == 'none'){
				$this.data('SCHint').val('');
			}
			else {
				if($this.data('SCResults').length > 0){
					var hintVal = $this.val() + $this.data('SCResults')[$this.data('SCIndex')].substr($this.val().length);
					$this.data('SCHint').val(hintVal);
				} else {
					$this.data('SCHint').val($this.val());

					$this.data('SCList').hide();
				}
			}

			this.dropdownUpdate($this);
		},

		dropdownUpdate: function($this) {

			$this.data('SCList').empty();

			if($this.data('SCResults').length > 0 || (Object.prototype.toString.call($this.data('SCSource')) === '[object Array]' && $this.data('SCSource').length > 0))
			{
				if($this.data('SCResults').length == 0) {
					$this.data('SCResults', $this.data('SCSource'))
				}

				for(var i = 0; i < $this.data('SCResults').length; i++){
					var li = $('<li />');
					li.append($('<a href="#" />').html($this.data('SCResults')[i]));
					$this.data('SCList').append(li);

					if(i == $this.data('SCIndex'))
					{
						li.addClass('SCSelected');
					}
				}

				//Handle scroll position
				var selected = $this.data('SCList').children('.SCSelected');
				var scrollBottom = $this.data('SCList').scrollTop() + $this.data('SCList').innerHeight();
				var scrollTop = $this.data('SCList').scrollTop();
				var position = selected.position();

				if(0 > position.top)
				{
					//Scroll up
					$this.data('SCList').scrollTop($this.data('SCList').scrollTop() + position.top);
				}
				else if (position.top >= $this.data('SCList').innerHeight())
				{
					//Scroll down
					var scrollTo = (position.top + selected.outerHeight()) - $this.data('SCList').innerHeight() + $this.data('SCList').scrollTop();
					$this.data('SCList').scrollTop(scrollTo);
				}
			}
			else {
				$this.data('SCList').hide();
			}
		},

		/**
		 * Creates the required DOM elements for the given element
		 * @param  {jQuery} $this
		 * @return {null}
		 */
		build: function($this) {
			$this.attr('autocomplete', 'off');
			
			if($this.prop('tagName') == 'INPUT') {
				//Make sure it is not of a type we cannot work with
				if($this.prop('tagName').search(/button|checkbox|file|hidden|image|radio|reset|submit/i) == -1)
				{
					this.buildInput($this);
				}
			} else if ($this.prop('tagName') == 'SELECT') {
				this.buildSelect($this);
			}

			$this.data('SCIndex', 0);
			$this.data('SCResults', new Array());
		},

		buildSelect: function($this) {
			var SC = this;
			var SCInput = $('<input />');
			var text = '';

			// iterate over every attribute of the #some_id span element
			$.each($this.get(0).attributes, function(i, attrib) {
					// set each attribute to the specific value
					SCInput.attr(attrib.name, attrib.value);
			});

			SCInput.addClass('SCInput');
			SCInput.addClass('SCSelect');
			SCInput.attr('type', 'text');
			SCInput.attr('autocomplete', 'off');

			var source = new Array();

			$this.children('option').each(function(key){
				if($(this).attr('value').length > 0){
					source.push($(this).attr('value'));
				}

				if($(this).attr('selected') != undefined) {
					text = $(this).attr('value');
				}
			});

			SCInput.attr('value', text);

			SCInput.data('SCSource', source);

			// finally, swap the elements   
			$this.replaceWith(SCInput); 

			var SCButton = $('<button class="SCButton" tabindex="-1"/>').click(function(e){
				e.preventDefault();
				if(SCInput.data('SCList').css('display') != 'none') {
					SCInput.data('SCList').hide();
				} else {
					SCInput.data('SCIndex', 0);

					if(SCInput.val().length > 0){
						SC.find(SCInput);
					} else {
						SCInput.data('SCResults', $this.data('SCSource'));
						SC.dropdownUpdate(SCInput);
						SCInput.data('SCList').show();
					}

					SCInput.focus();
				}
			}).mousedown(function(e){e.preventDefault()});

			var SCHint = $('<input />', {
								css: {	
									'font-family'			: SCInput.css("font-family"),
									'font-size'				: SCInput.css("font-size"),
									'font-style'			: SCInput.css("font-style"),
									'font-weight'			: SCInput.css("font-weight"),
									'text-shadow'			: ($.support.opacity) ? SCInput.css("text-shadow") : '',
									'line-height'			: SCInput.css("line-height"),
									'position'				: 'absolute',
									'height'				: SCInput.css("height"),
									'width'					: SCInput.css("width"),
									'color'					: SCInput.css("border-top-color"),
									'margin-top'			: SCInput.css("margin-top"),
									'margin-left'			: SCInput.css("margin-left"),
									'border-top'			: SCInput.css("border-top"),
									'border-bottom'			: SCInput.css("border-bottom"),
									'border-left'			: SCInput.css("border-left"),
									'-moz-user-select'		: 'none',
									'-webkit-user-select'	: 'none',
									'cursor'				: 'text',
									'z-index'				: '-100'
								},
								class: 'SCHint',
								'type': 'text',
								'disabled': 'true'
							});

			var SCList = $('<ul />', {
					css: {
						width: SCInput.outerWidth()
					},
					class: 'SCList'
				}).hide();

			SCInput.before(SCHint);
			SCInput.after(SCList);
			SCInput.after(SCButton);

			SCInput.data('SCHint', SCHint);
			SCInput.data('SCButton', SCButton);
			SCInput.data('SCList', SCList);
			SCInput.data('SCResults', new Array());
			SCInput.data('SCIndex', 0);

			this.attachEvents(SCInput);
		},

		buildInput: function($this) {
			$this.css('background-color', 'transparent');
			$this.addClass('SCInput');

			var SCHint = $('<input />', {
								css: {	
									'font-family'			: $this.css("font-family"),
									'font-size'				: $this.css("font-size"),
									'font-style'			: $this.css("font-style"),
									'font-weight'			: $this.css("font-weight"),
									'text-shadow'			: ($.support.opacity) ? $this.css("text-shadow") : '',
									'line-height'			: $this.css("line-height"),
									'position'				: 'absolute',
									'height'				: $this.css("height"),
									'width'					: $this.css("width"),
									'color'					: $this.css("border-top-color"),
									'margin-top'			: $this.css("margin-top"),
									'margin-left'			: $this.css("margin-left"),
									'border-top'			: $this.css("border-top"),
									'border-bottom'			: $this.css("border-bottom"),
									'border-left'			: $this.css("border-left"),
									'-moz-user-select'		: 'none',
									'-webkit-user-select'	: 'none',
									'cursor'				: 'text',
									'z-index'				: '-100'
								},
								class: 'SCHint',
								'type': 'text',
								'disabled': 'true'
							});
				
			var SCList = $('<ul />', {
					css: {
						'width': 		$this.outerWidth(),
						'position': 	'absolute'
					},
					class: 'SCList'
				}).hide();

			$this.attr('autocomplete', 'off');

			$this.before(SCHint);
			$this.after(SCList);

			$this.data('SCHint', SCHint);
			$this.data('SCList', SCList);
			$this.data('SCResults', new Array());
			$this.data('SCSource', opts.source);

			this.attachEvents($this);
		},

		buildDropdown: function($this) {

		},

		attachEvents: function($this) {
			var SC = this;

			//Handler for function keys
			//Tab and arrow keys
			$this.keydown(function(e){
				switch(e.which) {
					case 9: //Tab
						methods.acceptSuggestion($this, e);
						break;
					case 37: //Left arrow

						break;
					case 38: //Up arrow
						methods.decrementIndex($this, e);
						break;
					case 39: //Right arrow
						methods.acceptSuggestion($this, e);
						break;
					case 40: //Down arrow
						if($this.data('SCList').css('display') == 'none') {
							SC.dropdownUpdate($this);
							$this.data('SCList').show();
							SC.find($this);
						} else {
							methods.incrementIndex($this, e);
						}
						break;
				}
			});

			//Handler for searching the autocomplete
			$this.keyup(function(e){
				if(e.which != 38 && e.which != 40){
					methods.find($this);
				}
			})

			//Hide everything on focusout
			$this.focusout(function(e){
				$this.data('SCResults', new Array());
				methods.dropdownUpdate($this);
				$this.data('SCList').empty().hide();
				$this.data('SCHint').val('');
			});

			$this.blur(function(e){
				$this.data('SCList').hide();
				$this.data('SCHint').val('');
			});

			$this.data('SCList').mousedown(function(e){
				if($(e.target).prop('tagName') == 'A'){
					$this.val($(e.target).html());
					$this.trigger('SCAccept');
				}
			});
		},

		acceptSuggestion: function($this, e) {
			var position = $this.getSelection();

			//If text is not selected, the cursor is at the end fo the text, and the hint box has text to donate
			if(position.length == 0 && position.end == $this.val().length && $this.data('SCHint').val().length > 0 && $this.val() != $this.data('SCHint').val())
			{
				e.preventDefault();
				$this.val($this.data('SCHint').val());
				$this.data('SCList').hide();
				$this.trigger('SCAccept');
			}
			else {
				$this.trigger('focusout');
			}
		},

		incrementIndex: function($this, e) {
			var curIndex 	= $this.data('SCIndex');
			var results 	= $this.data('SCResults');

			if(curIndex + 1 <= results.length - 1)
			{
				e.preventDefault();
				this.setIndex($this, curIndex + 1);
			}
		},

		decrementIndex: function($this, e) {
			var curIndex 	= $this.data('SCIndex');
			var results 	= $this.data('SCResults');

			if(curIndex - 1 >= 0)
			{
				e.preventDefault();
				this.setIndex($this, curIndex - 1);
			}
		},

		setIndex: function($this, index) {
			$this.data('SCIndex', index);
			this.update($this);
		}
	};

	$.fn.shadowComplete = function(options) {
		opts = $.extend({
					inputClass: 	'SCUserInput',
					hintClass: 		'SCHint',
					dropdownClass: 	'SCDropdown',
					maxSuggestions: 100,
					minLength: 		1,
					source: 		new Array(),
					dataContainer: 	null,
					sourceSuffix: 	''
				}, options);

		this.each(function(){
			$this = $(this);

			methods.build($this);
		});
	}

})(jQuery);