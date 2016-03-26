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
		$body			= $( 'body' );

	var Social = {
		init: function(options, elem){
			var self = this;

			self.elem = elem;
			self.$elem = $(elem);
			self.options = options;

			// console.log( self.options );

			self.$elem.click(function  (e) {
				self.wait();

				self[ self.options.network ][ self.options.active ]( self.options, function ( response ) {
					if( response.closed ){ 
						self.closed();
						return false; 
					}
					self[ self.options.active ]( response );
				} );
				e.preventDefault();
			});
			
		},

		twitter: {
			login: function ( options, callback ) {

				var self = this;
				self.callback = callback;
				url = options.redirect+"?login";
				w = 840;
				h = 800;

				// PopupCenter
				// Fixes dual-screen position                         Most browsers      Firefox
   				var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
    			var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

    			var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
    			var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

    			var left = ((width / 2) - (w / 2)) + dualScreenLeft;
    			var top = ((height / 2) - (h / 2)) + dualScreenTop;

    			var win = window.open( url, 'tw_login', ' width=' + w + ', height=' + h + ', top=' + top + ', left=' + left, false );

    			
    			window.myApp = {
    				syncCall: function() {
						return "Sync";
					},
    				asyncCall: function( response ) {

    					if( typeof self.callback === 'function' ){
    						self.callback( response );
    					}
    				}
    			}

				// var RunCallbackFunction = function() { };
                var timer = setInterval(function() {  
				    if( win.closed ) {  
				        clearInterval(timer);

				        if( typeof self.callback === 'function' ){
    						self.callback( { 'closed': win.closed } );
    					}
				    }  
				}, 1000);

				// redirect
				// console.log( options );
			}
		},

		google: {
			login: function (options, call) {
				var self = this;

				self.callback = call;
				url = options.redirect+"?login";
				w = 450;
				h = 400;

				// PopupCenter
				// Fixes dual-screen position                         Most browsers      Firefox
   				var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
    			var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

    			var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
    			var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

    			var left = ((width / 2) - (w / 2)) + dualScreenLeft;
    			var top = ((height / 2) - (h / 2)) + dualScreenTop;

    			var win = window.open( url, 'g_login', ' width=' + w + ', height=' + h + ', top=' + top + ', left=' + left, false );

    			window.myApp = {
    				asyncCall: function(response) {
    					if( typeof self.callback === 'function' ){
    						self.callback( response );
    					}
    				}
    			}

                var timer = setInterval(function() {   
				    if(win.closed) {  
				        clearInterval(timer);  
				        
				        if( typeof self.callback === 'function' ){
    						self.callback( {closed: true} );
    					}
				    }  
				}, 1000);
			}
		},

		facebook: {
			init: function ( options ) {
				var self = this;
				self.options = options;
				// Load the SDK asynchronously
				if (typeof FB === 'undefined') {

					(function(d, s, id) {
					    var js, fjs = d.getElementsByTagName(s)[0];
					    if (d.getElementById(id)) return;
					    js = d.createElement(s); js.id = id;
					    js.src = "//connect.facebook.net/en_US/sdk.js";
					    fjs.parentNode.insertBefore(js, fjs);
					}(document, 'script', 'facebook-jssdk'));

					window.fbAsyncInit = function() {
					    FB.init({
					      appId      : self.options.client_id,
					      cookie     : true,  // enable cookies to allow the server to access 
					                          // the session
					      xfbml      : true,  // parse social plugins on this page
					      version    : 'v2.5' // use version 2.5
					    });

					    /*FB.getLoginStatus(function(response) {
					    	console.log( response );
					      // statusChangeCallback(response);
					    });*/

					    self.active();
					};
				}
				else{
					self.active();
				}
			},

			active: function () {
				var self = this;

				if( self.options.active=='login' ){
					self._login();
				}
			},

			login: function ( options, call ) {
				var self = this;

				self.callback = call;
				self.init( options );
			},
			_login:function () {
				var self = this;

				FB.login(function(response) {
			    	self.statusChangeCallback(response);
			    }, {scope: 'public_profile, email'});
			},

			statusChangeCallback: function (response) {
				var self = this;

				// Event.hideMsg();
				// self.$elem.removeClass('disabled');

				if (response.status === 'connected') {
			      // Logged into your app and Facebook.

			      	self.getFBData();
			    } else if (response.status === 'not_authorized') {

			      // The person is logged into Facebook, but not your app.
			    } else {
			    	
			    	if( typeof self.callback === 'function' ){
						self.callback( {closed: true} );
					}
			      // The person is not logged into Facebook, so we're not sure if
			      // they are logged into this app or not.
			    }
			},
			getFBData: function () {
				var self = this;

				self.dataPost = {};
				// self.wait();

				FB.api('/me?fields=name,email', function(response) {

					self.dataPost = response;
					self.dataPost.type = 'facebook';

					if( self.options.active=='login'  ){
						FB.api('/me/picture?type=normal', function (response) {
					    	self.dataPost.image_url = response.data.url;

					    	if( typeof self.callback === 'function' ){
	    						self.callback( self.dataPost );
	    					}
					    	// self.redirect();
					    });
					}
			    });
			},
		},
		
		login: function ( response ) {
			var self = this;

			// console.log( response ); 
			$.ajax({
				type: "POST",
				url: this.options.url,
				data: response,
				dataType: 'json'
			})
			.always(function() { 
				self.closed();
			})
			.done(function( results ) {

				if( results.error ){

					var networkName = self.options.network;
					Dialog.open({
						title: 'เชื่อมต่อกับ ' + networkName.charAt(0).toUpperCase() + networkName.substring(1),
						body: results.error,
						button: '<a href="#" class="btn btn-cancel" role="dialog-close"><span class="btn-text">ปิด</span></a>'
					});
					// $('#signup_fblogin').find('.notification').text( results.error );
					return false;
				}

				if( results.url ){
					window.location = results.url;
				}
				else{
					window.location.reload();
				}
			});
		},

		wait: function () {
			var self = this;

			var networkName = self.options.network;
			self.$elem.addClass('disabled');
			Event.showMsg({text: 'กำลังเชื่อต่อกับ ' + networkName.charAt(0).toUpperCase() + networkName.substring(1)  });
		},

		closed: function(){
			var self = this;

			Event.hideMsg();
			self.$elem.removeClass('disabled');
		}
	};

	$.fn.social = function( options ) {
		return this.each(function() {
			var $this = Object.create( Social );
			$this.init( options, this );
			$.data( this, 'social', $this );
		});
	};
	
})( jQuery, window, document );