// Utility
if ( typeof Object.create !== 'function' ) {
	Object.create = function( obj ) {
		function F() {};
		F.prototype = obj;
		return new F();
	};
}

(function( $, window, document, undefined ) {

	var Col = {
		init: function ( options, elem ) {
			var self = this;
			self.elem = elem;
			self.$elem = $(self.elem);
			self.setElem();

			setTimeout(function () {
				
				self.resize();
				$( window ).resize(function () {
					self.resize();
				});

				self.$elem.addClass('on');
			}, 100);
			

			$('.navigation-trigger').click(function () {
				self.resize();
			});
		},
		setElem: function () {
			var self = this;
			
			self.$elem.attr('id', 'SettingColContainer')
			self.$elem.find('[data-elem]').each(function () {
				if( $(this).attr('data-elem') ){
					var ref = "$" + $(this).attr('data-elem');
					$(this).removeAttr('data-elem');
					self[ref] = $(this);
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

			self.$header.css({
				position: 'fixed',
				top: 0,
				left: offset.left,
				// right: right,
				width: self.$elem.parent().width(),

			});

			paddingTop = self.$header.outerHeight();
			
			paddingTop -= parseInt( self.$elem.parent().css('padding-top') )

			self.$content.css({
				paddingTop: paddingTop
			});
		},

		Events: function () {
			var self = this;
		},

	};

	$.fn.SettingCol = function( options ) {
		return this.each(function() {
			var $this = Object.create( Col );
			$this.init( options, this );
			$.data( this, 'SettingCol', $this );
		});
	};

	$.fn.SettingCol.options = {	};
	
})( jQuery, window, document );