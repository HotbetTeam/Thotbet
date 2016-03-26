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
		windowScrollTop	= 0;

	var Chat = {
		init: function ( options, elem ) {
			var self = this;
			self.elem = elem;
			self.$elem = $(elem);

			self.options = $.extend( {}, $.fn.chat.options, options );

			self.$elem.addClass('has-loading');

			self.config();
			self.Events();

			if( self.currentUser ){
				self.Server();
			}
		},

		config: function () {
			var self = this;

			self.currentUser = self.options.username;
			self.$elem.find('.form-chat').width( self.$elem.find('.form-chat>li').length*300 );
		},

		Events: function () {
			var self = this;

			self.$elem.find('.js-close').click(function (e) {
				self.$elem.toggleClass('on', !self.$elem.hasClass('on') );
				e.preventDefault();
			});

			$('.js-live-chat').click(function (e) {

				e.preventDefault();
				self.$elem.toggleClass('on', !self.$elem.hasClass('on') );

				if( self.$elem.hasClass('on') && self.currentUser ){

					/*if( !self.rootRef ){
						self.Server();
					}*/
					self.resizePopup();
					self.$elem.find('.messageInput').focus();
				}
			});

			self.$elem.find('.js-register-switch').click(function (e) {

				var c = self.$elem.find('.login-register-container');
				if( c.hasClass('login') ){
					c.removeClass('login').addClass('register');

					$(this).text( 'ลงชื่อเข้าใช้งาน' );
				}
				else{
					c.removeClass('register').addClass('login');

					$(this).text( 'สมัครสมาชิก' );
					// self.$elem.find('.login-register-title').text( 'ลงชื่อเข้าใช้งาน' );
				}

				// $(this).toggleClass('login', !$(this).hasClass('login') );
				e.preventDefault();
			});

			self.focus = false;
			self.$elem.mouseenter(function() {
				self.scrollTop = $( window ).scrollTop();
				self.focus = true;
			})
			.mouseleave(function() {
				self.focus = false;
			});

			$( window ).scroll(function() {
				if( self.focus ){
					$(this).scrollTop( self.scrollTop );
				}
			});
		},

		/**/
		/* Server */
		/**/
		Server: function () {
			var self = this;

			// set server
			self.rootRef = new Firebase( self.options.ROOT );
			self.usersRef = self.rootRef.child('users');
			self.currentUserRef = self.usersRef.child(self.currentUser);

			self.Session( 1 );
			self.Messenger();
			self.getNewMessage();
		},
		Session: function  ( length ) {
			var self = this;

			setTimeout(function() {

				// add user Online
				self.currentUserRef.update({
					timeout: new Date().getTime() + self.options.refresh
				});

				// remove user Online
				self.usersRef.once("value", function(snap) {
					self.removeUserOnline( snap.val() );
				});

				if ( self.options.refresh ) {
					self.Session();
				}

			}, length || self.options.refresh );
		},
		removeUserOnline: function ( data ) {
			var self = this;
			
			$.each(data, function(user, val){

				/*if( new Date().getTime() > val.timeout && user!=self.currentUser){
					self.usersRef.child( user ).remove();
				}*/

			});
		},

		/**/
		/* Messenger */
		/**/
		Messenger: function () {
			var self = this;

			// self.notify: [];
			self.has_scroll = false;

			self.loadMsg();
			self.newMessage();
		},

		loadMsg: function () {
			var self = this;

			self.is_loading = true;
			self.$elem.find('.chat-content').addClass('has-loading');
			if( !self.data ){ self.data = {}; }

			setTimeout(function () {

				$.ajax({
					url: self.options.URL,
					data: self.data,
					dataType: 'json'
				}).always(function () {

					if( self.$elem.hasClass('has-loading') ){
						self.$elem.removeClass('has-loading');
					}

					self.$elem.find('.chat-content').removeClass('has-loading');

				}).fail(function() { 

				}).done(function ( data ) {
					
					self.data = $.extend( {}, self.data, data.options );
					 
					// c_id: 0
					self.buildFrag( data.lists );
					
				});
			}, 1);
		},

		buildFrag: function ( results ) {
			var self = this;

			if( self.has_scroll ){
				for (var i = 0; i < results.length; i++) {
					var obj = results[i];
					// self.setTheDate(obj.time);
					self.display( self.setItem( obj ), obj );
				};
			}
			else{

				for (var i = results.length - 1; i >= 0; i--) {
					var obj = results[i];
					// self.setTheDate(obj.time);
					self.display( self.setItem( obj ), obj );
				};
			}


			var d = {};
			self.$elem.find('.list-message>li.newday').remove();
			$.each( self.$elem.find('.list-message>li' ).not('.list-message-loading'), function () {
				var item = $(this);

				d[item.attr('data-date')] = item.data('time');				
			});

			$.each(d, function (id, date) {
				
				self.$elem.find('.list-message>li[data-date='+id+']').first().before( 
					$('<li>', {class:'clearfix newday', 'data-date': id }).html(
						$('<span>', {text: self.fullDate( date ) })
					) 
				);

			});
			
			self.is_loading = false;
			self.scrollMsg();

			if( self.has_scroll ){
				self.has_scroll = false;
			}
		},
		setTheDate: function ( date ) {
			var self = this, date = new Date( date );

			date.setHours(0);
			date.setMinutes(0);
			date.setSeconds(0);

			var is = false;

			if( !self.theDate ){
				is = true;
			}else if( date.getTime()!=self.theDate.getTime() ){
				is = true;
			}

			if( is ){
				self.theDate = date;
				self.display( $('<li>', {class:'clearfix newday', id: date.getFullYear() +'-'+ date.getMonth() +'-' + date.getDate() }).html(
					$('<span>', {text: self.fullDate( date ) })
				), 1 );
			}
		},
		fullDate: function (date){

			var month = ["มกราคม", "กุมภาาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฏาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"];
			return date.getDate() + " " + month[date.getMonth()] + " " + (date.getFullYear()+543);
		},
		setItem: function (data) {
			var self = this;

			var date = new Date( data.time );
			var hour =  date.getHours();
			hour = hour < 10 ? "0"+hour:hour;

			var m =  date.getMinutes();
			m = m < 10 ? "0"+m:m;

			var dateText = hour + ":" + m;

			var textDate = date.getFullYear() +'-'+ date.getMonth() +'-' + date.getDate();

			var li = $('<li>', {class: 'clearfix', id: 'msg-'+data.cr_id, 'data-date':textDate}) // , 
				.addClass( data.user_id==self.currentUser ? 'send' : 'received' )
				.html(
					$('<div>', {class: 'box'}).append(
						  $('<div>', {class: 'text'}).html( data.text )
						, $('<div>', {class: 'time', text: dateText})
					)
				);

			li.data('time', date);

			return li;
		},
		display: function  ( item ) {
			var self = this;

			var date = new Date( item.data('time') );	

			if( self.has_scroll ){

				var li = self.$elem.find('.list-message>li').not('.newday').first();				
				li.before( item );
			}
			else{
				self.$elem.find('.list-message').not('.newday').append( item );
			}
		},

		timeDay: function (date) {
			
			date.setHours(0);
			date.setMinutes(0);
			date.setSeconds(0);

			return date.getTime();
		},

		resizeMsg: function () {
			var self = this;

			self.$elem.find('.chat-content').height( self.$elem.height() - 
				( self.$elem.find('.chat-header').height()+ self.$elem.find('.chat-write').height() ) 
			);
		},
		scrollMsg: function () {
			var self = this;

			/*var box = self.$elem.find('.chat-content');
			var outer = box.height();
			var height = box.find('.list-message').outerHeight();

			height += 30;

			if( outer<height && !self.has_scroll ){
				box.scrollTop( height-outer );
			}
			else if( !self.hover_scroll ){
				box.scrollTop( (height-outer)-(self.scroll_height+outer) );
			}

			box.scroll(function (e) {

				self.hover_scroll = true;
				if( $(this).scrollTop() == 0 && self.data.more && !self.is_loading ){
					self.data.pager++;
					self.has_scroll = true;
					self.hover_scroll = false;
					self.scroll_height = height-outer;

					self.scroll_height -= 100;
					// console.log( self.data );
					self.loadMsg();
				}

				e.preventDefault();
			});*/
		},

		// 
		newMessage: function () {
			var self = this;

			self.resizeMsg();

			self.$elem.find('.messageInput').autosize({
				callback: function  ( e ) {

					self.resizeMsg();
					return false;
				}
			});
			// Enter Send
			self.$elem.find('.messageInput').keydown(function(e){
				var text = $.trim( $(this).val() );
				if (!e.ctrlKey && e.keyCode == 13 && text != ''){

					self.sendMessage( text );
					e.preventDefault();
				} else if(e.keyCode == 13) {

					$(this).height( $(this).height() + 18 ); 
					$(this).val(text + "\n");
				}
				
			}).keyup(function () {
				self.$elem.find('.btn-send').toggleClass('disabled',  $.trim( $(this).val() )==''? true: false);
			});

			self.$elem.find('.btn-send').click(function (e) {
				e.preventDefault();
				var text = $.trim( self.$elem.find('.messageInput').val() );

				if( text == '' ) return false;
				self.sendMessage( text );
			});	
		},

		sendMessage: function ( text ) {
			var self = this;

			self.$elem.find('.messageInput').attr('disabled', true);
			self.$elem.find('.btn-send').attr('disabled', true);

			self.msg = {
				id: self.data.id,
				text: text
			}
			
			$.ajax({
				type: 'post',
				url: self.options.URL+'send',
				data: self.msg,
				dataType: 'json'
			}).always(function () {
				self.$elem.find('.messageInput').removeAttr('disabled');
				self.$elem.find('.btn-send').removeAttr('disabled');
			}).fail(function() { 

			}).done(function ( results ) {

				self.data.id = results.id;
				self.$elem.find('.messageInput').val("").height( 18 ).focus();
				self.$elem.find('.btn-send').addClass('disabled');

				self.updateMsg( results );				
			});
		},
		updateMsg: function ( msg ) {
			var self = this;

			console.log( msg );

			self.usersRef.once("value", function(snap) {
				// get users online
				var usersAreOnline = [];
				$.each(snap.val(), function(username, val){
					if( val ){
						if( new Date().getTime() < val.timeout ){
							usersAreOnline.push( parseInt(username) );
						}
					}
					
				});

				var ids = [];
				$.each(msg.users, function(i, uesr){
					ids.push( parseInt(uesr.user_id) );
				});

				$.each(self.arrayUnique(ids), function(i, id){

					if( usersAreOnline.indexOf( id )>=0 ){
						// Update new message to user
						var usersMessagesRef = self.usersRef.child(id+'/messages');
						usersMessagesRef.push(msg);
					}
				});
			});
		},
		arrayUnique: function(a) {
		    return a.reduce(function(p, c) {
		        if (p.indexOf(c) < 0) p.push(c);
		        return p;
		    }, []);
		},


		getNewMessage: function () {
			var self = this;

			var userMessagesRef = self.currentUserRef.child('messages');

			// is new message to me 
			userMessagesRef.on('child_added', function(snapshot){
				var msg = snapshot.val();

				userMessagesRef.child( snapshot.name() ).remove();

				// update Message
				self.showMessage( msg );
			});
		},

		showMessage: function ( msg ) {
			var self = this;

			if( msg.evt=='update' ){
				self.$elem.find('.list-message #msg-'+msg.cr_id+' .text').html(msg.text);
			}
			else{
				self.display( self.setItem( msg ), msg, 1 );
			}

			self.scrollMsg();
		}

	};

	$.fn.chat = function( options ) {
		return this.each(function() {
			var $this = Object.create( Chat );
			$this.init( options, this );
			$.data( this, 'chat', $this );
		});
	};

	$.fn.chat.options = {
		refresh: 60000,
		onOpen: function() {},
		onClose: function() {}
	};
	
})( jQuery, window, document );