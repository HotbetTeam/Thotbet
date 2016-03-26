// Utility
if ( typeof Object.create !== 'function' ) {
	Object.create = function( obj ) {
		function F() {};
		F.prototype = obj;
		return new F();
	};
}

(function( $, window, document, undefined ) {

	var Liveform = {
		init: function ( options, elem ) {
			var self = this;
			self.elem = elem;

			self.setElem();

			/*self.resize();
			$( window ).resize(function () {
				self.resize();
			});*/

			// self.$elem.addClass('on');
			/*self.ids = [];
			self.Events();*/
		},
		setElem: function () {
			var self = this;
			self.$elem = $(self.elem);

			self.queue = [];
			self.loading = false;
			self.url = self.$elem.attr('action');
			// console.log( self.url );

			self.$elem.addClass('live-form');
			self.$elem.find(':input[type=text]').after( $('<i>', {class: 'icon-pencil'}) );
			
			self.inputDefault();
			/*$.each( self.$elem.find(':input'), function (i, input) {
				
				$(input).closest( '.control-group' ).find('.control-label').append( $('<a>', {
						class: 'mls fsss fcg', 
						'data-ref' : $(input).val()
					}).html( $('<i>', {class: 'icon-refresh'}) ) 

				);

			} );*/


			$('[data-ref]', self.$elem).click(function (e) {

				var $box = $(this).closest( '.control-group' );
				var val = $(this).attr('data-ref');
					
				var el = '[type=text], select';

				// input 
				if( $box.find( el ).val()!=val ){
					$box.find( el ).val( val ).focus();

					if( !$box.hasClass('has-error') ){
						self.update( $box.find( el ) );
					}
				}

				if( $box.hasClass('has-error') ){
					$box.removeClass('has-error');
				}
				
				e.preventDefault();
			});

			self.$elem.find(':input').change(function (e) {
				
				// self.setQueue( $(this) );
				self.update( $(this) );
				e.preventDefault();
			});
		},

		setQueue: function ( $el ) {
			var self = this;

			self.queue.push({
				$elem: $el,
				name: $el.attr('name'),
				val: $el.val(),
				status: 'wait'
			});
		},

		getQueue: function () {
			var self = this;

			if( self.loading ){ return false; }

			for (var i = 0; i < self.queue.length; i++) {
				var obj = self.queue[i];
				if( obj.status == 'wait' ) return obj;
			};
		},

		update: function ( $el ) {
			var self = this;
			

			if( $el.hasClass('has-error') ) $el.removeClass('has-error');
			setTimeout(function () {

				var fieldset = $el.closest('.control-group');
				self.fetch( $el.attr('name'), $el.val() ).done(function ( res ) {

					if( $el.attr('type') == 'password' ){
						$el.val("");
					}

					// 
					if( res.error ){
						fieldset.addClass('has-error').find('.notification').html( res.error_message );


						$el.addClass('has-error');
						return false;
						// var $refresh = $('<a>', {class: 'mls fsss fcg'}).html( $('<i>', {class: 'icon-refresh'}) )
						// fieldset.find('.control-label').append( $refresh );
					}

					if( fieldset.hasClass('has-error') ){
						fieldset.removeClass('has-error')
					}

					
					// console.log( res );
					self.showMsq( res.message || 'แก้ไขข้อมูลเรียบร้อย' );
				});

			}, 1);	
		},

		fetch: function ( name, text ) {
			var self = this;

			return $.ajax({
				type: 'post',
				url: self.url,
				data: { field: name, val: text },
				dataType: 'json'
			}).always(function () {
				self.inputDefault();
				// console.log( 'always' );
			}).fail(function() { 
				console.log( ' send Error ' );
			});
		},

		inputDefault: function () {
			var self = this;
			$.each( self.$elem.find(':input'), function (i, input) {
				
				$fieldset = $(input).closest( '.control-group' );
				if( $fieldset.find('[data-ref]').length==0 ){

					$fieldset.find('.control-label').append( $('<a>', {
							class: 'mls fsss fcg', 
							'data-ref' : $(input).val()
						}).html( $('<i>', {class: 'icon-refresh'}) ) 

					);
				}

				$fieldset.find('[data-ref]').toggleClass('hidden_elem', $(input).val()==$fieldset.find('[data-ref]').attr('data-ref') || $(input).attr('type')=='password' );
			} );
		},

		showMsq: function ( text ) {
			var self = this;			

			if( !self.$msq ){
				self.$msq = $('<div>', {class: 'toaster'});

				$( 'body' ).append( self.$msq );
			}
			
			self.$msq.addClass('on');
			var item = $('<div>', {class: 'toast' }).html( text );

			self.$msq.append( item );

			setTimeout(function () {
				
				item.addClass('is-closed');

				setTimeout(function () {
					item.remove();

					if( self.$msq.find('.toast').length==0 && self.$msq.hasClass('on') ){
						self.$msq.removeClass('on');
					}

				}, 1000);

			}, 3000);
			// self.$msq.append( $() );
		},


	};

	$.fn.liveform = function( options ) {
		return this.each(function() {
			var $this = Object.create( Liveform );
			$this.init( options, this );
			$.data( this, 'liveform', $this );
		});
	};

	$.fn.liveform.options = {
		widthLeft: 25,
		widthRight: 30
	};
	
})( jQuery, window, document );