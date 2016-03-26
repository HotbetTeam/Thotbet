// Utility
if ( typeof Object.create !== 'function' ) {
	Object.create = function( obj ) {
		function F() {};
		F.prototype = obj;
		return new F();
	};
}

(function( $, window, document, undefined ) {

	var $window			= $( window ),
		$body			= $( 'body' ),
		windowHeight	= $window.height(),
		windowScrollTop	= 0,

		settings = {};

	var Inbox = {
		init: function ( options, elem ) {
			var self = this;
			self.elem = elem;
			self.$elem = $(elem);


			settings = $.extend( {}, $.fn.Inbox.options, options );
			// set var 
			self.config();
			
			self.Session( 1 );
		},

		config: function () {
			var self = this;

			self.runSession = 1;
			
		}

		/**/
		/* Server */
		/**/
		connect: function ( user ) {
			var self = this;

			self.rootRef = new Firebase( settings.ROOT );
			self.usersRef = self.rootRef.child('users');
			self.currentUserRef = self.usersRef.child( user || settings.username );
		},

			/*usersRef: function () {
				var self = this;

				self.Server();
				return self.usersRef;
			},
			user: function ( user ) {
				var self = this;

				self.Server();
				return self.usersRef.child( user || settings.username );
			}*/

		/*function  () {
			var self = this;

			// set server
			self.rootRef = new Firebase( self.options.ROOT );
			self.usersRef = self.rootRef.child('users');
			self.currentUserRef = self.usersRef.child(self.currentUser);
		},*/

		Session: function  ( length ) {
			var self = this;

			clearTimeout( self.runSession );
			self.runSession = setTimeout(function() {

				console.log( self.connect.currentUserRef() );

				// set Session
				/*self.connect.user().update({
					timeout: new Date().getTime() + self.options.refresh
				});*/

				// clear Session
				/*self.connect.usersRef.once("value", function(snap) {
					self.userOnline( snap.val() );
				});*/

				if ( self.options.refresh ) {
					self.Session();
				}
			}, length || self.options.refresh );
		},


	};

	$.fn.inbox = function( options ) {
		return this.each(function() {
			var $this = Object.create( Inbox );
			$this.init( options, this );
			$.data( this, 'Inbox', $this );
		});
	};

	$.fn.inbox.options = {
		refresh: 60000, // 60 วิ
	};
	
})( jQuery, window, document );