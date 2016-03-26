// Utility
if ( typeof Object.create !== 'function' ) {
	Object.create = function( obj ) {
		function F() {};
		F.prototype = obj;
		return new F();
	};
}

(function( $, window, document, undefined ) {
	var Facebook = {
		init: function(options, elem){
			var self = this;

			self.elem = elem;
			self.$elem = $(elem);
			self.options = options;

			self.$elem.click(function  (e) {
				e.preventDefault();
				self.wait();

				if( self.options.active=='login' ){
					FB.login(function(response) {
				    	self.statusChangeCallback(response);
				    }, {scope: 'public_profile,email'});
				}
				else if(self.options.active=='share'){

					FB.ui({
				    	method: 'share',
				    	href: self.options.url,
				    },
				    // callback
				    function(response) {
				        if (response && !response.error_code) {
				        	Event.showMsg({text: 'Posting completed.' , auto: true});
				    	} else {
				    		Event.showMsg({text: 'Error while posting.' , auto: true});
				      	}
				    });
				}
			});

			window.fbAsyncInit = function() {
			    FB.init({
			      appId      : self.options.app_id,
			      cookie     : true,  // enable cookies to allow the server to access 
			                          // the session
			      xfbml      : true,  // parse social plugins on this page
			      version    : 'v2.5' // use version 2.5
			    });

			    // FB.getLoginStatus(function(response) {
			    	//console.log( response );
			      // statusChangeCallback(response);
			    //});
			};

			// Load the SDK asynchronously
			(function(d, s, id) {
			    var js, fjs = d.getElementsByTagName(s)[0];
			    if (d.getElementById(id)) return;
			    js = d.createElement(s); js.id = id;
			    js.src = "//connect.facebook.net/en_US/sdk.js";
			    fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));

		},

		statusChangeCallback: function (response) {
			var self = this;

			Event.hideMsg();
			self.$elem.removeClass('disabled');
			if (response.status === 'connected') {
		      // Logged into your app and Facebook.

		      self.getFBData();
		    } else if (response.status === 'not_authorized') {
		      // The person is logged into Facebook, but not your app.
		    } else {
		      // The person is not logged into Facebook, so we're not sure if
		      // they are logged into this app or not.
		    }
		},

		getFBData: function () {
			var self = this;

			self.dataPost = {};
			self.wait();

			FB.api('/me?fields=name,email', function(response) {

				self.dataPost = response;

				if( self.options.active=='login'  ){
					FB.api('/me/picture?type=normal', function (response) {
				    	self.dataPost.image_url = response.data.url;

				    	self.Login();
				    });
				}
		    });

		},

		Login: function () {
			var self = this;

			self.fetch().done(function( results ) {
				Event.hideMsg();

				self.$elem.removeClass('disabled');

				if( results.error ){

					$('#signup_fblogin').find('.notification').text( results.error );
					
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

		fetch: function(){
			return $.ajax({
				type: "POST",
				url: this.options.url,
				data: this.dataPost,
				dataType: 'json'
			});
		},

		wait: function () {
			var self = this;

			self.$elem.addClass('disabled');
			Event.showMsg({text: 'กำลังเชื่อต่อกับ facebook' });
		}
	};

	$.fn.facebook = function( options ) {
		return this.each(function() {
			var fb = Object.create( Facebook );
			fb.init( options, this );
			$.data( this, 'facebook', fb );
		});
	};

	$.fn.facebook.options = { };
	
})( jQuery, window, document );