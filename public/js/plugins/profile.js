// Utility
if ( typeof Object.create !== 'function' ) {
	Object.create = function( obj ) {
		function F() {};
		F.prototype = obj;
		return new F();
	};
}

(function( $, window, document, undefined ) {

	var Profile = {
		init: function ( options, elem ) {
			var self = this;
			self.elem = elem;

			self.setElem();

			self.resize();
			$( window ).resize(function () {
				self.resize();
			});

			/*self.ids = [];
			self.Events();*/
		},
		setElem: function () {
			var self = this;
			self.$elem = $(self.elem);
			self.$elem.find('[role]').each(function () {
				if( $(this).attr('role') ){
					var role = "$" + $(this).attr('role');
					self[role] = $(this);
				}
				
			});
		},
		resize: function () {
			var self = this;

			var outer = $( window );
			var offset = self.$elem.offset();
			var right = 0;
			var fullw = outer.width() - (offset.left+right);
			var fullh = (outer.height() + outer.scrollTop()) - $('#tobar').height();

			var leftw = (fullw*25) / 100;
			console.log( leftw );

			self.$left.css({
				width: leftw,
				height: fullh
			});

			self.$leftContent.css({
				height: fullh-self.$leftHeader.outerHeight()
			});

			self.$content.css({
				marginLeft: leftw,
			});



		},

		Events: function () {
			var self = this;

			

		},

	};

	$.fn.profile = function( options ) {
		return this.each(function() {
			var $this = Object.create( Profile );
			$this.init( options, this );
			$.data( this, 'profile', $this );
		});
	};

	$.fn.profile.options = {
		onOpen: function() {},
		onClose: function() {}
	};
	
})( jQuery, window, document );