// Utility
if ( typeof Object.create !== 'function' ) {
	Object.create = function( obj ) {
		function F() {};
		F.prototype = obj;
		return new F();
	};
}

(function( $, window, document, undefined ) {

	var Banner = {
		init: function ( options, elem ) {
			var self = this;
			self.elem = elem;
			self.$elem = $(elem);

			self.options = options;

			//  
			self.$elem.find('[type=file]').change(function () {
				
				var file = this.files[0];


				self.setImage( file );
				// console.log( file );
			});
			
		},

		setImage: function( file ){
			var self = this;

			var reader = new FileReader();
			reader.onload = function(e){

     			var image = new Image();
	            image.src = e.target.result;
	            image.onload = function() {

	            	if( this.width!=self.options.width || this.height!=self.options.height ){
	            		self.error_message( 'ขนาดไม่ตรงกับที่ตั้งใว้' );
	            		return false;
	            	}

	            	self.save( file );
	            	/*self.$image.attr('src', e.target.result);
	            	Event.showMsg({ load:true });
	            	self.display();*/
	            }

			}
			reader.readAsDataURL( file );
		},

		error_message: function (text) {
			
			Event.showMsg( {text: text, auto: true, load: true, bg: 'red'} );
		},

		save: function ( file ) {
			var self = this;

			var data = new FormData();

			data.append('file1', file);
			data.append('width', self.options.width);
			data.append('height', self.options.height);
			data.append('caption', self.options.caption || '');

			$.ajax({
				type: "POST",
				url: self.options.url,
				data: data,
				dataType: 'json',
				processData: false,
        		contentType: false,
			}).always(function() { 
				// complete
			})
			.fail(function(  ) { 
				// error

			}).done(function (response) {
				
				if( response.message ){

					if( typeof response.message === 'string' ) {
						Event.showMsg( {text: response.message, load: true, auto: true} );
					}
					else{
						Event.showMsg( response.message );
					}
					
				}

				if( !response.error ){
					self.display( response.src );
				}

			});

		},
		display: function ( src ) {
			var self = this;

			self.$elem.find('.preview').html(
				$('<img>', {
					src: src
				})
			);
		}

	};

	$.fn.save_banner = function( options ) {
		return this.each(function() {
			var $this = Object.create( Banner );
			$this.init( options, this );
			$.data( this, 'save_banner', $this );
		});
	};

	$.fn.save_banner.options = {};
	
})( jQuery, window, document );