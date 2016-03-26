// Utility
if ( typeof Object.create !== 'function' ) {
	Object.create = function( obj ) {
		function F() {};
		F.prototype = obj;
		return new F();
	};
}

window.handleClientLoad = function() {
	$( window ).trigger( 'handleClientLoad' );
};

(function( $, window, document, undefined ) {
	var $window			= $( window ),
		$body			= $( 'body' ),
		setting 		= {};

	$window.on('handleClientLoad', function(){
		// Reference the API key
        gapi.client.setApiKey(setting.client_secret);
        window.setTimeout(checkAuth,1);
	});

	function checkAuth() {

		gapi.auth.authorize({client_id: setting.client_id, scope: setting.scopes, immediate: true}, handleAuthResult);
	}

	function handleAuthResult (authResult) {
		
		if (authResult && !authResult.error) {
			makeApiCall();
        } else {
        	console.log("Error: makeApiCall");
        	// error 
        }
	}

	// Load the API and make an API call.  Display the results on the screen.
	function makeApiCall () {

		// Load the Google+ API
		console.log( gapi.client );

		gapi.client.load('plus','v1', function(){
			
		});

		// gapi.client.load('plus', 'v1').then(function() {

			// Assemble the API request
			/*var request = gapi.client.plus.people.get({
	        	'userId': 'me'
	        });*/

	        // Execute the API request
	        /*request.then(function(resp) {
	        	console.log( resp );
	       	}, function(reason) {
           		console.log('Error: ' + reason.result.error.message);
          	});*/
		// });
	}

	var googleAuth = {
		init: function(options, elem){
			var self = this;

			self.elem = elem;
			self.$elem = $(elem);

			setting = options;

			/*clientId = options.client_id;
			apiKey = options.client_id;
			scopes = options.scopes;*/

			// Event
			self.$elem.click(function  (e) {
				self.wait();
				self.connect();

				e.preventDefault();
			});
		},

		connect: function () {
			var self = this;


			/*(function() {
			    var po = document.createElement('script');
			    po.type = 'text/javascript'; po.async = true;
			    po.src = 'https://plus.google.com/js/client:plusone.js';
			    var s = document.getElementsByTagName('script')[0];
			    s.parentNode.insertBefore(po, s);
			 })();*/

			// 
			$.getScript( "https://apis.google.com/js/client.js?onload=handleClientLoad" ).fail(function () {
				console.log( 'Is not connect Google API' );
			}).done(function () {


				// var request = gapi.client.plus.people.get( {'userId' : 'me'} );

				// console.log( gapi.client );
			});
		},

		handleClientLoad: function () {
			var self = this;

			// ReferenceThe API key
			
			gapi.client.setApiKey(self.options.app_id);
        	// setTimeout(self.checkAuth,1);
			
		},
		checkAuth: function () {
			var self = this;

			console.log( gapi );
			// gapi.auth.authorize({client_id: self.options.app_id, scope: self.options.app_scopes, immediate: true}, self.handleAuthResult);
		},



		statusChangeCallback: function (response) {
			/*var self = this;

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
		    }*/
		},

		getFBData: function () {
			/*var self = this;

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
		    });*/

		},

		Login: function () {
			/*var self = this;

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
			});*/
		},

		fetch: function(){
			/*return $.ajax({
				type: "POST",
				url: this.options.url,
				data: this.dataPost,
				dataType: 'json'
			});*/
		},

		wait: function () {
			var self = this;

			self.$elem.addClass('disabled');
			Event.showMsg({text: 'กำลังเชื่อต่อกับ Google' });
		}
	};

	$.fn.googleauth = function( options ) {
		return this.each(function() {
			var $this = Object.create( googleAuth );
			$this.init( options, this );
			$.data( this, 'googleauth', $this );
		});
	};
	
})( jQuery, window, document );