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
			

			$( window ).resize( function () {
				self.resize();
			});
			self.resize();


			self.config();
			self.events();

			if( self.currentUser && self.online ){
				self.Server();
			}
		},

		resize: function () {
			var self = this;

			var outer = $( window );
			var innerNav = self.$elem.find('.navigation-chat-content').offset();

			self.$elem.find('.navigation-chat-content').height( outer.height()-innerNav.top )
		},

		config: function () {
			var self = this;

			self.currentUser = self.options.username;
			self.online = navigator.onLine;

			self.data = [];
			self.key_id = self.options.key_id;
			self.$main = self.$elem.find('.chat-main-content');
		},
		events: function () {
			var self = this;

			var v;
			self.pageon = true;
			$( window ).mousemove(function () {
				if( !self.pageon ){
					// self.Session( 1 );
					self.pageon = true;
					clearTimeout( v );
				}
				
			}).mouseout(function () {
				v = setTimeout(function() {
					self.pageon = false;
				}, 30000 );
			});


			self.currentList = "recent";
			self.activeList();

			$('[data-nav-link]', self.$elem).click(function (e) {

				self.currentList = $(this).attr('data-nav-link');
				self.activeList();
				e.preventDefault();
			});

			self.$elem.delegate('.chat-navigation-item', 'click',function (e) {

				$(this).addClass('active').siblings().removeClass('active');

				self.activeMsg( $(this) );
				e.preventDefault();
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
			// 
			self.Session( 1 );
			

			self.getNewMessage();
		},
		Session: function  ( length ) {
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
					self.changeUserOnline( snap.val() );
				});

				if ( self.options.refresh ) {
					self.Session();
				}
			}, length || self.options.refresh );
		},
		changeUserOnline: function ( data ) {
			var self = this;

			if( !data || !self.key_id) return false;

			// navigator.onLine
			$.each(data, function(user, val){

				if( val ){
					if( new Date().getTime() > val.timeout && user!=self.key_id){
						self.usersRef.child( user ).remove();
					}
				}
			});
		},

		activeList: function () {
			var self = this;

			a = self.$elem.find('[data-nav-link='+self.currentList+']');
			if( a.parent().hasClass('active') ) return false;

			a.parent().addClass('active').siblings().removeClass('active');

			box = self.$elem.find('[data-nav-ref='+self.currentList+']');
			listbox = box.find('.navigation-chat-items');
			listbox.empty();

			box.addClass('active').siblings().removeClass('active');

			self.list = {
				$elem: box,
				$listbox: listbox
			};
			self.loadList();
		},
		loadList: function () {
			var self = this;

			self.list.$elem.addClass('has-loading');
			setTimeout(function () {

				$.ajax({
					url: self.options.URL+'nav/'+self.currentList,
					data: self.list.options || {},
					dataType: 'json'
				}).always(function () {
					self.list.$elem.removeClass('has-loading');
				}).fail(function() { 

				}).done(function ( data ) {
					
					self.list = $.extend( {}, self.list, data );

					// console.log( self.list );
					// c_id: 0
					self.listBuildFrag( self.list.lists );
					
				});

			}, 1);
		},
		listBuildFrag: function ( results ) {
			var self = this;
			
			$.each( results, function ( i, obj) {
				
				self.listDisplay( obj );
			} );
		},
		listItem: function (data) {

			data.id = !data.id || data.id==0 ? 'new' : data.id;
			li = $('<li>', {class: 'chat-navigation-item clearfix'}).append(

				  $('<div>', {class: 'point-online'})

				, $('<a>', {class: 'anchor clearfix pvs'}).append(

					  $('<div>', {class: 'avatar avatar-icon mrm lfloat'}).html(
						$('<i>', {class: 'icon-user'})
					)
					, $('<div>', {class: 'content'}).append(
						  $('<div>', {class: 'spacer'})
						, $('<div>', {class: 'massages'}).append(
							  $('<div>', {class: 'fullname'}).html( data.title )
							, $('<div>', {class: 'text'})
						)

					)

				)
			);

			li.data(data);

			return li;
		},
		listDisplay: function ( data ) {
			var self = this;

			item = self.listItem(data)
			self.list.$listbox.append( item );
		},

		activeMsg: function ( $el ) {
			var self = this;

			self.msg = $el.data() || {};

			if( !self.msg.$popup ){
				self.msg.$popup = self.setMsgPopup( self.msg );

				self.msgLoad( self.msg );
			}

			self.showMsgPopup( self.msg );
		},
		setMsgPopup: function ( data ) {
			var self = this;

			var header = $('<header>', {id:'header', class: 'header chat-header clearfix'}).append(
					$('<div>', {class: 'lfloat'}).append(
						$('<div>', {class: ''}).append(
							$('<strong>', {text: data.title})
							, data.phone_number ? $('<span>', {class:'mls', text: data.phone_number}) : ''
						)
						, $('<div>', {class: 'status-online js-user-online', 'data-user-id': data.user_id}).append(
								'<i></i>', 
								'<span class="offline">ออฟไลน์</span>', 
								'<span class="online">ออนไลน์</span>'
							)
					)
				),

				content = $('<div>', {id:'content', class: 'content chat-content clearfix'}).append(
			  		$('<div>', {class: 'more'}).append( 
						$('<div>', {class: 'loading loader-spin-wrap'}).html(
							$('<div>', {class: 'loader-spin'})
						),
						$('<a>', {class: 'more-link', text: 'โหลดข้อความเพิ่มเติม'})
					)					  
					, $('<ul>', {id: 'listbox', class: 'list-message clearfix has-time'})
				),

				write = $('<div>', {id:'write', class: 'write chat-write'}).html(
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

			return $('<div>', {id:'popup', class: 'popup has-empty has-loading'}).append(
				header, content, write, empty
			);
		},
		showMsgPopup: function ( msg ) {
			var self = this;

			self.$main.addClass( 'has-message' ).find('.chat-main-message').html( msg.$popup );


			self.msgScroll( msg );
			msg.$popup.find('#content').scroll(function () {
				self.msgScrollActive( msg );
			});

			self.msgReply( msg );

			// resize
			self.msgResizePopup( msg.$popup );
			$( window ).resize( function () {
				self.msgResizePopup( msg.$popup )
			} );
		},
		msgResizePopup: function  ( $popup ) {
			var self = this,
				height = $(window).height();

			var inner = height - ($popup.find('.chat-header').outerHeight() + $popup.find('#write').outerHeight() );

			$popup.find('#empty').css('top', $popup.find('.chat-header').outerHeight());
			$popup.find('#content').height( inner - $("#tobar").outerHeight() );
		},

		msgLoad: function( msg, delay ){
			var self = this;

			msg.$popup.find('#content').addClass('has-loading');
			if( !msg.options ){
				msg.getURL = self.options.URL+'conversation/'+msg.id;
				msg.options = {};
			}


			setTimeout(function () {

				$.ajax({
					url: msg.getURL,
					data: msg.options,
					dataType: 'json'
				}).always(function () {
					msg.$popup.find('#content').removeClass('has-loading');

					if(msg.$popup.hasClass('has-loading')){
						msg.$popup.removeClass('has-loading');
					}
				}).fail(function() { 
					msg.$popup.find('#content').addClass('has-error');

					console.log( 'Error' );
				}).done(function ( results ) {



					// self.data[ self.currentId ] = $.extend( {}, data, results );
					msg.$popup.find('#content').toggleClass('has-more', results.options.more);

					if( results.lists.length ){
						self.msgBuildFrag( msg, results.lists );
					}

					if( !self.key_id && results.key_id){
						self.key_id = results.key_id;
					}

					self.Session( 1 );
				});
			}, delay || 1);
		},

		msgBuildFrag: function ( msg, results ) {
			var self = this;

			var self = this;

			if( msg.is_more ){
				for (var i = 0; i < results.length; i++) {
					self.msgDisplay( msg, results[i] );
				};
			}
			else{
				for (var i = results.length - 1; i >= 0; i--) {
					self.msgDisplay( msg, results[i] );
				};
			}

			var d = {};
			var box = msg.$popup.find('.content');
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

			self.msgScroll( msg );
		},
		fullDate: function (date){
			var month = ["มกราคม", "กุมภาาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฏาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"];
			return date.getDate() + " " + month[date.getMonth()] + " " + (date.getFullYear()+543);
		},
		msgDisplay: function ( msg, result ) {
			var self = this,
				item = self.msgSetItem( result );

			msg.latest = result;
			if( result.evt=='update' ){	
				msg.$popup.find('.list-message #msg-'+result.cr_id).find('.text').html( result.text );
			}
			else{

				if( !msg.is_more || msg.$popup.find('.list-message').find('li').length == 0){
					msg.$popup.find('.list-message').append( item );
				}
				else{
					msg.$popup.find('.list-message').find('li').not('.newday').first().before( item );
				}

				
			}
		},
		msgSetItem: function ( data ) {
			var self = this;

			var date = new Date( data.time );
			var hour =  date.getHours();
			hour = hour < 10 ? "0"+hour:hour;

			var m =  date.getMinutes();
			m = m < 10 ? "0"+m:m;

			var dateText = hour + ":" + m;

			var textDate = date.getFullYear() +'-'+ date.getMonth() +'-' + date.getDate();

			var li = $('<li>', {class: 'clearfix', 'data-r-id': data.cr_id, 'data-date':textDate}) // , 
				.addClass( data.c_key_id==self.key_id ? 'send' : 'received' )
				.html(
					$('<div>', {class: 'box'}).append(
						  $('<div>', {class: 'text'}).html( data.text )
						, $('<div>', {class: 'time', text: dateText})
						/*, $('<div>', {class: 'actor'}).append(
							$('<span>', {text: 'ส่งโดย '})
							, $( '<a>', {href: '', text: data.name} )
						)*/
					)
				);

			li.data('time', date);

			return li;
		},
		msgScroll: function ( msg ) {
			var self = this;

			var box = msg.$popup.find('.content');

			var outer = box.height() - box.find('.more').outerHeight(),
				inner = box.find('.list-message').outerHeight();

			if( msg.is_more ){


				box.scrollTop( (inner-msg.thatHeight) );
				// self.data[ data.id ].is_more = false;
			}
			else{
				box.scrollTop( inner-outer );
			}
		},
		msgScrollActive: function ( msg ) {
			// body...
		},
		msgLoadMore: function  ( msg ) {
			var self = this;
			msg.options.pager++;
			msg.is_more = true;
			msg.thatHeight = msg.$popup.find('#listbox').outerHeight();
			self.msgLoad( msg );
		},

		/**/
		/* reply message */
		msgReply: function ( msg ) {
			var self = this;
			var $messageInput = $('<textarea>', {
				type: 'text',
				placeholder: 'เขียนข้อความ...',
				class: 'inputtext messageInput'
			});

			msg.$popup.find('.write-wrapper').html( $messageInput );

			$messageInput.autosize({
				callback: function  ( e ) {
					self.resizePopup( msg.$popup );
					return false;
				}
			});

			$messageInput.keydown(function(e){
				var text = $.trim( $(this).val() );

				//  
				if ( ((e.ctrlKey&&e.keyCode==13) || (e.shiftKey&&e.keyCode==13))  && text != ''){

					/*$(this).height( $(this).height() + 18 );
					$(this).val(text + "\n").focus();*/
					
				} else if(e.keyCode == 13) {
					self.msgSend( msg, text );
					e.preventDefault();
				}
			}).keyup(function () {
				msg.$popup.find('.btn-send').toggleClass('disabled',  $.trim( $(this).val() )==''? true: false);
			}).focus();	
		},
		msgSend: function (msg, text) {
			var self = this;

			if( !text ) return false;			

			// self.Session( 1 );
			msg.$popup.find('.messageInput').attr('disabled', true);
			msg.$popup.find('.btn-send').attr('disabled', true);

			var dataPost = {
				text: text,
				note: 'กำลังส่ง' 
			};

			/*var item = self.msgSetItem( dataPost );
			var $last = msg.$popup.find('.list-message').find('li').not('.newday').last();
			if( $last.length==1 ){
				data = $last.data();

				console.log( data );
			}
			msg.$popup.find('.list-message').append( item );
			self.msgScroll( msg, 800 );*/

			$.ajax({
				type: 'post',
				url: self.options.URL+'send',
				data: {
					id: msg.id,
					text: text,
					key_id: msg.key_id,
					to: msg.m_id
				},
				dataType: 'json'
			}).always(function () {
				msg.$popup.find('.messageInput').removeAttr('disabled').val("").height( 18 ).focus();
				msg.$popup.find('.btn-send').removeAttr('disabled').addClass('disabled');
			}).fail(function() { 
				console.log( ' send Error ' );

			}).done(function ( results ) {
				self.msgUpdate( msg, results );				
			});	
		},

		// Update new message to users who are online.
		msgUpdate: function (msg, results) {
			var self = this;

			if( !self.key_id ){
				// self.key_id = 
			}

			if( msg.id=='new' ){
				msg.id = results.id;
			}

			self.Session( 1 );

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
				$.each(results.ids, function(i, uesr){
					ids.push( parseInt(uesr) );
				});

				$.each(self.arrayUnique(ids), function(i, id){

					if( usersAreOnline.indexOf( id )>=0 ){
						// Update new message to user
						var usersMessagesRef = self.usersRef.child(id+'/messages');
						usersMessagesRef.push(results);
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

			console.log( data, msg );

			/*if( data ){

				if( data.$popup ){
					self.displayMessage( data, msg );

					self.scrollMessage( data );
				}

				if( data.$nav ){

					if( data.$nav.index()>0 ){

						// update
						// var old = self.data[ parseInt(data.id) ];
						var item = data.$nav.clone();
						data.$nav.remove();
						self.data[ parseInt(msg.id) ].$nav = item;
						item.data( data );

						if( self.$recent.find('li').length==0 ){
							self.$recent.append( item );
						}
						else{
							self.$recent.find('li').first().before( item );
						}
					}
					
					// update text recent 
					var text = msg.text.replace(/(<([^>]+)>)/ig," ");
					data.$nav.find('.text').html( text );
				}

			}
			else{

				var newData = {
					id: msg.id,
					// name: 'sss',
					updated: msg.time,
					latest: {
						// name: 'text',
						text: msg.text,
						user_id: msg.user_id,
					}
				};

				$.each( msg.users, function (i, user) {
					if( msg.user_id==user.user_id ){
						newData.name = user.name;
					}
				} );

				self.recentDisplay( newData );
			}*/
		},



		

		/**/
		/* Recent */
		/**/
		Recent: function () {
			var self = this;

			// set
			self.$recent = self.$elem.find('[role=recent]');
			self.$msgMain = self.$elem.find('.wmMasterMain');

			self.recent = $.parseJSON( self.$recent.attr('data') );
			self.$recent.removeAttr('data');

			self.data = [];
			self.recentBuildFrag( self.recent.lists );

			self.$recent.delegate('.conversation-link', 'click',function (e) {
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

			if( self.data[ parseInt(self.options.current_id) ] ){
				self.recentActive( self.data[ parseInt(self.options.current_id) ].$nav );
			}
		},
		recentDisplay: function ( data ) {
			var self = this;

			// latest
			if( self.data[ parseInt(data.id) ] ){
				// update
				var old = self.data[ parseInt(data.id) ];

				var item = old.$nav.clone();

				item.find('.text').html( data.text.replace(/(<([^>]+)>)/ig,"") );
				
				old.$nav.remove();
				self.data[ parseInt(data.id) ].$nav = item;
			}
			else{
				var item = self.recentItem( data );

				data.$nav = item;
				self.data[ parseInt(data.id) ] = data;				
			}

			if( self.$recent.find('li').length==0 ){
				self.$recent.append( item );
			}
			else{
				self.$recent.find('li').first().before( item );
			}
		},
		recentItem: function ( data ) {
			var self = this;

			data.current = false;
			data.online = false;

			data.is_loading = false;
			data.has_scroll = false;

			data.text = data.latest ? 
				data.latest.text.replace(/(<([^>]+)>)/ig," ")
				: '';

			var box = $('<a>', {href: data.url, class: 'conversation-link'}).append(
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
						, $('<div>', {class: 'text clearfix'}).html( data.text? data.text : '' )
						
					)
				)
			);

			var li = $('<li>', {class: "clearfix"}).append( box );

			li.data(data);

			// Event
			/*box.click(function (e) {
				var item = $(this).parent();

				e.preventDefault();

				if(item.hasClass('active')) return;
				self.recentActive(item);
			});*/

			return li;
		},
		recentActive: function ( $el ) {
			var self = this;

			ul = $el.closest('ul');
			ul.find('.active').removeClass('active');
			$el.addClass('active');

			var data = $el.data();
			self.currentId = parseInt(data.id);
			// self.data[ self.currentId ].current = true;

			if( !self.data[ self.currentId ] ){

				return false;
			} 

			self.Session( 1 );
			if( !self.data[ self.currentId ].$popup ){

				self.data[ self.currentId ].$popup = self.setPopup( self.data[ self.currentId ] );

				self.loadMessage( self.data[ self.currentId ] );
				
				// Event
				self.data[ self.currentId ].$popup.find('.more-link').click(function () {
					self.loadMessageMore( self.data[ self.currentId ] );
				});
			}

			// set url
			var returnLocation = history.location || document.location,
				href = $el.find('a').attr("href"),
				title = self.data[ self.currentId ].name+' - ข้อความ';
			history.pushState('', title, href);
			document.title = title;

			// show popup
			self.showPopup( self.data[ self.currentId ] );
		},

		/**/
		/* popup */
		
		showPopup: function ( data ) {
			var self = this;

			// show
			self.$msgMain.html( data.$popup );

			// Event 
			self.scrollMessage( data );
			data.$popup.find('#content').scroll(function () {
				self.scrollMessageActive( self.data[ data.id ] );
			});

			self.replyMessage( data );

			// resize
			self.resizePopup( data.$popup );
			$( window ).resize( function () {
				self.resizePopup( data.$popup )
			} );
		},
		resizePopup: function ( $popup ) {
			var self = this,
				height = $(window).height();

			var inner = height - ($popup.find('.chat-header').outerHeight() + $popup.find('#write').outerHeight() );

			$popup.find('#empty').css('top', $popup.find('.chat-header').outerHeight());
			$popup.find('#content').height( inner - $("#tobar").outerHeight() );
		},

		/**/
		/* load message */
		loadMessage: function ( data, delay ) {
			var self = this;

			data.$popup.find('#content').addClass('has-loading');

			if( !data.options ){
				data.getURL = self.options.URL+'conversation/'+data.id;
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

					console.log( 'Error' );
				}).done(function ( results ) {

					self.data[ self.currentId ] = $.extend( {}, data, results );

					data.$popup.find('#content').toggleClass('has-more', results.options.more);

					if( results.lists.length ){
						self.buildFragMessage( data, results.lists );
					}
				});
			}, delay || 1);
		},
		buildFragMessage: function (data, results){
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
		
		displayMessage: function (data, obj) {
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
				.addClass( data.user_id==self.currentUser ? 'send' : 'received' )
				.html(
					$('<div>', {class: 'box'}).append(
						  $('<div>', {class: 'text'}).html( data.text )
						, $('<div>', {class: 'time', text: dateText})
						, $('<div>', {class: 'actor'}).append(
							$('<span>', {text: 'ส่งโดย '})
							, $( '<a>', {href: '', text: data.name} )
						)
					)
				);

			li.data('time', date);

			return li;
		},
		scrollMessage: function ( data ) {
			var self = this;

			var box = data.$popup.find('.content');

			var outer = box.height() - box.find('.more').outerHeight(),
				inner = box.find('.list-message').outerHeight();

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

			self.data[ data.id ]._scroll = false;
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
					self.resizePopup( data.$popup );
					return false;
				}
			});

			$messageInput.keydown(function(e){
				var text = $.trim( $(this).val() );

				//  
				if ( ((e.ctrlKey&&e.keyCode==13) || (e.shiftKey&&e.keyCode==13))  && text != ''){

					/*$(this).height( $(this).height() + 18 );
					$(this).val(text + "\n").focus();*/
					
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

			self.Session( 1 );
			data.$popup.find('.messageInput').attr('disabled', true);
			data.$popup.find('.btn-send').attr('disabled', true);

			$.ajax({
				type: 'post',
				url: self.options.URL+'send',
				data: {
					id: data.id,
					text: text
				},
				dataType: 'json'
			}).always(function () {
				data.$popup.find('.messageInput').removeAttr('disabled');
				data.$popup.find('.btn-send').removeAttr('disabled');
			}).fail(function() { 
				console.log( ' send Error ' );

			}).done(function ( results ) {

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

	};

	$.fn.webMessenger = function( options ) {
		return this.each(function() {
			var $this = Object.create( webMessenger );
			$this.init( options, this );
			$.data( this, 'webMessenger', $this );
		});
	};

	$.fn.webMessenger.options = {
		refresh: 13000,
		onOpen: function() {},
		onClose: function() {}
	};
	
})( jQuery, window, document );