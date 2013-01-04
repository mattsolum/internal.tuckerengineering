(function($){
	var opts;
	var results;

	var methods = {
		init: function() {
			
		},

		find: function($this, search) {
			search = search.replace(new RegExp("['\x00-\x1F\x80-\xFF]", "gm"), '');
			search = search.toLowerCase();

			if(Object.prototype.toString.call(opts.source) === '[object Array]')
			{
				this.find_in_array($this, search);
			}
			else if(typeof opts.source == 'string' || opts.source instanceof String)
			{
				this.find_ajax($this, search);
			}
			else if(Object.prototype.toString.call(opts.source) === '[object Function]')
			{
				opts.source($this, search);
			}

			this.update($this, search);
		},

		find_in_array: function($this, search) {
			var result = new Array();
			var searchLen = search.length;

			for(var i = 0; i < opts.source.length; i++)
			{
				if(opts.source[i].substr(0, searchLen).toLowerCase() == search)
				{
					result.unshift(opts.source[i]);
				}
			}

			$this.data('SCresults', result);
			$this.data('SCindex', 0);
		},

		find_ajax: function($this, search) {
			var uri = encodeURIComponent(search);
			var url = opts.source + uri + opts.sourceSuffix;
			var SC = $this.data('SCbg');
			var val = '';

			SComp = this;

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

				$this.data('SCresults', result);
				$this.data('SCindex', 0);
				SComp.update($this);
			});
		},

		update: function($this, q) {
			var val = '';
			var SC = $this.data('SCbg');

			if($this.data('SCresults').length > 0)
			{
				val = $this.val() + $this.data('SCresults')[$this.data('SCindex')].substr($this.val().length);
			}
			SC.val(val);
		}
	};

	$.fn.shadowComplete = function(options){
		opts = $.extend({
			inputClass: 'SCUserInput',
			hintClass: 'SCHint',
			minLength: 2,
			source: new Array(),
			dataContainer: null,
			sourceSuffix: ''
		}, options);

		methods.init();

		this.each(function(){
			$this = $(this);

			var tagName = $this.prop('tagName');

			if(tagName == 'INPUT') {
				
				var SC = $this.clone();

				$this.addClass(opts.inputClass);
				$this.css('background-color', 'transparent');
				$this.css('z-index', '100');

				SC.css({
					position: 	"absolute",
					color: 		"#ccc",
					zIndex: 	"-100"
				});

				SC.addClass(opts.hintClass)
				SC.prop({
					"disabled": 	"true",
					"id": 			"",
					"title": 		"",
					"name": 		""
				});

				$this.before(SC);

				$this.data('SCbg', SC);

				$this.keydown(function(e){
					var q = $this.val();
					var shouldSearch = false;

					if(
						(e.which > 48 && e.which < 90) ||
						(e.which > 96 && e.which < 111) ||
						(e.which > 186 && e.which < 222)
					)
					{
						q = q + String.fromCharCode(e.which);
						shouldSearch = true;
					}

					if(e.which == 13) {
						e.preventDefault();
					}
					else if (e.which == 8 || e.which == 46)
					{
						var selection = $(this).getSelection();

						if(selection.length > 0)
						{
							q = q.substr(0, selection.start) + q.substr(selection.end);
						}
						else
						{
							if(e.which == 8)
							{
								q = q.substr(0, selection.start - 1) + q.substr(selection.end);
							}
							else
							{
								q = q.substr(0, selection.start) + q.substr(selection.end + 1);
							}
						}

						shouldSearch = true;
					}
					else if(e.which == 38)
					{
						//up arrow
						if($(this).data('SCindex') > 0)
						{
							e.preventDefault();
							$this.data('SCindex', $this.data('SCindex') - 1);
						}

						SC.val($this.data('SCresults')[$this.data('SCindex')]);
					}
					else if(e.which == 40)
					{
						//down arrow
						if($(this).data('SCindex') < $(this).data('SCresults').length - 1)
						{
							e.preventDefault();
							$this.data('SCindex', $this.data('SCindex') + 1);
						}

						SC.val($this.data('SCresults')[$this.data('SCindex')]);
					}
					else if(e.which == 9)
					{
						//Tab
						if($this.val().length != SC.val().length)
						{
							$this.val(SC.val());
							e.preventDefault();
						}
						
					}

					q = q.replace(new RegExp("['\x00-\x1F\x80-\xFF]", "gm"), '');
					q = q.toLowerCase();

					if(shouldSearch == true && q.length >= opts.minLength)
					{
						methods.find($this, q);
					}
				});

				$this.keyup(function(e){
					if($this.val().length < opts.minLength)
					{
						SC.val('');
					}

					switch(e.which) {
						case 13:
							//enter
							$this.val(SC.val());
							$(this).parents("form").submit();
							break;
						case 39:
							//right arrow
							var selection = $this.getSelection();
							if(SC.val().length != 0 && selection.end == $this.val().length)
							{
								$this.val(SC.val());
							}
							break;
					}
				});

			}
			else if($this.prop('tagName') == 'SELECT') {

			}
		});
	}

	$.fn.shadowComplete.setSource = function(source)
	{
		opts.source = source;
		methods.find(this.val());
		alert('yay');
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