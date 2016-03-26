// Utility
if ( typeof Object.create !== 'function' ) {
	Object.create = function( obj ) {
		function F() {};
		F.prototype = obj;
		return new F();
	};
}

(function( $, window, document, undefined ) {

	var Search = {
		init: function ( options, elem ) {
			var self = this;
			self.elem = elem;
			self.$elem = $(elem);
			self.options = $.extend( {}, $.fn.search.options, options );
			self.initElem();
			self.initEvent();

		},

		initElem: function (argument) {
			var self = this;

			self.$q = self.$elem.find('input#q');
			self.$foods = self.$elem.find('input#foods');
			self.url = self.$elem.attr('action');
		},

		initEvent: function  () {
			var self = this;

			var inputs = ['q','foods', 'places'];
			$.each(inputs, function (i, val) {
				var $el = self.$elem.find('input#'+val);
				if($el.length){

					$el.focus(function () {
						self.$current = $(this);
						self.current_name = $(this).attr('name');
						self.event();

						
					}).click(function () {
						if((val=='places' || val=='foods') && $(this).val()=="") {

							self.fetch().done(function( results ) {
								self.buildFrag( results );
								self.display();
							});
						}
					});
				}
			});
			/*self.$q.focus(function () {
				self.$current = $(this);
				self.current_name = $(this).attr('name');
				self.event();
			});*/
		},

		event: function () {
			var self = this;

			self.is_refresh = '';
			self.text = "";
			self.$current.keydown(function (e){
				/*if( $(this).val() == self.search ){
					clearTimeout(self.is_refresh);
				}*/

				if ( e.keyCode == 13 ){

					window.location.href = self.url + "?"+self.current_name+"="+$(this).val();
				}

				if ( e.keyCode == 40 ){
					self.select('up');
					e.preventDefault();
				}

				if( e.keyCode == 38 ){
					self.select('down');
					e.preventDefault();

				}
				
			}).keyup(function () {
				
				clearTimeout(self.is_refresh);
				self.refresh(500, $(this).val(), $(this).attr('id'));
			}).blur(function () {
				
			}).click(function () {
				self.search = "";
				self.refresh(1, $(this).val() );
				
			});

			$(window).click(function () {
				if( self.$dialog ){ 
					self.$dialog.click(function (e) {
						e.stopPropagation();
					});


					self.$dialog.remove();
				}
			});
		},
		select: function ( e ) {
			console.log( e );
		},

		refresh: function( length, text ) {
			var self = this;
			self.is_refresh = setTimeout(function() {
				if( text == self.search) return false;

				self.search = text;
				if( self.$dialog ){ self.$dialog.remove() }
				if( self.search=="" ) return false;

				// console.log( 'loading', self.search );
				self.fetch().done(function( results ) {
					// results = self.limit( results.results, self.options.limit );

					self.buildFrag( results );
					self.display();

					if ( typeof self.options.onComplete === 'function' ) {
						self.options.onComplete.apply( self.elem, arguments );
					}

				});
			}, length );
		},

		fetch: function() {
			var self = this;

			return $.ajax({
				url: self.url+self.current_name,
				data: { q: self.search },
				dataType: 'json'
			});
		},

		buildFrag: function( results ) {
			var self = this;

			self.getMenu();
			self.lists = [];
			$.each( results.lists, function(i, obj) {

				if( obj.data.length ){
					self.$menu.append( 
						$('<li>', {class: 'header'}).html( $('<a>').append(
							$('<span/>', {class:'text', text: obj.object_name}) 
						) )
					);

					$.each(obj.data, function (j, item) {
						var $item = $('<li>').html( $('<a>', {href: item.url}).append(
							$('<span/>', {class:'text', text: item.text}),
							$('<span/>', {class:'subtext', text: item.subtext})
						) )[0];

						if( item.image_url ){
							$($item).addClass('picThumb').find('.text').before(
								$('<div/>', {class:'avatar'}).html(
									$('<img/>', { src: item.image_url,class: 'img'})
								)
							);
						}

						self.lists.push( $item );
						self.$menu.append( $item );
					});
				}
				

				// return $( self.options.wrapEachWith ).append ( obj.text )[0];
			});

			if(self.lists.length==0){
				self.$dialog.remove()
			}
		},

		display: function() {
			var self = this;

			self.$dialog.removeClass('has-loading');
		},

		getMenu: function () {
			var self = this;

			self.$view = $('<div>', {class: 'uiTypeaheadView uiContextualTypeaheadView'});
			self.$bucketed = $('<div>', {class: 'bucketed'});
			self.$menu = $('<ul/>', {class:"search",role:"listbox"});
			self.$view.html( self.$bucketed.html( self.$menu ) );

			var offset = self.$current.offset();
			offset.top += self.$current.height();
			uiLayer.set( offset, self.$view );
			self.$dialog = self.$view.parents('.uiContextualLayerPositioner');

			self.$dialog.css(offset);
			self.$dialog.addClass('open has-loading');
		}
	};

	$.fn.search = function( options ) {
		return this.each(function() {
			var $this = Object.create( Search );
			$this.init( options, this );
			$.data( this, 'search', $this );
		});
	};

	$.fn.search.options = {
		setReload: 2,
        loading: "กำลังโหลด...",
        texterror: "ไม่มีข้อมูล...",
        onItemEvent: false,
        onInputEventBlur: false,
        inputIcon: false,
        getData:null,
        menuWrapper: "",
        showSelected: "",
        placeholder: "ค้นหา...",
        datareferrer:"",
        onSelected: function() {}
	};
	
})( jQuery, window, document );