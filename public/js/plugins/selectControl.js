// Utility
if ( typeof Object.create !== 'function' ) {
	Object.create = function( obj ) {
		function F() {};
		F.prototype = obj;
		return new F();
	};
}

(function( $, window, document, undefined ) {

	var selectControl = {
		init: function ( options, elem ) {
			var self = this;
			self.elem = elem;

			
		}

	};

	$.fn.selectControl = function( options ) {
		return this.each(function() {
			var $this = Object.create( selectControl );
			$this.init( options, this );
			$.data( this, 'selectControl', $this );
		});
	};

	$.fn.selectControl.options = {};
	
})( jQuery, window, document );