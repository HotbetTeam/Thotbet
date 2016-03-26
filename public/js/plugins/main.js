// Utility
if ( typeof Object.create !== 'function' ) {
	Object.create = function( obj ) {
		function F() {};
		F.prototype = obj;
		return new F();
	};
}

(function( $, window, document, undefined ) {

	var main = {
		init: function ( options, elem ) {
			var self = this;
			self.elem = elem;

			self.setElem();

			self.resize();
			$( window ).resize(function () {
				self.resize();
			});

			self.$elem.addClass('on');
			/*self.ids = [];
			self.Events();*/
		},
		setElem: function () {
			var self = this;
			self.$elem = $(self.elem);
			self.$elem.attr('id', 'mainContainer')
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

			if( self.$topbar ){
				self.$topbar.css({
					height: self.$topbar.outerHeight(),
					position: 'fixed',
					top: offset.top,
					left: offset.left,
					right: right
				});
			}

			if( self.$right ){
				var rightWPercent = self.$right.attr('data-w-percent') || 30;
				var rightw = (fullw*rightWPercent) / 100;
				self.$right.css({
					width: rightw,
					height: fullh,
					position: 'absolute',
					top: 0,
					right: 0
				});

				self.$content.css({
					marginRight: rightw
				});

			}

			var leftw = (fullw*25) / 100;
			if( self.$left ){


				if( self.$left.attr('data-width') ){
					leftw = parseInt( self.$left.attr('data-width') );
					self.$left.removeAttr('data-width');
				}

				self.$left.css({
					width: leftw,
					height: fullh,
					position: 'absolute',
					top: 0,
					left: 0
				});

				if( self.$leftContent ){

					self.$leftContent.css({
						height: fullh-self.$leftHeader.outerHeight(),
						overflowY: 'auto'
					});
				}
				

				self.$content.css({
					marginLeft: leftw,
				});

			}

			if( self.$topbar ){
				fullh -= self.$topbar.outerHeight();
				self.$elem.css('padding-top', self.$topbar.outerHeight());

				if( self.$left ){
					self.$left.css('top', self.$topbar.outerHeight());
				}

				if( self.$right ){
					self.$right.css('top', self.$topbar.outerHeight());
				}
			}

			if( self.$toolbar ){
				fullh -= self.$toolbar.outerHeight();
			}

			self.$main.css({
				height: fullh,
				overflowY: 'auto'
			});

			if( self.$toolbar && self.$toolbarControls  ){

				self.$toolbarControls.css({
					height: self.$toolbar.outerHeight(),
					position: 'fixed',
					left: offset.left+leftw,
					right: right,
				});
				
			}
			
		},

		Events: function () {
			var self = this;
		},

	};

	$.fn.main = function( options ) {
		return this.each(function() {
			var $this = Object.create( main );
			$this.init( options, this );
			$.data( this, 'main', $this );
		});
	};

	$.fn.main.options = {
		widthLeft: 25,
		widthRight: 30
	};
	
})( jQuery, window, document );