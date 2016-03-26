// Utility
if ( typeof Object.create !== 'function' ) {
	Object.create = function( obj ) {
		function F() {};
		F.prototype = obj;
		return new F();
	};
}

(function( $, window, document, undefined ) {

var $window = $( window ),
	$doc = $('div#doc');

var Gallery = {
		init: function( options, elem ) {
			var self = this;

			self.elem = elem;
			self.$elem = $( elem );
			self.options = $.extend( {}, $.fn.photoGallery.options, options );

			if( !self.options.current_id && self.$elem.attr('data-id')){
				self.options.current_id = self.$elem.attr('data-id');
			}

			if( self.$elem.attr('href') ){
				self.url = self.$elem.attr('href');
			}
			else if( self.options.url ){
				self.url = self.options.url;
			}

			if( !self.url ) return false;

			// config
			self.is_open = false;

			self.initEvent();

			// photo.php?id=912882982134441&set=a.324096261013119.71763.100002382449231&type=3&theater
		},

		initEvent: function () {
			var self = this;

			self.$elem.click(function (e) {
				e.preventDefault();
				// console.log( 1 );
				self.loadGallery();
			});
		},

		loadGallery: function () {
			var self = this;

			Event.showMsg({ load:true });

			$.ajax({
				url: self.url,
				// data: self.data,
				dataType: 'json'
			}).done(function( results ) {
				
				self.setModel();

				self.buildFrag( results );
				self.display();

				self.Events();
				self.openModel();

			}).fail(function() { 
				// error
				Event.showMsg({ text: "เกิดข้อผิดพลาด...", load: true , auto: true });
			}).always(function() { 
				// complete
				Event.hideMsg();
			});
		},

		buildFrag: function (results) {
			var self = this;
			var fullWidth = 0;


			if( results.total > 1 ){
				self.$model.addClass("pagingReady");
			}

			self.photos = [];
			$.each(results.lists, function (i,obj) {
				
				if( results.current==obj.photo_id || self.options.current_id==obj.photo_id ){
					self.currentPhotoLength = i;
				}

				// obj = self.loadImage(obj);
				/*if( obj.file_size ){
					obj.width = obj.file_size[0];
					obj.height = obj.file_size[1];
				}*/

				self.photos.push( obj );
			} );

			if( !self.currentPhotoLength ){
				self.currentPhotoLength = 0;
			}
			self.setImage();
		},

		loadImage: function (url) {
			var self = this;
			// console.log( obj );

			var image = new Image();
			image.onload = function () {
				var img = this;

				self.width = img.width;
				self.height = img.height;
				self.stage();
			}

			image.onerror = function(e){

			}

			image.src = url;
		},

		setResizeImage: function () {
			var self = this;

			self.nWidth = $window.width() - self.options.margin_width;
			self.nHeight = $window.height() - self.options.margin_height;

			if( !self.currentPhoto.width ){
				self.loadImage( self.currentPhoto.url );
			}
			else{
				self.width = self.currentPhoto.width;
				self.height = self.currentPhoto.height;
				self.stage();
			}

		},
		stage: function () {
			var self = this;

			if( self.width > self.height  ){
				self.sX();
				self.sY();
			}
			else{
				self.sY();
				self.sX();
			}

			// 
			self.$model.find('.stageWrapper,.stage,.img').css({
				width: self.width,
				height: self.height
			});
		},
		sX: function () {
			var self = this;

			if( self.width > innerWidth ){
				self.width = innerWidth;
				self.height = parseInt( (self.currentPhoto.height*innerWidth)/self.currentPhoto.width );
			}
		},

		sY: function () {
			var self = this;

			if( self.height > self.nHeight ){
				self.height = self.nHeight;
				self.width = parseInt( (self.currentPhoto.width*self.nHeight)/self.currentPhoto.height );
			}
		},

		display: function () {
			var self = this;

			if( self.$model.hasClass('pagingActivated') ){
				self.$model.removeClass("pagingActivated");
			}

			self.$model.find('.stage').html( self.$img );
			self.setResizeImage();

			if( self.photos.length > 1 ){

				self.$stageActions.find( '.mediaCount' ).text( (self.currentPhotoLength+1) + " จาก " + self.photos.length );
			}
			
		},

		setImage: function () {
			var self = this;

			self.currentPhoto = self.photos[self.currentPhotoLength];		
			self.$img = $('<img/>', {class: 'img', src: self.currentPhoto.url + "?rand="+Math.random() });
		},

		Events: function () {
			var self = this;

			var $wrapper = self.$model.find('.stageWrapper');
			// Mouse
			$wrapper.mouseenter(function() {

				self.focus = true;
				self.$model.addClass("pagingActivated");

			}).mouseleave(function(){

				self.focus = false;
				self.$model.removeClass("pagingActivated");

			}).mousemove(function( e ){

				if( !self.$model.hasClass("pagingActivated") ){
					self.$model.addClass("pagingActivated");
				}

				var width = $(this).width(),
					hilightPrev = (25*width)/100,
				 	xLeft = e.pageX-$(this).position().left,
				 	prev = self.$model.find(".galleryPager.prev"),
				 	next = self.$model.find(".galleryPager.next");

				 	if( xLeft < hilightPrev ){
						if( prev.hasClass("hilightPager")==false ){
							prev.addClass("hilightPager");
							next.removeClass("hilightPager");

							self.pager = "prev";
						}
					}else{
						if( next.hasClass("hilightPager")==false ){
							next.addClass("hilightPager");
							prev.removeClass("hilightPager");

							self.pager = "next";
						}
					}
			}).click(function(e){

				e.stopPropagation();
				self.prevnext();
			});

			$(document).keyup(function(e){
				if( self.is_open == true){

					if( e.keyCode == 27 ){ // e.which
						self.closeModel();
					}else if(e.keyCode == 37){ // prev
						self.prevnext( "prev" );
					}else if( e.keyCode == 39 ){// next
						self.prevnext( "next" );
					}
				}
			});
		},

		prevnext: function ( e ) {
			var self = this;

			if(!self.$model.hasClass('pagingReady')){ return false; }

			var prevnext = e||self.pager;
			
			if( prevnext=='next' ){

				self.currentPhotoLength = self.photos[self.currentPhotoLength+1]
					? self.currentPhotoLength+1
					: 0;
			}
			else{
				self.currentPhotoLength = self.photos[self.currentPhotoLength-1]
					? self.currentPhotoLength-1
					: self.photos.length-1;
			}

			self.setImage();
			self.display();
		},

		openModel: function () {
			var self = this;

			self.$model.addClass( 'show' );

			self.is_open = true;

			$window.resize(function () {
				self.setResizeImage();
			});
		},

		closeModel: function () {
			var self = this;

			var scroll = parseInt($doc.css("top"));
			scroll= scroll < 0 ? scroll*-1:scroll;

			self.$model.removeClass("show");

			setTimeout( function(){

				self.$model.remove();

				if ( typeof self.onClose==='function' ) {
					self.onClose();
				}

				if( $('.model').not('.hidden_elem').length ) return false;

				$doc.removeClass('fixed_elem').css('top', "");
				$(window).scrollTop( scroll );

				$('html').removeClass('hasModel');
			}, 300);
		},

		setSequenceCurrent: function () {
			var self = this;

			var $current = self.$model.find('.current');
			var $container = self.$model.find('.gallery-content');
			var fWidth = self.fullContent;
			var marginLeft = 0;
			var marginR = 0;

			var width = $current.width();
			var fullWidth = $window.width();
			var left = (fullWidth/2)-(width/2);
			var left =  left<0 ? left*-1:left;

			if( $current.prev().length ){
				var $prev = $current.prev();

				marginLeft = left-150; //$prev.width()/2
				fWidth+=marginLeft;
				
			}

			if( $current.next().length ){

				var $next = $current.next();
				marginR = parseInt(fullWidth-(left+width))-150; //$next.width()/2
				fWidth+=marginR;
			}

			$current.css({
				marginLeft: marginLeft,
				marginRight: marginR
			});

			$container.css('width', fWidth);

			var leftWidth = 0;
			$.each( $container.find('>li'), function (i, obj) {
				var item = $(this);
				if( item.hasClass('current') ) return false;
				leftWidth += parseInt(item.css('width'));
			} );

			$container.css('left', ((leftWidth+marginLeft)*-1)+left );

		},
		setModel: function () {
			var self = this;

			self.$model = $('<div/>',{class: 'model black model-gallery'});

			self.$stageActions = $('<div/>',{class: 'stageActions'});

			self.$stageActions.html(
				$('<div/>', {class: 'clearfix mediaOverlayBar'}).append(
					$('<div/>',{class: 'mediaTitleInfo'}).append(
						$('<h2/>', {class: 'mediaTitle mrm hidden_elem'})
						, $('<span/>', {class: 'mediaCount'})
					)
				)
			)

			self.$model.html(
				$('<div/>',{class: 'galleryContainer'}).html(

					$('<div/>',{class: 'galleryContent'}).html(
						$('<div/>',{class: 'stageWrapper'}).append(

							$('<div/>',{class: 'stageControls'}).append(
								$('<a/>',{class: 'closeTheater', title: "กดปุ่ม Esc เพื่อปิด"})
								// , $('<a/>',{class: 'fullScreenSwitchTheater'})
							)
							, $('<div/>',{class: 'stage'})
							, $('<a/>',{class: 'galleryPager prev', title: "ก่อนหน้านี้"}).html( $('<i/>', {class: 'icon-angle-left'}) )
							, $('<a/>',{class: 'galleryPager next', title: "ถัดไป"}).html( $('<i/>', {class: 'icon-angle-right'}) )
							, self.$stageActions
						)
					)
				)
			);
			
			$( 'body' ).append( self.$model );

			if( !$('html').hasClass('hasModel') ){

				$doc.css('top', $window.scrollTop()*-1 ).addClass('fixed_elem');
				$window.scrollTop(0);

				$('html').addClass('hasModel');
			}

			// Event
			self.$model.click(function () {
				
				if( self.is_open ){
					self.closeModel();
				}
				
			});
			
		}
	};

	$.fn.photoGallery = function( options ) {
		return this.each(function() {
			var gallery = Object.create( Gallery );
			gallery.init( options, this );
			$.data( this, 'photoGallery', gallery );
		});
	};

	$.fn.photoGallery.options = {
		onOpen: function(){},
		format: 'stage', // sequence,
		margin_width: 40,
		margin_height: 40
	};

})( jQuery, window, document );