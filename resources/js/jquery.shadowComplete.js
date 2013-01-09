(function($){
	var opts;

	var methods = {
		find: function($this) {
			search = $this.val().toLowerCase();

			if(search.length >= opts.minLength)
			{
				if(Object.prototype.toString.call(opts.source) === '[object Array]')
				{
					this.find_in_array($this);
				}
				else if(typeof opts.source == 'string' || opts.source instanceof String)
				{
					this.find_ajax($this);
				}
				else if(Object.prototype.toString.call(opts.source) === '[object Function]')
				{
					opts.source($this);
				}
			}
			else if(search.length > 0) {
				this.update($this);
			} else {
				if($this.val().length == 0 && Object.prototype.toString.call(opts.source) === '[object Array]') {
					$this.data('SCResults', opts.source);
				}
				this.update($this);
			}
		},

		find_in_array: function($this) {
			var result = new Array();
			var searchLen = $this.val().length;

			for(var i = 0; i < opts.source.length; i++)
			{
				if(opts.source[i].substr(0, searchLen).toLowerCase() == $this.val().toLowerCase())
				{
					result.push(opts.source[i]);
				}
			}

			$this.data('SCResults', result);
			$this.data('SCIndex', 0);
			this.update($this);
			$this.data('SCList').show();
		},

		find_ajax: function($this) {
			var uri = encodeURIComponent($this.val());
			var url = opts.source + uri + opts.sourceSuffix;
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
				$this.data('SCList').show();
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

					if(Object.prototype.toString.call(opts.source) === '[object Array]')
					{
						$this.data('SCResults', opts.source);
					}
				}
			}

			this.dropdownUpdate($this);
		},

		dropdownUpdate: function($this) {

			$this.data('SCList').empty();

			if($this.data('SCResults').length > 0 || (Object.prototype.toString.call(opts.source) === '[object Array]' && opts.source.length > 0))
			{
				if($this.data('SCResults').length == 0) {
					$this.data('SCResults', opts.source)
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

			var source = new Array();

			$this.children().each(function(key){
				if($(this).attr('value').length > 0){
					source.push($(this).attr('value'));
				}

				if($(this).attr('selected') != undefined) {
					text = $(this).attr('value');
				}
			});

			SCInput.attr('value', text);

			opts.source = source;

			// finally, swap the elements   
			$this.replaceWith(SCInput); 

			var SCButton = $('<button class="SCButton" tabIndex="-1"/>').click(function(e){
				e.preventDefault();

				if(SCInput.data('SCList').css('display') != 'none') {
					SCInput.data('SCList').hide();
				} else {
					SCInput.data('SCIndex', 0);

					if(SCInput.val().length > 0){
						SC.find(SCInput);
					} else {
						SCInput.data('SCResults', opts.source);
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

			$this.before(SCHint);
			$this.after(SCList);

			$this.data('SCHint', SCHint);
			$this.data('SCList', SCList);
			SCInput.data('SCResults', new Array());

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
							SC.update($this);
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
			if(position.length == 0 && position.end == $this.val().length && $this.val() != $this.data('SCHint').val())
			{
				e.preventDefault();
				$this.val($this.data('SCHint').val());
				$this.trigger('SCAccept');
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

/*
 Rangy Text Inputs, a cross-browser textarea and text input library plug-in for jQuery.

 Part of Rangy, a cross-browser JavaScript range and selection library
 http://code.google.com/p/rangy/

 Depends on jQuery 1.0 or later.

 Copyright 2010, Tim Down
 Licensed under the MIT license.
 Version: 0.1.205
 Build date: 5 November 2010
*/
(function(n){function o(e,g){var a=typeof e[g];return a==="function"||!!(a=="object"&&e[g])||a=="unknown"}function p(e,g,a){if(g<0)g+=e.value.length;if(typeof a=="undefined")a=g;if(a<0)a+=e.value.length;return{start:g,end:a}}function k(){return typeof document.body=="object"&&document.body?document.body:document.getElementsByTagName("body")[0]}var i,h,q,l,r,s,t,u,m;n(document).ready(function(){function e(a,b){return function(){var c=this.jquery?this[0]:this,d=c.nodeName.toLowerCase();if(c.nodeType==
1&&(d=="textarea"||d=="input"&&c.type=="text")){c=[c].concat(Array.prototype.slice.call(arguments));c=a.apply(this,c);if(!b)return c}if(b)return this}}var g=document.createElement("textarea");k().appendChild(g);if(typeof g.selectionStart!="undefined"&&typeof g.selectionEnd!="undefined"){i=function(a){return{start:a.selectionStart,end:a.selectionEnd,length:a.selectionEnd-a.selectionStart,text:a.value.slice(a.selectionStart,a.selectionEnd)}};h=function(a,b,c){b=p(a,b,c);a.selectionStart=b.start;a.selectionEnd=
b.end};m=function(a,b){if(b)a.selectionEnd=a.selectionStart;else a.selectionStart=a.selectionEnd}}else if(o(g,"createTextRange")&&typeof document.selection=="object"&&document.selection&&o(document.selection,"createRange")){i=function(a){var b=0,c=0,d,f,j;if((j=document.selection.createRange())&&j.parentElement()==a){f=a.value.length;d=a.value.replace(/\r\n/g,"\n");c=a.createTextRange();c.moveToBookmark(j.getBookmark());j=a.createTextRange();j.collapse(false);if(c.compareEndPoints("StartToEnd",j)>
-1)b=c=f;else{b=-c.moveStart("character",-f);b+=d.slice(0,b).split("\n").length-1;if(c.compareEndPoints("EndToEnd",j)>-1)c=f;else{c=-c.moveEnd("character",-f);c+=d.slice(0,c).split("\n").length-1}}}return{start:b,end:c,length:c-b,text:a.value.slice(b,c)}};h=function(a,b,c){b=p(a,b,c);c=a.createTextRange();var d=b.start-(a.value.slice(0,b.start).split("\r\n").length-1);c.collapse(true);if(b.start==b.end)c.move("character",d);else{c.moveEnd("character",b.end-(a.value.slice(0,b.end).split("\r\n").length-
1));c.moveStart("character",d)}c.select()};m=function(a,b){var c=document.selection.createRange();c.collapse(b);c.select()}}else{k().removeChild(g);window.console&&window.console.log&&window.console.log("TextInputs module for Rangy not supported in your browser. Reason: No means of finding text input caret position");return}k().removeChild(g);l=function(a,b,c,d){var f;if(b!=c){f=a.value;a.value=f.slice(0,b)+f.slice(c)}d&&h(a,b,b)};q=function(a){var b=i(a);l(a,b.start,b.end,true)};u=function(a){var b=
i(a),c;if(b.start!=b.end){c=a.value;a.value=c.slice(0,b.start)+c.slice(b.end)}h(a,b.start,b.start);return b.text};r=function(a,b,c,d){var f=a.value;a.value=f.slice(0,c)+b+f.slice(c);if(d){b=c+b.length;h(a,b,b)}};s=function(a,b){var c=i(a),d=a.value;a.value=d.slice(0,c.start)+b+d.slice(c.end);c=c.start+b.length;h(a,c,c)};t=function(a,b,c){var d=i(a),f=a.value;a.value=f.slice(0,d.start)+b+d.text+c+f.slice(d.end);b=d.start+b.length;h(a,b,b+d.length)};n.fn.extend({getSelection:e(i,false),setSelection:e(h,
true),collapseSelection:e(m,true),deleteSelectedText:e(q,true),deleteText:e(l,true),extractSelectedText:e(u,false),insertText:e(r,true),replaceSelectedText:e(s,true),surroundSelectedText:e(t,true)})})})(jQuery);