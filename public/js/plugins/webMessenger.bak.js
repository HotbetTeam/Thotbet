

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

	var webMessenger = {
		init: function ( options, elem ) {
			var self = this;
			self.elem = elem;
			self.$elem = $(elem);

			self.options = $.extend( {}, $.fn.webMessenger.options, options );
			self.setElem();

			self.current = {};

			// self.ids = [];
			self.Events();

			self.currentUser = self.options.username;

			if( self.currentUser ){
				self.Server();

				$window.resize(function () {
					self.resize();
				});
			}
		},

		setElem: function () {
			var self = this;
			// self.$elem.find('.form-webMessenger').width( self.$elem.find('.form-webMessenger>li').length*300 );
		},

		Events: function () {
			var self = this;

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

			self.Recent();

			self.getNewMessage();
			// self.Messenger();
		},
		Session: function  ( length ) {
			var self = this;

			setTimeout(function() {

				// self.timeout = 

				// add user Online
				self.currentUserRef.update({
					timeout: new Date().getTime() + self.options.refresh
				});

				// remove user Online
				self.usersRef.once("value", function(snap) {
					self.userOnline( snap.val() );
				});

				if ( self.options.refresh ) {
					self.Session();
				}
			}, length || self.options.refresh );
		},
		userOnline: function ( data ) {
			var self = this;
			
			$.each(data, function(user, val){
				if( val ){

					var item = $('.js-user-online');

					// offline
					if( new Date().getTime() > val.timeout && user!=self.currentUser){


						if( item.attr('data-user-id')==user ){
							item.removeClass('online');
						}

						self.usersRef.child( user ).remove();
					}

					// online
					else{
						
						if( item.attr('data-user-id') == user ){
							item.addClass('online');
						}
						
					}

				}
			});
		},

		/**/
		/* Recent */
		/**/
		Recent: function () {
			var self = this;

			// set
			self.$recent = self.$elem.find('[role=recent]');
			self.$msgMain = self.$elem.find('.wmMasterMain');
			self.msg = [];

			self.recent = $.parseJSON( self.$recent.attr('data') );
			self.recent.data = [];
			self.$recent.removeAttr('data');

			self.recentBuildFrag( self.recent.lists );

			// Event
			$('li>a', self.$recent).click(function (e) {
				var item = $(this).parent();

				e.preventDefault();

				if(item.hasClass('active')) return;
				self.recentActive(item);
			});

		},
		recentLoad: function () {
			var self = this;

			setTimeout(function () {

				$.ajax({
					url: self.options.URL,
					data: self.data,
					dataType: 'json'
				}).always(function () {
					// self.$elem.removeClass('has-loading');
				}).fail(function() { 

				}).done(function ( data ) {
					
					self.data = $.extend( {}, self.data, data.options );

					// c_id: 0
					self.buildFrag( data.lists );
					
				});

			}, 1);
		},
		recentBuildFrag: function ( results ) {
			var self = this;

			
			for (var i = results.length - 1; i >= 0; i--) {
				
				self.recentDisplay( results[i] );
			};

			if( self.recent.data[ parseInt(self.options.current_id) ] ){
				self.recentActive( self.recent.data[ parseInt(self.options.current_id) ].$elem );
			}
		},
		recentItem: function ( data ) {

			data.current = false;
			data.online = false;

			data.is_loading = false;
			data.has_scroll = false;

			data.text = data.latest ? 
				data.latest.text.replace(/(<([^>]+)>)/ig,"")
				: '';

			var li = $('<li>', {class: "clearfix"}).append(
				$('<a>', {href: data.url}).append(
					$('<div>', {class: 'clearfix pvs'}).append(
						$('<div>', {class: 'status-online js-user-online', 'data-user-id': data.user_id}).append(
							'<i></i>', 
							'<span class="offline">ออฟไลน์</span>', 
							'<span class="online">ออนไลน์</span>'
						)

						, $('<div>', {class: 'clearfix'}).append(
							$('<div>', {class: 'name'}).append(
								$('<strong>', {text: data.name})
								, data.phone_number ? $('<span>', {text: data.phone_number}) : ''
							)
							, $('<div>', {class: 'text clearfix', text: data.latest? data.latest.text : ''})
							
						)
					)
				)
			);

			li.data(data);

			return li;
		},
		recentDisplay: function ( data ) {
			var self = this;

			// latest
			if( self.recent.data[ parseInt(data.id) ] ){
				// update
				var old = self.recent.data[ parseInt(data.id) ];

				var item = old.$elem.clone();

				item.find('.text').html( data.text.replace(/(<([^>]+)>)/ig,"") );
				
				old.$elem.remove();
				self.recent.data[ parseInt(data.id) ].$elem = item;
			}
			else{
				var item = self.recentItem( data );

				data.$elem = item;
				self.recent.data[ parseInt(data.id) ] = data;				
			}

			if( self.$recent.find('li').length==0 ){
				self.$recent.append( item );
			}
			else{
				self.$recent.find('li').first().before( item );
			}
		},
		recentActive: function ( $el ) {
			var self = this;

			ul = $el.closest('ul');
			ul.find('.active').removeClass('active');
			$el.addClass('active');

			var data = $el.data();
			if( !self.msg[ data.id ] ){
				self.msg[ data.id ] = data;
			}
			self.current = self.msg[ data.id ];

			// 
			var returnLocation = history.location || document.location,
				href = $el.find('a').attr("href");

			history.pushState('', self.current.name+' - ข้อความ', href);

			if( !self.current.$elem ){
				self.setRecentItem();

				self.loadMsg();
				self.newMessage();
			}

			// console.log( self.current );
			// self.current = 

			self.$msgMain.html( self.current.$elem );
			self.resize();
			
			// 			
		},
		setRecentItem: function () {
			var self = this;

			self.current.elems = {
				header: $('<header>', {class: 'chat-header clearfix'}).append(
					$('<div>', {class: 'lfloat'}).append(
						$('<div>', {class: ''}).append(
							$('<strong>', {text: self.current.name})
							, self.current.name.phone_number ? $('<span>', {text: self.current.name.phone_number}) : ''
						)
						, $('<div>', {class: 'status-online js-user-online', 'data-user-id': self.current.user_id}).append(
								'<i></i>', 
								'<span class="offline">ออฟไลน์</span>', 
								'<span class="online">ออนไลน์</span>'
							)
					)
				),

				content: $('<div>', {class: 'chat-content clearfix'}).append(
					  $('<div>',{ class: 'clearfix mbs pts uiMorePager'}).html(

					  		$('<a>', {class:'pam uiBoxLightblue uiMorePagerPrimary'}).append(
					  			  $('<span>', {class:'uiMorePagerLoader'}).html(
					  			  	$('<span>', {class:'skype-loader'}).append(
					  			  		$('<span>', {class:'dot'})
					  			  		, $('<span>', {class:'dot'})
					  			  		, $('<span>', {class:'dot'})
					  			  		, $('<span>', {class:'dot'})
					  			  	)
					  			  )
					  			, $('<span>', {class:'uiMorePagerText', text:'กำลังโหลดข้อความ...'})
					  		)
					  		
					  )
					, $('<ul>', {class: 'list-message clearfix has-time'})
				),

				write: $('<div>', {class: 'chat-write'}).html(
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
				)
			}

			self.current.$elem = $('<div>', {class: 'has-empty'}).append(
				self.current.elems.header,
				self.current.elems.content,
				self.current.elems.write
			);
		},
		resize: function () {
			var self = this;
			
			if( self.current.elems ){

				var height = $(window).height();

				inner = height - (self.current.elems.header.outerHeight() + self.current.elems.write.outerHeight() );

				self.current.elems.content.height( inner - $("#tobar").outerHeight() );
			}
		},


		loadMsg: function ( id ) {
			var self = this;

			self.current.elems.content.addClass('has-loading');
			self.current.is_loading = true;
			$.get( self.options.URL + 'get/'+self.current.id, self.current.options, function ( results ) {

				self.current = $.extend( {}, self.current, results );
				
				if( results.options.more==false ){
					self.current.elems.content.removeClass('has-loading');
				}

				self.buildFrag(results.lists);
				self.display();

				// update Data
				self.msg[ self.current.id ] = self.current;
			}, 'json' );
		},
		buildFrag: function ( results ) {
			var self = this;

			if( self.current.has_scroll ){
				for (var i = 0; i < results.length; i++) {
					var obj = results[i];
					self.display( self.setItem( obj ), obj );
				};
			}
			else{

				for (var i = results.length - 1; i >= 0; i--) {
					var obj = results[i];
					self.display( self.setItem( obj ), obj );
				};
			}

			var d = {};
			var box = self.current.elems.content;
			box.find('.list-message>li.newday').remove();
			$.each(box.find('.list-message>li'), function () {
				var item = $(this);
				d[item.attr('data-date')] = item.data('time');
			});

			$.each(d, function (id, date) {
				
				box.find('.list-message>li[data-date='+id+']').first().before( 
					$('<li>', {class:'clearfix newday', 'data-date': id }).html(
						$('<span>', {text: self.fullDate( date ) })
					) 
				);
			});
			

			self.current.is_loading = false;
			self.scrollMsg();

			if( self.current.has_scroll ){
				self.current.has_scroll = false;
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
		display: function ( item ) {	
			var self = this;

			// var date = new Date( item.data('time') );	

			var box = self.current.elems.content;

			if( self.current.has_scroll ){

				var li =box.find('.list-message>li').not('.newday').first();		
				li.before( item );
			}
			else{
				box.find('.list-message').not('.newday').append( item );
			}
		},

		timeDay: function (date) {
			
			date.setHours(0);
			date.setMinutes(0);
			date.setSeconds(0);

			return date.getTime();
		},
		
		scrollMsg: function () {
			var self = this;

			var box = self.current.elems.content;
			var outer = box.height()-self.current.elems.content.find('.uiMorePager').outerHeight();
			var height = box.find('.list-message').outerHeight() ;

			if( outer<height && !self.current.has_scroll ){

				box.scrollTop( height-outer );
			}
			else if( !self.current.hover_scroll ){

				var t = (height-outer)-(self.current.scroll_height+outer);
				box.animate({
		          scrollTop: t
		        }, 800);

				// box.scrollTop(  );
			}

			box.scroll(function (e) {

				var top = self.current.is_loading 

				self.current.hover_scroll = true;
				if( $(this).scrollTop() == 0 && self.current.options.more && !self.current.is_loading ){
					self.current.options.pager++;
					self.current.has_scroll = true;
					self.current.hover_scroll = false;
					self.current.scroll_height = (height-outer) - 80 ;
					self.loadMsg();
				}

				e.preventDefault();
			});
		},

		// send Message
		newMessage: function () {
			
			var self = this;

			self.resize();
			self.current.elems.write.find('.messageInput').autosize({
				callback: function  ( e ) {

					self.resize();
					return false;
				}
			});


			self.current.elems.write.find('.messageInput').keydown(function(e){
				var text = $.trim( $(this).val() );

				if (!e.ctrlKey && e.keyCode == 13 && text != ''){

					self.sendMessage( text );
					e.preventDefault();
				} else if(e.keyCode == 13) {

					$(this).height( $(this).height() + 18 ); 
					$(this).val(text + "\n");
				}
			}).keyup(function () {
				self.current.elems.write.find('.btn-send').toggleClass('disabled',  $.trim( $(this).val() )==''? true: false);
			});

			self.current.elems.write.find('.btn-send').click(function (e) {
				e.preventDefault();
				var text = $.trim( self.current.elems.write.find('.messageInput').val() );

				if( text == '' ) return false;
				self.sendMessage( text );
			});
		},
		sendMessage: function ( text ) {
			var self = this;


			self.current.elems.write.find('.messageInput').attr('disabled');

			$.ajax({
				type: 'post',
				url: self.options.URL+'send',
				data: {
					id: self.current.id,
					text: text
				},
				dataType: 'json'
			}).always(function () {
				self.$elem.find('.messageInput').removeAttr('disabled');
			}).fail(function() { 

			}).done(function ( results ) {

				self.current.id = results.id;
				self.current.elems.write.find('.messageInput').val("").height( 18 ).focus();
				self.current.elems.write.find('.btn-send').addClass('disabled');

				self.updateMessage( results );				
			});
		},

		// Update new message to users who are online.
		updateMessage: function ( msg ) {
			var self = this;

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

				// update Recent
				self.recentDisplay( msg );

			});
		},

		showMessage: function (msg) {
			var self = this;

			if( msg.evt=='update' ){

				self.current.elems.content.find('.list-message #msg-'+msg.cr_id+' .text').html(msg.text);
			}
			else{
				self.display( self.setItem( msg ) );
			}
			
			self.scrollMsg();
		}

	};

	$.fn.webMessenger = function( options ) {
		return this.each(function() {
			var $this = Object.create( webMessenger );
			$this.init( options, this );
			$.data( this, 'webMessenger', $this );
		});
	};

	$.fn.webMessenger.options = {
		refresh: 60000,
		onOpen: function() {},
		onClose: function() {}
	};
	
})( jQuery, window, document );