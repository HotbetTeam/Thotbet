// Utility
if ( typeof Object.create !== 'function' ) {
	Object.create = function( obj ) {
		function F() {};
		F.prototype = obj;
		return new F();
	};
}



(function( $, window, document, undefined ) {
	

	/**/
	/* text slide*/
	/**/
	var TextSlide = {
		init: function (options, elem) {
			var self = this;

			var timer, $elem = $(elem);
			var ul = $elem.find('ul');
			var currentLeft = 0;
			var fullWidth = $elem.parent().width();
			var current = 0;

			$elem.css({
				width: fullWidth,
				overflow: 'hidden'
			})

			ul.css({
				overflow: 'hidden',
				position: 'relative',
	    		// transition: "left .2s",
	    		width: $elem.find('ul').width(),
	    		left: 0
			});

			var currentWidth = 0;
			$.each( ul.find('li'), function() {

				currentWidth += $(this).width()+1;
			});

			slidesleft();
			
			function timmer () {

				timer = setTimeout(function(){

					slidesleft();
				}, 50);
			}

			function slidesleft () {

				if( currentWidth <= fullWidth ) return false;
				
				currentLeft++;
				ul.css({
					width: currentWidth,
					left: currentLeft*-1
				});

				if( (currentLeft+fullWidth)===currentWidth ){

					var li = ul.find('li');

					var $item = $(li[current]);
					var item = $item.clone();

					current++;
					currentWidth += $item.width();
					ul.append( item );
				}

				timmer();
			}
			
			$elem.mouseenter(function() {
				clearTimeout( timer );
			}).mouseleave(function(){
				slidesleft();
			});
		}
	}
	$.fn.textslide = function( options ) {
		return this.each(function() {
			var $this = Object.create( TextSlide );
			$this.init( options, this );
			$.data( this, 'textslide', $this );
		});
	};
	// $.fn.textslide.options = {};

	/**/
	/* autosize */
	/**/
	var observe;
	if (window.attachEvent) {
	    observe = function (element, event, handler) {
	        element.attachEvent('on'+event, handler);
	    };
	}
	else {
	    observe = function (element, event, handler) {
	        element.addEventListener(event, handler, false);
	    };
	}
	var Autosize = {
		init: function (options, text) {
			var self = this;

			$(text).attr('rows', 1).addClass('uiTextareaAutogrow');
			function resize () {
		        text.style.height = 'auto';
		        text.style.height = text.scrollHeight+'px';
		    }

		    /* 0-timeout to get the already changed text */
		    function delayedResize () {
		        window.setTimeout(resize, 0);
		    }
			
			observe(text, 'change',  resize);
		    observe(text, 'cut',     delayedResize);
		    observe(text, 'paste',   delayedResize);
		    observe(text, 'drop',    delayedResize);
		    observe(text, 'keydown', delayedResize);

		    resize();
		}
	}
	/*$.fn.autosize = function( options ) {
		return this.each(function() {
			var $this = Object.create( Autosize );
			$this.init( options, this );
			$.data( this, 'autosize', $this );
			
		});
	};
	$.fn.autosize.options = {};*/

	/**/
	/* Stars Ratable */
	/**/
	var StarsRatable = {
		init: function (options, elem) {
			var self = this;

			self.elem = elem;
			self.$elem = $(elem);
			self.options = $.extend( {}, $.fn.starsRatable.options, options );

			self.rating = parseInt( self.$elem.val() ) || 0;

			self.$box = $('<div/>', {class:'uiStarsRatable'});

			self.$elem.before( self.$box );
			$.each(self.options.level ,function (i, obj) {
				var $a = $('<a/>', {class: 'icon-star', rating: obj.rating});

				if( self.options.showlabel ){
					Event.setPlugin( $a, 'tooltip', {text: obj.text, reload: 0, overflow:{
		           		Y: "Above",
		           		X: "Left"
		           	} } );
	           	}
				self.$box.append( $a );
			});

			self.$box.find('a').mouseenter(function () {
				var $item = $(this);
				var rating = $item.attr( 'rating' );

				for (var i = rating; i > 0; i--) {
					self.$box.find('[rating='+i+']').addClass('has-hover');
				};
				
			}).mouseleave(function () {
				self.$box.find('.has-hover').removeClass('has-hover');

			}).click(function () {
				var $item = $(this);
				self.rating = $item.attr( 'rating' );
				self.active();	
			});

			self.active();
		},

		active: function ( rating ) {
			var self = this;

			self.$box.find('.has-active').removeClass('has-active');
			self.$elem.val( self.rating );
			for (var i = self.rating; i > 0; i--) {
				self.$box.find('[rating='+i+']').addClass('has-active');
			};
		}
	};
	$.fn.starsRatable = function( options ) {
		return this.each(function() {
			var $this = Object.create( StarsRatable );
			$this.init( options, this );
			$.data( this, 'starsRatable', $this );
		});
	};
	$.fn.starsRatable.options = {
		rating: 0,
		level: { 
			1: { rating: 1, text: 'แย่' },
			2: { rating: 2, text: 'พอใช้' },
			3: { rating: 3, text: 'ดี' },
			4: { rating: 4, text: 'ดีมาก' },
			5: { rating: 5, text: 'ยอดเยี่ยม' }
		},
		showlabel: false
	};

	/**/
	/* phone */
	/**/
	var PhoneNumber = {
		init: function (options, elem) {
			var self = this;

			self.elem = elem;
			self.$elem = $(elem);

			var text = self.$elem.val();
			var val = '';
			/*for (var i = 0; i < text.length; i++) {
				
				if( $.inArray(i, [3,6])>=0 ){
					val += "-";
				}
				val += text[i];
			};*/

			self.$elem.attr('maxlength', 12).keydown(function (e) {
				var value = $(this).val();

				console.log( e.keyCode, value.length, $.inArray(value.length, [3,6] ) );
				if((e.keyCode>=48&&e.keyCode<=57) || (e.keyCode>=96&&e.keyCode<=105) ) {
					if( $.inArray(value.length, [3,7])>=0 ){
						$(this).val( value+"-" )
					}
				}
				else if( e.keyCode==8  ){
					if( $.inArray(value.length, [5,9])>=0 ){
						$(this).val( value.substr(0,value.length-1) );
					}
				}
				else if( e.keyCode==189 && $.inArray(value.length, [3,7] )>=0 ) {
					
				}
				else{
					e.preventDefault();
				}
			});


		}
	}

	$.fn.phone_number = function( options ) {
		return this.each(function() {
			var $this = Object.create( PhoneNumber );
			$this.init( options, this );
			$.data( this, 'starsRatable', $this );
		});
	};

	/**/
	/* Selectbox */
	/**/
	var Selectbox = {
		init: function( options, elem ) {
			
			var self = this;
			
			self.elem = elem;
			self.$elem = $( elem );
			
			self.options = $.extend( {}, $.fn.selectbox.options, options );
			
			// setting
			self.setSlecte();
			self.setElem();

			
			self.getSlected();

			if( self.options.setitem ){
			}
			
			self.active = false;
			self.setMenu();
			
			if ( typeof self.options.onComplete === 'function' ) {
				self.options.onComplete.apply( self, arguments );
			}

			// Event
			self.initEvent();
		},
		
		initEvent: function(){
			var self = this;
			
			self.$btn.not('.disabled').click(function(e){
				
				$('body').find('.uiPopover').find('a.btn-toggle.active').removeClass('active');
				if( self.menu.hasClass('open') ){
					self.close();
				}
				else{
					self.$btn.addClass('active');
					self.display();
					self.open();
				}
				
				e.stopPropagation();
			});
			
			$('a', self.menu).click(function(){
				self.change( $(this).parent().index() );				
			});

			$('html').on('click', function() {
		
				if( self.active && self.menu.hasClass('open') ){
					self.$btn.removeClass('active');
					self.close();
				}
				
			});
		},

		setElem: function(){
			var self = this;
			
			self.selectedInput = $('<input>', {
				class: 'hiddenInput',
				type: 'hidden',
				name: self.$elem.attr('name')
			});
			self.selectedText = $('<span>', {class: 'btn-text'});
				
			self.original = self.$elem;

			var placeholder = $('<div/>', {class: 'uiPopover'});
			
			self.$elem.replaceWith(placeholder);
            self.$elem = placeholder;
			
			self.$btn = $('<a>', {class: 'btn btn-box btn-toggle'}).append( self.selectedText );

			if( !self.options.display ){
				self.$btn.addClass('disabled');
			}

			if( !self.options.icon ){
				self.$btn.append( $('<i/>', {class: 'img mls icon-angle-down'}) );
			}

			self.$elem.append( self.$btn, self.selectedInput );
		},
		setSlecte: function(){
			var self = this;
			
			self.select = [];
            self.$elem.find('optgroup,option').each(function (i, obj) {

            	var $item = $(this), type = "";

            	if($item.context.nodeName.toLowerCase()=='option' ){
            		type = 'default';
            	}
            	else if($item.context.nodeName.toLowerCase()=='optgroup' ){
            		type = 'header';
            	}

            	if( $item.attr('type') ){
            		type = $item.attr('type');
            	}

				if( type ){
					var data = {
						type: type,
						text: $.trim($item.text()),
						value: $.trim($item.val()),
						href: ( $item.attr('href') ) ? $item.attr('href') : '',
						selected: $item.is(':selected'),
						image: $.trim($item.attr('image-url')),
						label: ( $item.attr('label') ) ? $item.attr('label') : '',
						icon: ( $item.attr('icon') ) ? $item.attr('icon') : '',
						// loadUrl: ( $item.attr('ajaxify') ) ? $item.attr('ajaxify') : '',
					};
					
					if($item.is(':selected')){
						self.selected = data;
					}
					
					self.select.push(data);
				}
				
            });
		},

		change: function ( length ) {
			var self = this;
			var $item = self.menu.find('li').eq(length);

			// if( $item.hasClass('selected') ) return false;
			$item.parent().find('.selected').removeClass('selected');
			$item.addClass('selected');

			self.setSlected( length );
			self.getSlected();
		},
		setSlected: function( index ){
			var self = this;
			self.selected = self.select[index];			
		},
		getSlected: function(){
			var self = this;

			if ( typeof self.options.onSelected === 'function' ) {
				self.options.onSelected.apply( self, arguments );
			}
			self.selectedText.text(self.selected.text);
			self.selectedInput.val(self.selected.value);
		},
		
		open: function(){
			var self = this;
			self.active = true;
			
			self.getOffset();
			self.menu.addClass('open');
		},
		
		close: function(){
			var self = this;
			
			self.active = false;		
			self.menu.removeClass('open'); // .remove();
		},
		
		display: function(){
			var self = this;

			$('body').append( self.menu );
			
			if( $('body').find('.open.uiContextualPositioner').length>0 ){
				$('body').find('.open.uiContextualPositioner').removeClass('open');
			}
			
			if( $('body').find('.openToggler.uiToggle').length>0 ){
				$('body').find('.openToggler.uiToggle').removeClass('openToggler');
			}
		},
		
		setMenu: function(){
			var self = this;
			
			var ul = $('<ul>', {class: 'uiMenu'});

			/*var $input = 
			ul.append( $('<li class="add"><table><tbody><tr><td><input class="inputtext" type="text"></td><td><button type="text" class="btn">เพิ่ม</button></td></tr></tbody></table></li>') );*/


			$.each(self.select, function (i, data) {
				ul.append( self._item[data.type || 'default']( data ) );			
			});
			
			var $boxInput = '';

			if( self.options.add_item_url ){
				var $input = $('<input>', {class: 'inputtext', autocomplete:"off", placeholder: 'เพิ่มใหม่...'});
				var $btn = $('<a />', {class: 'btn rfloat', 'text': 'บันทึก'});
				var $boxInput = $('<div>', {class: 'box-input'} ).append( $input, $btn );
				$input.click(function (e) {
					e.stopPropagation();
				}).keydown(function (e) {

					if(e.keyCode ==13){
						self.addItem( $input );
						e.preventDefault();
					}

				});
				$btn.click(function (e) {
					e.stopPropagation();
					self.addItem( $input );
				});
			}

			self.menu = $('<div>', {class: 'uiContextualPositioner'})
				.addClass()
				.append(
					$('<div>', {class: 'toggleFlyout selectBoxFlyout'}).append( $boxInput, ul )
				);
				
			if( self.options.max_width ){
				self.menu.find('.toggleFlyout').css('width', self.options.max_width);
			}
		},

		addItem: function ( $input ) {
			var self = this;

			var formData = new FormData();
			formData.append(self.options.add_item_name, $input.val() );
			formData.append('get_insert_select', true );

			$input.attr('disabled', true);

			$.ajax({
				type: "POST",
				url: self.options.add_item_url,
				data: formData,
				dataType: 'json',
				processData: false,
        		contentType: false,
			}).done(function( response ) {
			    
			    self.selected = response.select;
			    var select = []; 
			    select.push( self.selected );

			    $.each( self.select, function (i, obj) {
			    	select.push( obj );
			    } );
			    
			    self.select = select;

			    self.close();
			   	self.setMenu();
			   	self.getSlected();

			   	self.menu.find('.selected').removeClass('selected');
			   	self.menu.find('li').first().addClass('selected');

			    // console.log( self.select );
			}).fail(function() {
			   
			}).always(function() {
			    $input.removeClass('disabled');
			});
		},

		_item: {
			default: function( data ){

				var li = $('<li/>');
				var a = $('<a/>', {class: 'itemAnchor'});
				var label = $('<span/>', {class: 'itemLabel', text: data.text});

				li.addClass( data.selected ? 'selected':''  ).append( a );

				if( data.icon ){
					li.addClass('has-icon');
					a.append( $('<i/>', {class: 'mrs img icon-' + data.icon}) );
				}

				a.append( label );

				if( data.label ){

					if( data.icon ){
						label.addClass('fwb');
					}

					li.addClass('has-des');
					a.append( $('<div/>', {class: 'itemDes'}).html( data.label ) );
				}

				return li;
			},

			header: function (data) {
				return $('<li/>', {class: 'header' }).html( $('<span/>',{class:'itemLabel'}).html( data.label ) );
			},

			separator: function(){
				return $('<li/>', {class: 'separator'});
			},

			user: function( data ){
				return $('<li/>', {class: 'user'})
					.addClass( data.selected ? 'selected':''  )
					.html( $('<a/>')
						.addClass('anchor anchor32')
						.html( $('<div/>').addClass('clearfix')
							.append( $('<div/>').addClass('avatar lfloat size32 mrs')
								.html( $('<img/>', {class: 'img', src: data.image}) )
							)

							.append( $('<div/>').addClass('content')
								.append( 
									$('<div/>', {class: 'spacer'}),
									$('<div/>', {class: 'massages clearfix', text: data.text})
								)
							)
						)
					);
			}
		},
		
		getOffset: function(){
			var self = this;
			
			if( self.menu.hasClass('uiContextualAbove') ){
				self.menu.removeClass('uiContextualAbove');
			}
			
			var cssMenu = { height: "", overflowY: '', overflowX: ''};
			
			self.menu.find('.uiMenu').css(cssMenu);

			var outer = $(document).height()<$(window).height()?$(window):$(document);

			var offset = self.$elem.offset(),
				position = self.$elem.offset(),
				outerWidth = $(window).width(),
				outerHeight = outer.height();
			
			position.top += self.$elem.outerHeight();
			
			var innerWidth = position.left+self.menu.outerWidth();
			if( $('html').hasClass('sidebarMode') ){
				innerWidth+= 301;
			}

			if( innerWidth>outerWidth ){
				position.left = position.left-self.menu.outerWidth()+self.$elem.outerWidth();
			}
			
			var innerHeight = position.top+self.menu.outerHeight();
			if( innerHeight>outerHeight ){
				

				position.top = position.top-self.menu.outerHeight()-self.$elem.outerHeight();

				if( position.top < 0 ){
					
					var h = outerHeight-offset.top+self.$elem.outerHeight();
					// 
					if( h>offset.top ){
						position.top = offset.top;
						position.top += self.$elem.outerHeight(); 
						cssMenu.height = outerHeight-position.top-15;
					}
					else{
						position.top = position.top*-1

						position.top = $('html').hasClass('hasModal')? 15: 65;

						cssMenu.height = (offset.top-7)-position.top;

						self.menu.addClass('uiContextualAbove');
					}
					
					cssMenu.overflowY = 'auto';
					cssMenu.overflowX = 'hidden';
					self.menu.find('.uiMenu').css(cssMenu);
					
				}else{
					position.top+=2;
					self.menu.addClass('uiContextualAbove'); 
				}
			}

			self.menu.css( position );
		}
	};
	$.fn.selectbox = function( options ) {
		return this.each(function() {
			var toggle = Object.create( Selectbox );
			toggle.init( options, this );
			$.data( this, 'selectbox', toggle );
			
		});};
	$.fn.selectbox.options = {
		display: true,
        onSelected: function () { },
        onComplete: function () { },
    };
	
	/**/
	/* Datepicker */
	/**/
	var Datepicker = {
		init: function( options, elem ) {
			var self = this;
			
			self.elem = elem;
			self.$elem = $( elem );
			
			self.options = $.extend( {}, $.fn.datepicker.options, options );
			
			self.options.lang = {
				lang: self.options.lang,
				type: "short"
			}

			if( self.$elem.val() ){
				self.options.selectedDate = new Date( self.$elem.val() );
			}

			if( !self.options.selectedDate ){
				self.options.selectedDate = new Date();
			}
			self.options.selectedDate.setHours(0, 0, 0, 0);
			
			// set date
			self.date = {
				today: new Date(),
				theDate: new Date( self.options.selectedDate ),
				selected: self.options.selectedDate,
				startDate: self.options.startDate
			};
			self.date.today.setHours(0, 0, 0, 0);

			if( self.date.startDate ){

				/*
				if( self.date.startDate.getTime()>self.date.selected.getTime() ){
					self.date.startDate = new Date( self.date.selected );
				}*/
				self.date.startDate.setHours(0, 0, 0, 0);
			}
			
			var lang = Object.create( Datelang );
			lang.init( self.options.lang );
			self.string = lang;

			self.setElem();
	
			self.getSlected();

			self.active = false;
			self.elemCalendar();
			self.initEvent();
		},

		gYear: function ( date ) {

			var self = this, 
				year = date.getFullYear();

			
			if( self.options.lang.lang=='th' ){
				year = year+543;
			}
			
			if( self.options.style=='short' ){
				year = year.toString().substr(2, 4);
			}

			return year;
		},
		
		getSlected: function(){
			var self = this;
			// this.date.selected
			//  'normal'
			// 'normal'

			var textDate = 	
				self.string.day( self.date.selected.getDay(), self.options.style ) + 
				(self.options.style=='normal' ? "ที่ ": ' ') +
				self.date.selected.getDate() + " " +
				self.string.month( self.date.selected.getMonth(), self.options.style ) + " " +
				self.gYear( self.date.selected );

			var date_str = self.date.selected.getDate();
			date_str = date_str<10?"0"+date_str:date_str;

			var month_str = self.date.selected.getMonth()+1;
			month_str = month_str<10?"0"+month_str:month_str;

			var valDate = 	self.date.selected.getFullYear() + "-" +
							month_str + "-" +
							date_str;

			self.selectedText.text( textDate );
			self.selectedInput.val( valDate );

			if ( typeof self.options.onSelected === 'function' ) {
				self.options.onSelected.apply( self.elem, arguments );
			}
		},

		setElem: function(){
			var self = this;
			
			self.selectedInput = $('<input>', {
				class: 'hiddenInput',
				type: 'hidden',
				name: self.$elem.attr('name')
			});
			self.selectedText = $('<span>', {class: 'btn-text'});
				
			self.original = self.$elem;

			var placeholder = $('<div/>', {class: 'uiPopover'});
			
			self.$elem.replaceWith(placeholder);
            self.$elem = placeholder;
			
			self.$btn = $('<a>', {class: 'btn btn-box btn-toggle'}).append( self.selectedText );

			if( !self.options.icon ){
				self.$btn.append( $('<i/>', {class: 'img mls icon-angle-down'}) );
			}

			self.$elem.append( self.$btn, self.selectedInput);
			self.calendar = {};
		},
		
		initEvent: function(){
			var self = this;
			
			self.$btn.click(function(e){
				
				$('body').find('.uiPopover').find('a.btn-toggle.active').removeClass('active');
				if( self.$calendar.hasClass('open') ){
					self.close();
				}
				else{
					self.$btn.addClass('active');
					self.display();
					self.open();
				}
				
				e.stopPropagation();
				
			});
			
			$('html').on('click', function() {
		
				if( self.active && self.$calendar.hasClass('open') ){
					self.$btn.removeClass('active');
					self.close();
				}
				
			});

			self.$calendar.bind('mousewheel', function(e){

				if( self.is_loading ) return false;

				var offset = e.originalEvent.wheelDelta /120 > 0 ? -1 : 1;
				var newDate = new Date( self.date.theDate );
				newDate.setMonth( self.date.theDate.getMonth() + offset);
				self.date.theDate = newDate;

				self.updateCalendar();
				e.stopPropagation();

				/*if(e.originalEvent.wheelDelta /120 > 0) {
					console.log('scrolling up !');
				}
				else{
					console.log('scrolling down !');
				}*/
			});
		},

		display: function(){
			var self = this;

			self.updateCalendar();

			$('body').append( self.$calendar );
			
			if( $('body').find('.open.uiContextualPositioner').length>0 ){
				$('body').find('.open.uiContextualPositioner').removeClass('open');
			}
			
			if( $('body').find('.openToggler.uiToggle').length>0 ){
				$('body').find('.openToggler.uiToggle').removeClass('openToggler');
			}
		},
		
		open: function(){
			var self = this;
			self.active = true;
			
			self.getOffset();
			self.$calendar.addClass('open');
		},
		
		close: function(){
			var self = this;
			
			self.active = false;			
			self.$calendar.removeClass('open');
		},
		
		setCalendar: function(){
			
			var self = this;
			self.is_loading = true;
 			var theDate = new Date( self.date.theDate );

			var firstDate = new Date( theDate.getFullYear(), theDate.getMonth(), 1);
			firstDate = new Date(theDate);
	        firstDate.setDate(1);
	        var firstTime = firstDate.getTime();
			var lastDate = new Date(firstDate);
	        lastDate.setMonth(lastDate.getMonth() + 1);
	        lastDate.setDate(0);
	        var lastTime = lastDate.getTime();
	        var lastDay = lastDate.getDate();
			
			// Calculate the last day in previous month
	        var prevDateLast = new Date(firstDate);
	        prevDateLast.setDate(0);
	        var prevDateLastDay = prevDateLast.getDay();
	        var prevDateLastDate = prevDateLast.getDate();

	        var prevweekDay = self.options.weekDayStart;
	
			prevweekDay = prevweekDay>prevDateLastDay
				? 7-prevweekDay
				: prevDateLastDay-prevweekDay;

			self.calendar.lists = [];
			for (var y = 0, i = 0; y < 7; y++){

				var row = [];
				var weekInMonth = false;

				for (var x = 0; x < 7; x++, i++) {
					var p = ((prevDateLastDate - prevweekDay ) + i);

					var call = {};
					var n = p - prevDateLastDate;
					call.date = new Date( theDate ); 
					call.date.setHours(0, 0, 0, 0); 
					call.date.setDate( n );

					// If value is outside of bounds its likely previous and next months
	            	if (n >= 1 && n <= lastDay){
	            		weekInMonth = true;

	            		if( self.date.today.getTime()==call.date.getTime()){
	                    	call.today = true;
	                    }

	                    if( self.date.selected.getTime()==call.date.getTime() ){
	                    	call.selected = true;
	                    }
	            	}
	            	else{
	            		call.noday = true;
	            	}

	            	if( self.date.startDate ){
                    	if( self.date.startDate.getTime()>call.date.getTime() ){
                    		call.empty = true;
                    	}
                    }
                    
					row.push(call);
				}

				if( row.length>0 && weekInMonth ){
					self.calendar.lists.push(row);
				}
			}

			self.calendar.header = [];
			for (var x=0,i=self.options.weekDayStart; x<7; x++, i++) {
				if( i==7 ) i=0;
				self.calendar.header.push({
	        		key: i,
	        		text: self.string.day( i )
	        	});
			};
		},

		elemCalendar: function(){
			var self = this;
			
			self.$calendar = $('<div>', {class: 'uiContextualPositioner'})
				.append( $('<div>', {class: 'toggleFlyout calendarGridTableSmall'}) );
				
			if( self.options.max_width ){
				self.menu.find('.toggleFlyout').css('width', self.options.max_width);
			}
		},

		updateCalendar: function(){
			var self = this;

			self.setCalendar();

			var $title = $('<thead>').html( $("<tr>", {class: 'title'})
				.append( $('<td>', {class: 'prev'}).append( $('<i/>', {class:'icon-angle-left'}) ) )
				.append( $('<td>', {class: 'title', colspan: 5, text: self.string.month( self.date.theDate.getMonth(), 'normal' ) + " " + self.date.theDate.getFullYear() }) )
				.append( $('<td>', {class: 'next'}).append( $('<i/>', {class:'icon-angle-right'}) ) )
			)

			var $header = $("<tr>", {class: 'header'});
			$.each( self.calendar.header, function(i, obj){
				$header.append( $('<th>', {text: obj.text}) );
			});
			$thead = $('<thead/>').html( $header );
			
			var $tbody = $('<tbody>');
			$.each(self.calendar.lists, function (i, row) {
				$tr = $('<tr>');
				$.each( row, function(j, call){

					call.cls = "";
					// call.date/
					var datestr = call.date.getFullYear()+"-"+ (call.date.getMonth()+1)+"-"+call.date.getDate();

					if( self.options.start ){

						if( self.options.start.getTime() == call.date.getTime() ){
							call.cls += ' select-start';
						}

						if( self.options.start.getTime() > call.date.getTime() ){
							call.overtime = true;
						}
					}

					if( self.options.end ){

						if( self.options.end.getTime() == call.date.getTime() ){
							call.cls += ' select-end';
						}

						if( self.options.end.getTime() < call.date.getTime() ){
							call.overtime = true;
						}
					}


					$tr.append( 
						$('<td>',{'data-date': datestr })

							.addClass( call.empty?'empty':'' )
							.addClass( call.today?'today':'' )
							.addClass( call.selected?'selected':'' )
							.addClass( call.noday?'noday':'' )
							.addClass( call.overtime?'overtime':'' )
							.addClass( call.cls )
							.addClass( call.date.getDay()==6 || call.date.getDay()==0?'weekHoliday':'' )
							.html( $('<span>', { text: call.date.getDate() }) )
					);
				});

				$tbody.append( $tr );
							
			});

			self.$calendar
				.find('.calendarGridTableSmall')
					.html( $('<table/>', { class: 'calendarGridTable', cellspacing: 0, cellpadding: 0 })
					.addClass( self.options.format )
					.append( $title, $thead, $tbody )
				);

			self.is_loading = false;


			// event 
			$('td[data-date]', self.$calendar).click(function(e){

				if( $(this).hasClass('empty') || ($(this).hasClass('noday')&&self.$calendar.find('.calendarGridTable').hasClass('range'))  ){
					e.stopPropagation();
					return false;
				}

				var selected = new Date( $(this).attr('data-date') );
				selected.setHours(0, 0, 0, 0);

				if( self.$calendar.find('.calendarGridTable').hasClass('range') ){

					if( self.$calendar.find('.calendarGridTable').hasClass('start') && self.options.start ){

						if( self.options.end ){

							if( selected.getTime() > self.options.end.getTime() ){
								e.stopPropagation();
								return false;
							}
						}

						self.options.start = new Date( selected );
					}

					if( self.$calendar.find('.calendarGridTable').hasClass('end') && self.options.end ){

						if( self.options.start ){

							if( selected.getTime() < self.options.start.getTime() ){
								e.stopPropagation();
								return false;
							}
						}


						self.options.end = new Date( selected );
					}
				}

				self.date.selected = selected
				
				self.date.theDate = new Date( $(this).attr('data-date') );
				self.date.theDate.setHours(0, 0, 0, 0);

				self.getSlected();
			});

			$('td.prev, td.next', self.$calendar).click(function(e){

				var offset = $(this).hasClass("prev") ? -1 : 1;
				var newDate = new Date( self.date.theDate );
				newDate.setMonth( self.date.theDate.getMonth() + offset);
				self.date.theDate = newDate;

				self.updateCalendar();

				e.stopPropagation();
			});
		},
		
		getOffset: function(){
			var self = this;
			
			if( self.$calendar.hasClass('uiContextualAbove') ){
				self.$calendar.removeClass('uiContextualAbove');
			}
			
			var outer = $(document).height()<$(window).height()?$(window):$(document);

			var offset = self.$elem.offset(),
				outerWidth = $(window).width(),
				outerHeight = outer.height();

			var position = offset;
			
			position.top += self.$elem.outerHeight();
			
			var innerWidth = position.left+self.$calendar.outerWidth();
			if( $('html').hasClass('sidebarMode') ){
				innerWidth+= 301;
			}

			if( innerWidth>outerWidth ){
				position.left = offset.left-self.$calendar.outerWidth()+self.$elem.outerWidth();
			}
			
			var innerHeight = position.top+self.$calendar.outerHeight();
			if( innerHeight>outerHeight ){
				position.top = offset.top-self.$calendar.outerHeight()-self.$elem.outerHeight();
				self.$calendar.addClass('uiContextualAbove'); 
			}

			self.$calendar.css( position );
		}};
	$.fn.datepicker = function( options ) {
		return this.each(function() {
			var $this = Object.create( Datepicker );
			$this.init( options, this );
			$.data( this, 'datepicker', $this );
		});
	};
	$.fn.datepicker.options = {
		lang: 'th',
		selectedDate: null,
		start: null,
		end: null,
		weekDayStart: 1,
		style: 'short',
		format : '',
		onSelected: function () { },
	};
	
	/**/
	/* ToggleLink */
	/**/
	var ToggleLink = {
		init: function( options, elem ) {
			
			var self = this;
			
			self.elem = elem;
			self.$elem = $( elem );
			
			self.options = $.extend( {}, $.fn.toggleLink.options, options );
			
			self.setElem();
			
			self.active = false;

			// Event
			self.initEvent();
		},
		setElem: function(){
			var self = this;
			
			self.$elem.addClass('btn-toggleLink').removeAttr('rel');
			
			self.$elem = self.$elem.parents('.uitoggleLink');
			self.$btn = self.$elem.find('a.btn-toggleLink');
			self.$menu = self.$elem.find('.uitoggleLinkFlyout');
			
			self.setOffset();
		},
		
		setElem: function(){
			var self = this;
			
			self.$elem.addClass('btn-toggle').removeAttr('rel');
			
			self.$elem = self.$elem.parents('.uiToggle');
			self.$btn = self.$elem.find('a.btn-toggle');
			self.$menu = self.$elem.find('.uiToggleFlyout');
			
			self.setOffset();
		},
		
		initEvent: function(){
			var self = this;
			
			self.$btn.click(function(e){
				
				$('body').find('.uiPopover, .uiToggle').find('a.btn-toggle.active').removeClass('active');
				if( self.$elem.hasClass('openToggler') ){
					self.close();
				}
				else{
					self.$btn.addClass('active');
					self.display();
					self.open();
				}
				
				e.preventDefault();
				e.stopPropagation();
				
			});
			
			self.$menu.find('a').click(function(){
				self.selected = $(this).parent().index();
				if ( typeof self.options.onSelected === 'function' ) {
					self.options.onSelected.apply( self, arguments );
				}
			});

			$('html').on('click', function() {
		
				if( self.active && self.$elem.hasClass('openToggler') ){
					self.$btn.removeClass('active');
					self.close();
				}
				
			});
		},
		
		display: function(){
			var self = this;
			
			if( $('body').find('.open.uiContextualPositioner').length>0 ){
				$('body').find('.open.uiContextualPositioner').removeClass('open');
			}
			
			if( $('body').find('.openToggler.uiToggle').length>0 ){
				$('body').find('.openToggler.uiToggle').removeClass('openToggler');
			}
		},
		
		open: function(){
			var self = this;
			self.active = true;
			
			self.$elem.addClass('openToggler');
			self.getOffset();
		},
		
		close: function(){
			var self = this;
			
			self.active = false;			
			self.$elem.removeClass('openToggler');
		},
		
		setOffset: function(){
			var self = this;
			
			var outer = $(document).height()<$(window).height()?$(window):$(document);

			self.$menu.find('.uiMenu').css({ overflowY: '', overflowX: '',height: '',minHeight:'',minWidth:''});

			var offset = self.$elem.offset(),
				outerWidth = $(window).width(),
				outerHeight = outer.height();
			
			var position = offset;

			position.top += self.$elem.outerHeight();
			
			var innerWidth = position.left+self.$menu.outerWidth();

			if( $('html').hasClass('sidebarMode') ){
				innerWidth+= 301;
			}

			if( innerWidth>=outerWidth || self.options.right ){
				self.$menu.addClass('uiToggleFlyoutRight'); 
			}
			else if( self.$menu.hasClass('uiToggleFlyoutRight') ){
				self.$menu.removeClass('uiToggleFlyoutRight');
			}
			
			var innerHeight = position.top+self.$menu.outerHeight();

			innerHeight += 30;
			if( innerHeight>outerHeight || self.options.above ){
				self.$menu.addClass('uiToggleFlyoutAbove'); 
			}else if( self.$menu.hasClass('uiToggleFlyoutAbove') ){
				self.$menu.removeClass('uiToggleFlyoutAbove');
			}
		},

		getOffset: function(){
			var self = this;

			self.setOffset();

			var outer = $(document).height()<$(window).height()?$(window):$(document);

			var offset = self.$elem.offset(),
				outerWidth = $(window).width(),
				outerHeight = outer.height();

			if( self.$menu.hasClass('fixedMenu') ){
				outerHeight = $(window).height();
				offset.top -= $(window).scrollTop();
			}

			var innerHeight = outerHeight-offset.top;
			if(innerHeight>offset.top){
				if( self.$menu.hasClass('uiToggleFlyoutAbove') ){
					self.$menu.removeClass('uiToggleFlyoutAbove');
				}
			}

			innerHeight = offset.top+self.$menu.outerHeight()+self.$elem.outerHeight()
			if( innerHeight>outerHeight && !self.$menu.hasClass('uiToggleFlyoutAbove') ){
				self.$menu.find('.uiMenu').css({
					overflowY: 'auto',
					overflowX: 'hidden',
					height: outerHeight-(offset.top+self.$elem.outerHeight()+15),
					minHeight: 180,
					minWidth:180
				});
			}
		}
	};
	$.fn.toggleLink = function( options ) {
		return this.each(function() {
			var $this = Object.create( ToggleLink );
			$this.init( options, this );
			$.data( this, 'toggleLink', $this );
		});
	};
	$.fn.toggleLink.options = {
		right: false,
		above: false,
        onSelected: function () { },
	};

	/**/
	/* Toggle */
	/**/
	var Toggle = {
		init: function( options, elem ) {
			
			var self = this;
			
			self.elem = elem;
			self.$elem = $( elem );
			
			self.options = $.extend( {}, $.fn.toggle.options, options );
			
			self.setElem();
			self.active = false;

			// Event
			self.initEvent();

		},
		setElem: function(){			
			var self = this;

			var placeholder = $('<div/>', {class: 'uiToggle'});
			
			self.$elem.replaceWith(placeholder);
            self.$elem = placeholder;

            var text = self.options.title.text || self.options.title;

			self.$btn = $('<a>')
				.addClass( 'btn-toggle' )
				.addClass( self.options.title.class || 'btn' )
				.append( 
					
					text != ''
						? $('<span/>', {class: 'btn-text'}).html( text )
						: ''

					, self.options.title.icon 
						? $('<i/>', {class: 'icon-' + self.options.title.icon })
						: ''
				);


			if( self.options.title.ricon ){
				self.$btn.append( $('<i/>', {class: 'img mls icon-angle-down'}) );
			}

			self.$elem.append( self.$btn );
			
			self.getMenu();
			self.setOffset();
		},

		getMenu: function(){
			var self = this;
			
			var ul = $('<ul>', {class: 'uiMenu'});
			$.each(self.options.menu, function (i, data) {
				
				
				ul.append( self.setMenuItem[data.type || 'default']( data ) );

				if( data.divider ){
					ul.append( self.setMenuItem.separator() );	
				}	
			});
			
			self.$menu = $('<div>', {class: 'uiContextualPositioner'})
				.css({
					left: -1000
				})
				.append(
					$('<div>', {class: 'uiToggleFlyout'}).append( ul )
				);
				
			if( self.options.max_width ){
				self.$menu.find('.uiToggleFlyout').css('width', self.options.max_width);
			}

			Event.plugins( self.$menu );
			$( 'body' ).append( self.$menu );
		},
		setMenuItem: {
			default: function( data ){
				var li = $('<li/>');
				var a = $('<a/>', data.attr);

				if( data.url ){
					a.attr('href', data.url);
				}

				var label = $('<span/>', {class: 'itemLabel', text: data.text});

				li.addClass( data.selected ? 'selected':''  ).append( a );

				if( data.icon ){
					li.addClass('has-icon');
					a.append( $('<i/>', {class: 'mrs img icon-' + data.icon}) );
				}

				a.append( label );

				if( data.label ){

					if( data.icon ){
						label.addClass('fwb');
					}

					li.addClass('has-des');
					a.append( $('<div/>', {class: 'itemDes'}).html( data.label ) );
				}

				return li;
			},

			separator: function(){
				return $('<li/>', {class: 'separator'});
			},
		},
		
		initEvent: function(){
			var self = this;
			
			self.$btn.click(function(e){
				
				$('body').find('.uiPopover, .uiToggle').find('a.btn-toggle.active').removeClass('active');

				if( self.$elem.hasClass('openToggler') ){
					self.close();
				}
				else{
					self.$btn.addClass('active');
					self.display();
					self.open();

					
				}
				
				e.preventDefault();
				e.stopPropagation();
				
			});
			
			/*self.$menu.find('a').click(function(){
				self.selected = $(this).parent().index();
				if ( typeof self.options.onSelected === 'function' ) {
					self.options.onSelected.apply( self, arguments );
				}
			});*/

			$('html').on('click', function() {
				
				if( self.active && self.$elem.hasClass('openToggler') ){
					self.$btn.removeClass('active');
					self.close();
				}
				
			});
		},
		
		display: function(){
			var self = this;
			
			if( $('body').find('.uiContextualPositioner').length>0 ){
				$('body').find('.uiContextualPositioner').removeClass('open');
			}
			
			if( $('body').find('.openToggler.uiToggle').length>0 ){
				$('body').find('.openToggler.uiToggle').removeClass('openToggler');
			}
		},
		
		open: function(){
			var self = this;
			self.active = true;
			
			self.getOffset();
			self.$menu.addClass('open');
			self.$elem.addClass('openToggler');
		},
		
		close: function(){
			var self = this;
			
			self.active = false;

			self.$menu.removeClass('open');
			self.$elem.removeClass('openToggler');
		},
		
		setOffset: function(){
			var self = this;
			
			var outer = $(document).height()<$(window).height()?$(window):$(document);
			var offset = self.$elem.offset(),
				outerWidth = $(window).width(),
				outerHeight = outer.height();

			var top = offset.top + self.$elem.outerHeight();
			var left = offset.left;

			
			if( self.options.position ){
				self.$menu.children().addClass('uiToggleFlyoutRight');
				left += self.$elem.outerWidth();
			}

			if( self.options.pointer ){
				self.$menu.children().addClass('uiToggleFlyoutPointer');
			}

			self.$menu.css({
				top: top,
				left: left,
			});

		},

		getOffset: function(){
			var self = this;

			self.setOffset();

			var outer = $(document).height()<$(window).height()?$(window):$(document);

			var offset = self.$elem.offset(),
				outerWidth = $(window).width(),
				outerHeight = outer.height();

			if( self.$menu.hasClass('fixedMenu') ){
				outerHeight = $(window).height();
				offset.top -= $(window).scrollTop();
			}

			var innerHeight = outerHeight-offset.top;
			if(innerHeight>offset.top){
				if( self.$menu.hasClass('uiToggleFlyoutAbove') ){
					self.$menu.removeClass('uiToggleFlyoutAbove');
				}
			}

			innerHeight = offset.top+self.$menu.outerHeight()+self.$elem.outerHeight()
			if( innerHeight>outerHeight && !self.$menu.hasClass('uiToggleFlyoutAbove') ){
				self.$menu.find('.uiMenu').css({
					overflowY: 'auto',
					overflowX: 'hidden',
					height: outerHeight-(offset.top+self.$elem.outerHeight()+15),
					minHeight: 180,
					minWidth:180
				});
			}
		}};
	$.fn.toggle = function( options ) {
		return this.each(function() {
			var toggle = Object.create( Toggle );
			toggle.init( options, this );
			$.data( this, 'toggle', toggle );
		});};
	$.fn.toggle.options = {
		title: '',
		menu: [],
        onSelected: function () { },
	};

	/**/
	/* ChooseFile */
	/**/
	var ChooseFile = {
		init: function( options, elem ) {
			var self = this;

			self.elem = elem;
			self.$elem = $( elem );
			self.options = options;

			self.$name = self.$elem.find('[data-name]');
			self.defaultName = self.$name.attr('data-name');
			self.$name.removeAttr('data-name');

			self.$remove = self.$elem.find('[data-remove]');
			self.$remove.removeAttr('data-remove');

			self.$file = self.$elem.find('input[type=file]');

			self._event();
			self._change();
		},

		_event: function() {
			var self = this;

			self.$file.change(function(e) {
				self.files = this.files;
				self._change();
			});

			self.$remove.click(function() {

				self.files = null;
				self._change();
			});
		},

		_change:function () {
			var self = this;

			if( !self.files ){
				self.$file.val("");
				self.$name.text( self.defaultName );
				self.$remove.addClass('hidden_elem');
				
			}
			else{
				self.$name.text( self.files[0].name );
				self.$remove.removeClass('hidden_elem');
			}
		},};
	$.fn.chooseFile = function( options ) {
		return this.each(function() {
			var obj = Object.create( ChooseFile );
			obj.init( options, this );
			$.data( this, 'chooseFile', obj );
		});
	};


	var LiveClock = {
		init: function( options, elem ) {
			var self = this;
			
			self.elem = elem;
			self.$elem = $( elem );

			self.options = $.extend( {}, $.fn.liveclock.options, options );

			self.$clock = self.$elem.find('[data-clock-text]');
			self.$date = self.$elem.find('[data-date-text]');
			self.refresh( 1 );

			if( self.$elem.find('[data-timezone]') ){

				var d =  new Date();
				// var utc = d.getTime() + (d.getTimezoneOffset() * 60000);

				// self.$elem.find('[data-timezone]').text( Math.floor( d.getTime() / 1000 ) );
			}
			
			// self.$date = self.$elem.find('.plugin-date');

		},

		refresh: function ( length ) {
			var self = this;

			setTimeout(function(){

				var theData = new Date();
				var minute = theData.getMinutes();
				minute = minute<10 ? "0"+minute:minute;

				var sec = theData.getSeconds();
				sec = sec<10 ? "0"+sec:sec;

				var hour = theData.getHours();
				// hour = hour < 10 ? "0" + hour : hour;

				var clock = '<span class="hour n'+theData.getHours()+'">' + hour + '</span>:<span class="minute">' + minute + '</span>:' + sec;

				self.$clock.html( clock );

				if ( self.$date ){

					if( self.options.lang=='th' ){

						self.$date.html( Datelang.day( theData.getDay(), self.options.type, self.options.lang )  + "ที่ " + theData.getDate() + " " + Datelang.month( theData.getMonth(), self.options.type, self.options.lang ) );
					}
					else{

						self.$date.html( Datelang.day( theData.getDay(), self.options.type, self.options.lang )  + ", " + theData.getDate() + " " + Datelang.month( theData.getMonth(), self.options.type, self.options.lang ) );
					}


					
				}
				// self.$date.html( date );

				if( self.options.refresh ){
					self.refresh();
				}

			}, length || self.options.refresh );
		}

	}
	$.fn.liveclock = function( options ) {
		return this.each(function() {
			var obj = Object.create( LiveClock );
			obj.init( options, this );
			$.data( this, 'liveclock', obj );
		});
	};
	$.fn.liveclock.options = {
        lang: 'th',
        type: 'normal',
        refresh: 1000
    };

	/**/
	/* Clock */
	var Clock = {
		init: function( options, elem ) {
			var self = this;
			
			self.elem = elem;
			self.$elem = $( elem );

			self.options = $.extend( {}, $.fn.clock.options, options );

			self.$clock = self.$elem.find('.plugin-clock');
			self.$date = self.$elem.find('.plugin-date');

			var lang = Object.create( Datelang );
			lang.init( self.options );
			self.string = lang;

			self.refresh( 1 );
		},

		refresh: function( length ){
			var self = this;

			setTimeout(function(){

				var theData = new Date();
				var minute = theData.getMinutes();
				minute = minute<10 ? "0"+minute:minute;
				var clock = theData.getHours() + "<span>:</span>" + minute;

				var date = self.string.day( theData.getDay() );
				date += 'ที่ ' + theData.getDate();
				date += " " + self.string.month( theData.getMonth() );
				date += " " + theData.getFullYear();

				self.$clock.html( clock );
				self.$date.html( date );

				if( self.options.refresh ){
					self.refresh();
				}

			}, length || self.options.refresh );

		}};
	$.fn.clock = function( options ) {
		return this.each(function() {
			var obj = Object.create( Clock );
			obj.init( options, this );
			$.data( this, 'Clock', obj );
		});
	};
	$.fn.clock.options = {
        lang: 'th',
        type: 'normal',
        refresh: 1000
    };

    /**/
    /* changeForm */
    /**/
	var changeForm = {
		init: function ( elem ) {
			var self = this;

			self.elem = elem;
			self.$elem = $(elem);

			self.$btnSubmit = self.$elem.find('.btn.btn-submit');

			self.setDefault();
			self.initEvent();
		},

		setDefault: function() {
			var self = this;

			$.each( self.$elem.find(':input'), function () {

				var type = $(this).attr('type');
				var defaultValue = $(this).val();
				
				if( type=='radio' ){
					var name = $(this).attr('name');
					this.default_value = self.$elem.find('input[name=' + name + ']:checked').val();
				}

				this.defaultValue = defaultValue;

			});
		},

		initEvent: function () {
			var self = this;

			self.$elem.find(':input').change(function () {
				self.update();
			});

			self.$elem.find('input[type=text],input[type=password],input[type=email],textarea').keyup(function () {
				self.update();
			});
		},

		update: function ( $el ) {
			var self = this, disabled = false;

			$.each( self.$elem.find(':input'), function () {
				var obj = $(this);
				var default_value = this.defaultValue;
				var currentVal = obj.val();

				if( obj.attr('type')=='radio' ){

					default_value = this.default_value
					currentVal = self.$elem.find('input[name=' + obj.attr('name') + ']:checked').val();
				}

				if( default_value != currentVal ){
					disabled = true;
					return false;
				}
			});

			// display
			if( self.$btnSubmit.hasClass('disabled') && disabled==true ){
				self.$btnSubmit.removeClass('disabled');
			}
			else if( !self.$btnSubmit.hasClass('disabled') && disabled==false ){
				self.$btnSubmit.addClass('disabled');
			}
		}};
	$.fn.changeForm = function() {
		return this.each(function() {
			var change = Object.create( changeForm );
			change.init( this );
			$.data( this, 'clock', change );
		});
	};

	/**/
	/* save as Picture */
	var save_as_picture = {
		init: function (options,elem) {
			var self = this;

			self.elem = elem;
			self.$elem = $(elem);

			self.$input = self.$elem.find('input[type=file]');
			self.options = $.extend( {}, $.fn.save_as_picture.options, options );

			self.form = self.options.form
			// self.$form = $(self.form);

			self.$input.change(function () {
				self.file = this.files[0];
				self.$image = $('<img/>', {class: 'img img-preveiw',alt: ''});
				self.setImage( );

				$(this).val("");
			});

		},

		setImage: function( ){
			var self = this;

			var reader = new FileReader();
			reader.onload = function(e){

     			var image = new Image();
	            image.src = e.target.result;
	            image.onload = function() {

	            	self.$image.attr('src', e.target.result);
	            	Event.showMsg({ load:true });
	            	self.display();
	            }
			}
			reader.readAsDataURL( self.file );
		},

		display: function () {
			var self = this;
			/*$('<div/>', {class:'img-preveiw'}).css({
					height:436,
					width:436
				})*/
			Dialog.open({
				form: self.form,
				title: 'ปรับขนาดรูปภาพ',
				body: '<div class="img-preveiw"></div>', //self.setCropimage().html(),
				onOpen: function ( response ) {

					response.$dialog.find('.img-preveiw').html( self.setCropimage() );
					self.preveiw();

					response.$dialog.find('form').submit(function(e){
						e.preventDefault();
						var $form = $(this);

						var formData = new FormData();

						// set field
						$.each($form.serializeArray(), function (index, field) {
							formData.append(field.name, field.value);
				        });

				        formData.append('file1', self.file);

				        Event.inlineSubmit($form, formData).done(function( result ){

				        	Event.processForm($form, result);
				        	Dialog.close();
				        	
				        }).fail(function(){

				        }).always(function(){

				        });
					});
				},
				button: '<button class="btn btn-blue btn-submit" type="submit" ><span class="btn-text">บันทึก</span></button><a role="dialog-close" class="btn js-close-dialog btn-white"><span class="btn-text">ยกเลิก</span></a>'
			});
		},

		preveiw: function(){

			var self = this;
			if (typeof $.fn['cropper'] !== 'undefined') {
				self.$image.cropper( self.options );
				Event.hideMsg();
			}
			else{
				Event.getPlugin( 'cropper' ).done(function () {
					self.$image.cropper( self.options );
					Event.hideMsg();
				}).fail(function () {
					console.log( 'Is not connect plugin:' );
					Event.hideMsg();
				});
			}
		},

		setCropimage: function () {
			var self = this;

			var $preveiw = $('<div>', {class: 'image-preveiw'});
			var $dataX = $('<input/>', {type:"hidden",autocomplete:"off",name:"cropimage[X]"});
		    var $dataY = $('<input/>',{type:"hidden",autocomplete:"off",name:"cropimage[Y]"});
		    var $dataHeight = $('<input/>',{type:"hidden",autocomplete:"off",name:"cropimage[height]"});
		    var $dataWidth = $('<input/>',{type:"hidden",autocomplete:"off",name:"cropimage[width]"});$('#dataWidth');
		    var $dataRotate = $('<input/>',{type:"hidden",autocomplete:"off",name:"cropimage[rotate]"});
		    var $dataScaleX = $('<input/>',{type:"hidden",autocomplete:"off",name:"cropimage[scaleX]"});
		    var $dataScaleY = $('<input/>',{type:"hidden",autocomplete:"off",name:"cropimage[scaleY]"});

		    self.options.crop = function (e) {
	            $dataX.val(Math.round(e.x));
	            $dataY.val(Math.round(e.y));
	            $dataHeight.val(Math.round(e.height));
	            $dataWidth.val(Math.round(e.width));
	            $dataRotate.val(e.rotate);
	            $dataScaleX.val(e.scaleX);
	            $dataScaleY.val(e.scaleY);
	        }

			return $preveiw.css({
				height:460,
				width:460
			}).append(
				$dataX,
				$dataY,
				$dataWidth,
				$dataHeight,
				$dataRotate,
				$dataScaleX,
				$dataScaleY,
				self.$image
			);
		}};
	$.fn.save_as_picture = function(options) {
		return this.each(function() {
			var $this = Object.create( save_as_picture );
			$this.init( options,this );
			$.data( this, 'save_as_picture', $this );
		});
	};
	$.fn.save_as_picture.options = {
		aspectRatio: 1,
		autoCropArea: 1,
		// preview: '.img-preveiw',
		strict: true,
		guides: true,
		highlight: false,
		dragCrop: false,
		cropBoxMovable: true,
		cropBoxResizable: false,
		onCallback: function () {},
	};

	/**/
	/* playYoutube */
	var playYoutube = {
		init: function (options, elem) {
			var self = this;

			self.elem = elem;
			self.$elem = $(elem);

			if(!options.url){
				options.URL = self.$elem.attr('data-url');
			}

			options.max_width = self.$elem.width();

			if( !options.URL ) return false;

			options.onReady = function () {
				console.log('onReady');
			};

			options.onError = function () {
				console.log('Error');
			};

			uiElem.iframePlayer.youtube.init(options, self.elem);
		}
	}

	$.fn.playYoutube = function(options) {
		return this.each(function() {
			var $this = Object.create( playYoutube );
			$this.init( options,this );
			$.data( this, 'playYoutube', $this );
		});
	};

	/**/
	/* Tooltip */
	var Tooltip = {
		init: function (options, elem) {
        	var self = this;
        	self.elem = elem;
        	self.$elem = $(elem);

        	self.options = $.extend( {}, $.fn.tooltip.options, options );

        	if( !self.options.text && self.$elem.attr('title')!='' ){
        		self.options.text = self.$elem.attr('title');
        		self.$elem.removeAttr('title');
        	}

        	self.is_show = false;
        	self.timeout = 0;
            self.Event();
        },

        Event: function(){
        	var self = this;
        	// Event
            self.$elem.mouseenter(function () {
                self.show();
            }).mouseleave(function () {
            	clearTimeout(self.timeout);
            	self.hide();
            });

             self.$elem.on('click', function(e){
            	clearTimeout(self.timeout);
            	self.hide();
            });
        },

        show: function (length) {
            var self = this;

            if( !self.options.text || self.options.text=="" ){ return false; }
            self.timeout = setTimeout(function () {

                self.is_show = true;
                self.get(); // Position

            }, length || self.options.reload );
        },

        hide: function () {
            var self = this;

            if( !self.is_show ) return false;

            self.$positioner.remove(); //remove();
            self.is_show = false;
        },

        get: function(){
            var self = this;

            self.$span = $('<span/>').html( self.options.text );
            self.$text = $('<div/>', {class: 'tooltipText'}).html( self.$span );
            self.$content = $('<div/>', {class: 'tooltipContent'}).html(self.$text);
            self.$container = $('<div/>', {class: 'uiTooltipX'}).html(self.$content);

            self.$layer = $('<div/>', {class: 'uiContextualLayer'}).html(self.$container);
            self.$positioner = $('<div/>', {class: 'uiContextualLayerPositioner uiLayer'}).html(self.$layer);

            var offset = self.$elem.offset();
            $( 'body' ).append( self.$positioner );

            if( self.$span.outerWidth() > (self.$text.outerWidth()+1) ){
            	
            	self.$text.css( 'width', self.$text.outerWidth() ).addClass('tooltipWrap');
            }
            /* else if(self.$text.hasClass('tooltipWrap')){
            	self.$text.removeClass('tooltipWrap');
            }*/
            // 
            offset.top += self.$elem.outerHeight();
            
            /*if( self.options.pointer ){
            	self.$layer.addClass('uiToggleFlyoutPointer');
            	offset.top += 12;
            }*/

           	var overflow = self.options.overflow;

           	if( !overflow ){

           		overflow = {
	           		Y: "Below",
	           		X: "Left"
	           	}

	           	var $window = $(window);
	           	var inner = {
	           		height: $window.height() - (offset.top+self.$container.outerHeight()),
	           		width: $window.width() - (offset.left+self.$container.outerWidth())
	           	}

           		if( inner.height < 0 ){
	           		overflow.Y = "Above";
	           	}

	           	if( inner.width < 0 ){
	           		overflow.X = "Right";
	           	}
           	}

           	if(overflow.X == "Right"){
           		self.$layer.css('right', 0);
           		offset.left +=self.$elem.outerWidth();
           	}

           	if(overflow.Y == "Above"){
           		self.$layer.css('bottom', 0);
           		offset.top -=self.$elem.outerHeight();
           	}
           	self.$layer.addClass("uiContextualLayer"+overflow.Y+overflow.X)
            self.$positioner.css(offset);       
        }
	}

	$.fn.tooltip = function( options ) {
		return this.each(function() {

			var data = $.data(this);
            if( data.tooltip ){
            	data.tooltip.options = $.extend( {}, data.tooltip.options, options );
            }
            else{
            	var title = Object.create( Tooltip );
				title.init( options, this );
				$.data( this, 'tooltip', title );
            }
			
		});
	};

	$.fn.tooltip.options = {
        reload: 800,
        pointer: true,
        text: ""
    };

    /*==================================================
	==================== Checked =====================
	====================================================*/
    var Checked = {
		init: function (options, elem) {
        	var self = this;
        	self.elem = elem;
        	self.$elem = $(self.elem);

        	self.options = $.extend( {}, $.fn.checkedlists.options, options );
        	self.dataSelect = [];

        	self.$elem.find('[role=item]').not('.disabled').click(function (e) {
        		e.preventDefault();

        		self.selected( $(this).index() );


        	});
        },
        selected: function ( index ) {
        	var self = this;

        	var item = self.$elem.find('[role=item]').eq(index);
        	item.toggleClass('checked', !item.hasClass('checked') );

        	if( item.hasClass('checked') ){
        		item.find('[type=checkbox]').prop( "checked", true );
        		self.dataSelect.push({
        			index: index,
        			elem: item
        		});

        		if( self.options.max ){
        			var length = self.dataSelect.length; // Object.keys(self.dataSelect).length;
        			if( length > self.options.max ){

        				$.each( self.dataSelect, function (i, obj) {
        					if( i==0 ){
        						obj.elem.removeClass('checked').find('[type=checkbox]').prop( "checked", false );
        						self.dataSelect.splice(i, 1);
        					}
        					
        				} );
        			}
        		}
        	}
        	else {
        		$.each( self.dataSelect, function (i, obj) {
        			if( obj ){
						if( obj.index==index ){
							obj.elem.find('[type=checkbox]').prop( "checked", false );
							self.dataSelect.splice(i, 1);
						}
					}
					
				} );
        	}	
        }
	}

	$.fn.checkedlists = function( options ) {
		return this.each(function() {

			var data = $.data(this);
            if( data.checkedlists ){
            	data.checkedlists.options = $.extend( {}, data.checkedlists.options, options );
            }
            else{
            	var title = Object.create( Checked );
				title.init( options, this );
				$.data( this, 'checkedlists', title );
            }
			
		});
	};

	$.fn.checkedlists.options = {
		max:1
	};
	
})( jQuery, window, document );