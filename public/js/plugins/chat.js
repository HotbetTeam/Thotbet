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

			self.config();
			self.events();

			if( self.currentUser && self.online ){
				self.Server();
			}

		},

		config: function () {
			var self = this;

			self.data = [];
			self.currentUser = self.options.username;
			self.key_id = self.options.key;
			self.online = navigator.onLine;
		},

		events: function () {
			var self = this;
			
			self.$elem.delegate('.link-close', 'click', function (e) {
				self.$elem.toggleClass('on', !self.$elem.hasClass('on') );
				e.preventDefault();
			});

			$('.js-live-chat').click(function (e) {

				e.preventDefault();
				self.$elem.toggleClass('on', !self.$elem.hasClass('on') );

				if( self.$elem.hasClass('on') && self.currentUser ){

					// 
					if( self.currentPopupId ){

						var data = self.data[ self.currentPopupId ];
						if( data ){
							self.resizePopup( data );
							self.scrollMessage( data );
						}

						// self.$elem.find('.messageInput').focus();
					}
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
				}
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

			var v;
			self.pageon = true;
			$( window ).mousemove(function () {
				if( !self.pageon ){
					self.pageon = true;
					clearTimeout( v );
				}
				
				
			}).mouseout(function () {
				v = setTimeout(function() {
					self.pageon = false;
				}, 30000 );
			});
		},

		/**/
		/* Server */
		/**/
		Server: function () {
			var self = this;

			// set server
			self.rootRef = new Firebase( self.options.ROOT );
			self.usersRef = self.rootRef.child('conversations');
			// self.currentUserRef = self.usersRef.child(self.currentUser);

			self.Session( 1 );

			self.Messenger();

			self.getNewMessage();
		},
		Session: function  ( length, data, results ) {
			var self = this;

			if( self.intime ) clearTimeout( self.intime );
			self.intime = setTimeout(function() {

				// add user Online
				if( self.key_id ){
					self.usersRef.child( self.key_id ).update({
						timeout: new Date().getTime() + self.options.refresh + 1000
					});

					if( !self.is_online ){
						self.getNewMessage();
					}
				}

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


			if( !data || !self.key_id) return false;
			
			$.each(data, function(user, val){

				if( val ){
					if( new Date().getTime() > val.timeout && user!=self.key_id){
						self.usersRef.child( user ).remove();
					}
				}
			});
		},

		/**/
		/* popup */
		setPopup: function ( data ) {
			var self = this;

			var header = $('<header>', {id:'header', class: 'header clearfix'}).append(
					$('<div>', {class: 'lfloat'}).append(
						$('<div>', {class: 'title'}).append(
							$('<strong>', {text: data.name})
							, data.phone_number ? $('<span>', {text: data.phone_number}) : ''
						)
						/*, $('<div>', {class: 'status-online js-user-online', 'data-user-id': data.user_id}).append(
								'<i></i>', 
								'<span class="offline">ออฟไลน์</span>', 
								'<span class="online">ออนไลน์</span>'
						)*/
					)
					
					, $('<div>', {class: 'actions rfloat'}).append(
						$('<a>', {class: 'link-close'}).append(
							$('<i>', {class: 'icon-remove'})
							, $('<span>', {text: 'ปิด'})
						)
					)
				),

				content = $('<div>', {id:'content', class: 'content clearfix'}).append(
			  		$('<div>', {class: 'more'}).append( 
						$('<div>', {class: 'loading loader-spin-wrap'}).html(
							$('<div>', {class: 'loader-spin'})
						),
						$('<a>', {class: 'more-link', text: 'โหลดข้อความเพิ่มเติม'})
					)					  
					, $('<ul>', {id: 'listbox', class: 'list-message clearfix'})
				),

				write = $('<div>', {id:'composer', class: 'composer'}).html(
					$('<ul>', {class: 'box-write'}).append(
						// 
						$('<li>', {class: 'write'}).append(
							$('<div>', {class: 'write-wrapper'}).html(
								$('<textarea>', {
									type: 'text',
									placeholder: 'เขียนข้อความ...',
									class: 'inputtext messageInput'
								})
							)
						),

						$('<li>', {class: 'send'}).append(
							
							$('<button>', { class: 'btn btn-blue btn-send disabled' }).append(
								  $('<i>', { class: 'img icon-paper-plane' })
								, $('<span>', { class: 'btn-text mls', text: 'ส่ง' })
							)
						)
					)
				),

				empty = $('<div>', {id: 'empty', class: 'empty'}).append(
					$('<div>', {class: 'empty-loading'}).html( 
						$('<div>', {class: 'loader-spin-wrap'}).html(
							$('<div>', {class: 'loader-spin'})
						)
					)
				);

			return $('<div>', {id:'popup', class: 'conversations-popup has-empty has-loading'}).append(
				header, content, write, empty
			);
		},

		/*
		/* Messenger */
		/**/
		Messenger: function ( id ) {
			var self = this;

			self.currentPopupId = id || "new";
			var data = self.data[ self.currentPopupId ];

			if( !data ){
				data = {
					id: self.currentPopupId,
					name: "Hotbet"
				};
			}
			 
			if( !data.$popup ){
				data.$popup = self.setPopup( data );
				self.loadMessage( data );
			}
			
			self.showPopup( data );
		},
		showPopup: function ( data ) {
			var self = this;

			self.$elem.find('.box-chat').html( data.$popup );

			// Event 
			self.scrollMessage( data );
			data.$popup.find('#content').scroll(function () {
				self.scrollMessageActive( self.data[ data.id ] );
			});

			$('.more-link', data.$popup).click(function (e) {
				self.loadMessageMore( self.data[ data.id ] );
				e.preventDefault();
			});
			
			self.replyMessage( data );

			// resize
			self.resizePopup( data );
			$( window ).resize( function () {
				self.resizePopup( data )
			} );
		},
		resizePopup: function ( data ) {
			var self = this;

			var height = self.$elem.height(),
				popup = data.$popup;

			var inner = height - (popup.find('#header').outerHeight() + popup.find('#composer').outerHeight() );

			popup.find('#empty').css('top', popup.find('#header').outerHeight());
			popup.find('#content').height( inner );
		},

		loadMessage: function ( data, delay ) {
			var self = this;

			data.$popup.find('#content').addClass('has-loading');
			if( !data.options ){
				data.getURL = self.options.URL + (data.id ? data.id : "");
				data.options = {};
			}

			setTimeout(function () {
				$.ajax({
					url: data.getURL,
					data: data.options,
					dataType: 'json'
				}).always(function () {
					data.$popup.find('#content').removeClass('has-loading');

					if(data.$popup.hasClass('has-loading')){
						data.$popup.removeClass('has-loading');
					}
				}).fail(function() { 
					data.$popup.find('#content').addClass('has-error');
				}).done(function ( results ) {

					if( results.lists.length ){
						self.buildFragMessage( data, results.lists );
					}

					if( !results.options.id ){
						return false;
					}

					if( data.id=='new' ){

						data.id = results.options.id;
						self.currentPopupId = data.id;
						self.data[ data.id ] = data;

						self.showPopup( data );
					}

					self.data[ data.id ] = $.extend( {}, data, results );
					data.$popup.find('#content').toggleClass('has-more', results.options.more);
				});

			}, delay || 1);
		},
		buildFragMessage: function ( data, results ) {
			var self = this;

			if( data.is_more ){
				for (var i = 0; i < results.length; i++) {
					self.displayMessage( data, results[i] );
				};
			}
			else{
				for (var i = results.length - 1; i >= 0; i--) {
					self.displayMessage( data, results[i] );
				};
			}

			var d = {};
			var box = data.$popup.find('.content');
			box.find('.list-message>li.newday').remove();
			$.each(box.find('.list-message>li'), function () {
				var item = $(this);
				if( item.data('time') ){
					d[item.attr('data-date')] = new Date( item.data('time') );
				}
				
			});

			$.each(d, function (id, date) {
				box.find('.list-message>li[data-date='+id+']').first().before( 
					$('<li>', {class:'clearfix newday', 'data-date': id }).html(
						$('<span>', {text: self.fullDate( date ) })
					) 
				);
			});

			self.scrollMessage( data );
		},
		fullDate: function (date){
			var month = ["มกราคม", "กุมภาาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฏาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"];
			return date.getDate() + " " + month[date.getMonth()] + " " + (date.getFullYear()+543);
		},
		displayMessage: function ( data, obj ) {
			var self = this,
				item = self.setItemMessage( obj );

			if( obj.evt=='update' ){	
				data.$popup.find('.list-message #msg-'+obj.cr_id).find('.text').html( obj.text );
			}
			else{
				if( !data.is_more || data.$popup.find('.list-message').find('li').length == 0){
					data.$popup.find('.list-message').append( item );
				}
				else{
					data.$popup.find('.list-message').find('li').not('.newday').first().before( item );
				}
			}
		},
		setItemMessage: function ( data ) {
			var self = this;

			var date = new Date( data.time );
			var hour =  date.getHours();
			hour = hour < 10 ? "0"+hour:hour;

			var m =  date.getMinutes();
			m = m < 10 ? "0"+m:m;

			var dateText = hour + ":" + m;

			var textDate = date.getFullYear() +'-'+ date.getMonth() +'-' + date.getDate();

			var li = $('<li>', {class: 'clearfix', id: 'msg-'+data.cr_id, 'data-date':textDate}) // , 
				.addClass( data.c_key_id==self.key_id ? 'send' : 'received' )
				.html(
					$('<div>', {class: 'box'}).append(
						  $('<div>', {class: 'text'}).html( data.text )
						, $('<div>', {class: 'time', text: dateText})
					)
				);

			li.data('time', date);

			return li;
		},
		scrollMessage: function ( data ) {
			var self = this;

			var box = data.$popup.find('.content');
			var outer = box.height() - box.find('.more').outerHeight(),
				inner = box.find('#listbox').outerHeight();

			if( data.is_more ){
				box.scrollTop( (inner-data.thatHeight) );
				self.data[ data.id ].is_more = false;
			}
			else{
				box.scrollTop( inner-outer );
			}
		},
		scrollMessageActive: function ( data ) {
			var self = this;

			if( data.$popup.find('#content').scrollTop()<=30 && data.options.more ){
				self.loadMessageMore( data );
			}
		},
		loadMessageMore: function  ( data ) {
			var self = this;
			
			data.options.pager++;
			data.is_more = true;
			data.thatHeight = data.$popup.find('#listbox').outerHeight();
			self.loadMessage( data );
		},

		/**/
		/* reply message */
		replyMessage: function ( data ) {
			var self = this;
			var $messageInput = $('<textarea>', {
				type: 'text',
				placeholder: 'เขียนข้อความ...',
				class: 'inputtext messageInput'
			});

			data.$popup.find('.write-wrapper').html( $messageInput );

			$messageInput.autosize({
				callback: function  ( e ) {
					self.resizePopup( data );
					return false;
				}
			});

			$messageInput.keydown(function(e){
				var text = $.trim( $(this).val() );

				// || (e.shiftKey&&e.keyCode==13)) 
				if ( (e.ctrlKey&&e.keyCode==13)  && text != ''){

					$(this).height( $(this).height() + 18 );
					$(this).val(text + "\n").focus();
					
				} else if(e.keyCode == 13) {
					self.sendMessage( data, text );
					e.preventDefault();
				}
			}).keyup(function () {
				data.$popup.find('.btn-send').toggleClass('disabled',  $.trim( $(this).val() )==''? true: false);
			}).focus();
		},
		sendMessage: function (data, text) {
			var self = this;

			if( text=='' ) return false;

			self.Session( 1 );
			data.$popup.find('.messageInput').attr('disabled', true);
			data.$popup.find('.btn-send').attr('disabled', true);

			$.ajax({
				type: 'post',
				url: self.options.URL+'send',
				data: {
					id: ( !data.id || data.id=='new' ? '': data.id),
					text: text
				},
				dataType: 'json'
			}).always(function () {
				data.$popup.find('.messageInput').removeAttr('disabled');
				data.$popup.find('.btn-send').removeAttr('disabled');
			}).fail(function() { 
				console.log( ' send Error ' );

			}).done(function ( results ) {

				if( !data.id ){
					data.id = results.id;
				}

				if( !self.key_id ){
					self.key_id = results.c_key_id;

					self.Session( 1, data, results );	
				}
				else{

					// self.updateMessage( data, results );
				}

				// self.current.id = results.id;
				data.$popup.find('.messageInput').val("").height( 18 ).focus();
				data.$popup.find('.btn-send').addClass('disabled');
				self.updateMessage( data, results );
				
			});
		},
		// Update new message to users who are online.
		updateMessage: function (data, msg) {
			var self = this;

			self.usersRef.once("value", function(snap) {
				
				if( !snap.val() ) return false;

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
				$.each(msg.ids, function(i, uesr){
					ids.push( parseInt(uesr) );
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

		/**/
		/* getNewMessage */
		getNewMessage: function () {
			var self = this;

			if( !self.key_id ) return false;
			self.is_online = true;
			var userMessagesRef = self.usersRef.child( self.key_id ).child('messages');

			// is new message to me 
			userMessagesRef.on('child_added', function(snapshot){

				var msg = snapshot.val();
				userMessagesRef.child( snapshot.name() ).remove();

				// update Message
				self.showMessage( msg );
			});
		},
		showMessage: function (msg) {
			var self = this;

			var data = self.data[ parseInt(msg.id) ];

			if( data ){

				if( data.$popup ){
					self.displayMessage( data, msg );
					self.scrollMessage( data );
				}
			}
			else{

				self.currentPopupId = msg.id;
				self.Messenger( msg.id );
			}
		},

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