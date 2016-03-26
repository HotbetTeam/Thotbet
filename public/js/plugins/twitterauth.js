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
		setting 		= {};

	var twitterAuth = {
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
				// self.connect();

				var oauth_token = "_sA62AAAAAAAkKy-AAABUtBAvJE";

				url = "https://api.twitter.com/oauth/authenticate?oauth_token="+oauth_token;
				title = "";
				w = 740;
				h = 600;

				// PopupCenter
				// Fixes dual-screen position                         Most browsers      Firefox
   				var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
    			var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

    			var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
    			var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

    			var left = ((width / 2) - (w / 2)) + dualScreenLeft;
    			var top = ((height / 2) - (h / 2)) + dualScreenTop;

    			var win = window.open( url, 'tw_login', ' width=' + w + ', height=' + h + ', top=' + top + ', left=' + left, false );

    			/*myPopup.win =  function () {
                    window.alert('hola!');
                };*/
                var timer = setInterval(function() {   
				    if(win.closed) {  
				        clearInterval(timer);  
				        alert('closed');
				    }  
				}, 1000); 

                /*myPopup.onclose = function () {
                	window.alert('close!');
                };*/

    			// myPopup.addEventListener('load', self.statusChangeCallback, true);

    			// console.log( myPopup.addEventListener );

    			/*myPopup[myPopup.addEventListener ? 'addEventListener' : 'attachEvent'](
				  (myPopup.attachEvent ? 'on' : '') + 'load', self.statusChangeCallback, false
				);*/

    			// toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no,

				// Puts focus on the newWindow
			    /*if (window.focus) {
			        twWindow.focus();
			    }*/

			    /*$(window.popup).onload = function()
        		{
                	
        		};*/

			    /*newWindow.onbeforeunload = function (event) {
			    	var message = " 1 ";

			    	self.statusChangeCallback( );

			    	if (typeof event == 'undefined') {
				        event = newWindow.event;
				    }
				    if (event) {
				        event.returnValue = message;
				    }
				    return message;
			    }*/

			    /*$(document).keydown(function (e) {    
			    	if (e.key=="F5") {

			    	}
			    });*/
			    
				e.preventDefault();
			});
		},

		load: function () {
						
		},

		statusChangeCallback: function () {
			
			alert( 1 );	
		},
		
		wait: function () {
			var self = this;

			// self.$elem.addClass('disabled');
			Event.showMsg({text: 'กำลังเชื่อต่อกับ Twitter' });
		}
	};

	$.fn.twitterauth = function( options ) {
		return this.each(function() {
			var $this = Object.create( twitterAuth );
			$this.init( options, this );
			$.data( this, 'twitterauth', $this );
		});
	};
	
})( jQuery, window, document );