// Utility
if ( typeof Object.create !== 'function' ) {
	Object.create = function( obj ) {
		function F() {};
		F.prototype = obj;
		return new F();
	};
}

(function( $, window, document, undefined ) {

	var Composer = {
		init: function( options, elem ) {
			var self = this;

			self.elem = elem;
			self.options = options;

			self.setElem();
			self.event();
		},

		setElem: function () {
			var self = this;

			self.$elem = $( self.elem );
			self.$preview = self.$elem.find('.post-preview');
			self.$attachments = self.$elem.find('.attachments');
		},
		event: function  () {
			var self = this;

			if( $('[role=media-upload-photo] [type=file]').length ){
				$('[role=media-upload-photo] [type=file]').change(function () {
					
					self.preview_photo( this.files[0] );
				});
			}
		},

		preview_photo: function ( file ) {
			var self = this;

			self.setPreviewElem();

			var width = self.$preview.width();
			var $progress = $('<div/>',{ class:'progress'}).html( $('<span>', {class: 'bar'}) );
			var $img = $('<div/>',{ class:'image-crop'});

			var $box = $('<div/>', {class: 'media-photo'}).append( $progress, $img );
			self.$preview.find('.media-container-inner').html( $box );

			var reader = new FileReader();
			reader.onload = function (e) {
				var image = new Image();
				image.src = e.target.result;
				$image = $(image);

				image.onload = function() {
					// var $img = this;

					var scaledW = this.width;
					var scaledH = this.height;

					var height = ( scaledH * width ) / scaledW;
					
					$img.css({
						width: width,
						height: height
					});
					
					self.$attachments.removeClass('has-loading').addClass('has-file');
					$img.html( $image );
				}
			}

			reader.onprogress = function(data) {
				if (data.lengthComputable) {                                            
	                var progress = parseInt( ((data.loaded / data.total) * 100), 10 );
	                $progress.find('.bar').width( progress+"%" );
	            }
        	}

			reader.readAsDataURL( file );
		},
		setPreviewElem: function () {
			var self = this;

			self.$attachments.addClass('has-loading');
			var $remove = $('<a/>', {class:"media-preview-remove"}).html( $('<i>', {class:'icon-remove'}) );

			$remove.click(function (e) {
				e.preventDefault();
				self.clearPreviewElem();
			});

			self.$preview.html( $('<div/>', {class:"media-container"}).append(
				$remove,
				$('<div/>', {class:"media-container-inner"})
			) );
		},

		clearPreviewElem: function () {
			var self = this;

			self.$preview.empty();
			self.$attachments.find('input[type=file]').each(function () {
				$(this).val("");
			});
			self.$attachments.removeClass('has-file');
		}
	}

	$.fn.composer = function( options ) {
		return this.each(function() {
			var $this = Object.create( Composer );
			$this.init( options, this );
			$.data( this, 'Composer', $this );
		});
	};

})( jQuery, window, document );