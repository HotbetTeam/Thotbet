// Utility
if ( typeof Object.create !== 'function' ) {
	Object.create = function( obj ) {
		function F() {};
		F.prototype = obj;
		return new F();
	};
}

(function( $, window, document, undefined ) {

	var uploadAlbum = {

		init: function( options, elem ){
			var self = this;

			self.elem = elem
			self.$elem = $(elem);
			self.options = $.extend( {}, $.fn.uploadPhoto.options, options );

			self.config();
			// if( !self.url ) return false;

			self.initEvent();
		},
		config: function () {
			var self = this;

			self.$doc = $('#doc');
			if( self.$elem.attr('href') ){
				self.url = self.$elem.attr('href');
				self.$elem.removeAttr('href');
			}
			
			self.files = [];
			self.count = 0;
			
		},
		initEvent: function () {
			var self = this;

			self.$elem.click(function () {

				self.setModel();
				self.load(1);

				if( self.options.activitie == 'upload' ){
					self.change();
				}
				else if( self.options.activitie == 'view' ){
					self.getModel();
				}

			});
		},
		change: function () {
			var self = this;

			var $input = $('<input/>', { type: 'file', accept: "image/*", multiple:''});
			$input.trigger('click');

			$input.change(function(){

				self.buildFrag( this.files, true );
				self.getModel();
			});
		},

		buildFrag: function ( results ) {
			var self = this;
			
			$.each(results, function (i, file) {
				var $item = self.editablePhoto();
				file.$elem = $item;
				self.display( $item, true);
				self.saveFile( file );
			});
		},
		display: function ( item, hasUpdate ) {
			var self = this;
			// self.getFiles();

			if( !hasUpdate || self.$listblox.find('.uiEditablePhoto').length==0 ){
				self.$listblox.append( item );
			}
			else{
				self.$listblox.find('.uiEditablePhoto').first().before( item );
			}
		},
		getFiles: function(){
			var self = this;

			$.each(self.files, function (i, file) {
				if( file.variable ){
					file.variable = false;
					self.saveFile( file );
					// self.loadFile( file );
					return false;
				}
			});
		},

		saveFile: function ( file ) {
			var self = this;

			var $progress = file.$elem.find('.progress-bar');
			var $img = file.$elem.find('.scaledImage');

			var formData = new FormData();
			formData.append('file1', file);

			$.ajax({
				xhr: function(){
			    	var xhr = new window.XMLHttpRequest();
				    //Upload progress
				    xhr.upload.addEventListener("progress", function(evt){
				      if (evt.lengthComputable) {
				        // var percentComplete = evt.loaded / evt.total;
				        var progress = parseInt( ((evt.loaded / evt.total) * 100), 10 );
				        //Do something with upload progress
				        $progress.find('.bar').width( progress+"%" );
				      }
				    }, false);
				    //Download progress
				    /*xhr.addEventListener("progress", function(evt){
				      if (evt.lengthComputable) {
				        var percentComplete = evt.loaded / evt.total;
				        //Do something with download progress
				        console.log(percentComplete);
				      }
				    }, false);*/
			    	return xhr;
			  	},
                url : self.options.upload_url,
                type : "POST",
                data : formData,
                dataType: 'json',
                processData : false,
                contentType : false
            }).done(function ( response ) {
            	// console.log( response );

            	if( response.error ){
            		file.$elem.removeClass('has-loading').addClass('has-error');
            		file.$elem.find('.textCaption').attr('disabled', true);
            		file.$elem.find('.empty-message').text( response.error_message );
            		return false;
            	}

            	self.loadImageUrl(file.$elem, response.url, response.photo_id);
            	// self.displayImage( file.$elem, response, true );
            	
            }).always(function() {
            	self.getFiles();
			}).fail(function(  ) { 
			});
		},
		loadFile: function ( file ) {
			var self = this;

			var $progress = file.$elem.find('.progress-bar');
			var $img = file.$elem.find('.scaledImage');

			file.$elem.find('.js-cancel').click(function(e){
				
				file.$elem.remove();
				delete self.files[file.id]; 
				// console.log( self.files );
			});

			var reader = new FileReader();
			reader.onload = function (e) {
				var image = new Image();
				image.src = e.target.result;
				$image = $('<img/>', {class: 'img', src: e.target.result});
				$img.html( $image );
				file.$elem.removeClass('has-loading');

				image.onload = function() {
					var $img = this;

					var h = file.$elem.find('.photoWrap').height();
					var w = file.$elem.find('.photoWrap').width();
					var scaled = self.resizeImage( {
						width: w, height: h
					}, { 
						width: $img.width, height: $img.height
					} );

					// var fitHeight = ( typeof scaled.fitHeight !== "undefined" )? true: false;
					var fitHeight = true;
					if( scaled.width>scaled.height ){
						fitHeight = false;
					}

					file.$elem.addClass('has-file').css({
						// width: scaled.width
					}).find('.photoWrap').addClass( fitHeight? '':'fitWidth').css({
						width: w,
						height: h,
						lineHeight: h+"px"
					}).find('.scaledImage').css({ 
						width: scaled.width,
						height: scaled.height
					}).find('.img').addClass( 
						fitHeight? "scaledImageFitHeight":'scaledImageFitWidth' 
					);

					self.getFiles();
					/*self.scaledAreaImg();
					self.verifyElImg();*/
				}
			}

			reader.onprogress = function(data) {
				if (data.lengthComputable) {                                            
	                var progress = parseInt( ((data.loaded / data.total) * 100), 10 );
	                $progress.find('.bar').width( progress+"%" );
	            }
        	}

			reader.readAsDataURL( file );
		},
		resizeImage: function ( fig, org ) {
			if( org.width == org.height ) return fig;
			else if (org.width > org.height) {

 				return {
					width: (fig.width).toFixed(),
					height: ((org.height * fig.height) / org.width).toFixed(),
					org: org,
					fitHeight: true
				}

 			}else{
				return {
					width: ((org.width * fig.width) / org.height).toFixed(),
					height: (fig.height).toFixed(),
					org: org,
					fitHeight: false
				}

			}
		},

		editablePhoto: function(){
			var self = this;
			return $('<div/>', {class: 'uiEditablePhoto has-loading'})
				.append( 
					$('<div/>', {class: 'photoWrap'}).append( 
						self.editablePhotoProgress(),
						self.editablePhotoError(),
						$('<div/>', {class: 'scaledImageContainer scaledImage'})
					),
					self.editablePhotoCaption(), 
					self.editablePhotoControls()
				);
		},
		editablePhotoError: function () {
			return $('<div/>').addClass('empty-error')
				.append( $('<span/>')
					.addClass('empty-title') 
					.append( $('<span/>') )
				)
				.append( $('<div/>')
					.addClass('empty-message')
				);
		},
		editablePhotoProgress: function(){
			var self = this;

			return $('<div/>').addClass('progress-bar medium')
				.append( $('<span/>')
					.addClass('bar blue') 
					.append( $('<span/>') )
				)
				.append( $('<div/>')
					.addClass('text') 
					.append( uiElem.loader() )
				);
		},
		editablePhotoCaption: function(){

			return $('<div/>', {class: 'inputs'})
				.append( $('<div/>')
					.addClass('captionArea')
					.append( $('<div/>')
						.addClass('uiTypeahead') 
						.append( $('<textarea/>')
							.addClass('uiTextareaNoResize textInput textCaption') 
							.attr({
								title: "เขียนคำบรรยายรูปภาพ...",
								name: 'caption_text',
								placeholder: "เขียนคำบรรยายรูปภาพ"
							})
							
						)
						
					)
				
				);
		},
		editablePhotoControls: function(){

			return $('<div/>').addClass('controls').append( 
				/*$('<a/>', {class: 'control'}).html( 
					$('<i/>', {class: 'icon-refresh'})
				)
				, $('<a/>', {class: 'control js-cancel'}).html(
					$('<i/>', {class: 'icon-trash', title: 'ลบออก'})
				)*/
				$('<a/>', {class: 'control checked'}).html(
					$('<i/>', {class: 'icon-check', title: 'เลือก'})
				)
			);
		},

		setModel: function () {
			var self = this;

			self.$model = $('<div/>', {class: 'model effect-2 model-upload-image black'});
			self.$modelContainer =  $('<div/>', {class: 'model-container'});
			self.$modelContent =  $('<div/>', {class: 'model-content'});

			self.$model.html( self.$modelContainer.html( self.$modelContent ) );

			self.$header = $('<div/>', {class: 'upload-image-header'});
			self.$title = $('<h1/>', {class: 'title lfloat mlm', text: self.options.title});
			self.$add = $('<a/>', {class: 'btn', text: 'อัปโหลดรูป'});
			self.$submit = $('<a/>', {class: 'btn btn-blue', text: 'บันทึกการเปลี่ยนแปลง'});
			self.$cancel = $('<a/>', {class: 'btn-icon'}).html( '<i class="icon-remove"></i>' );
			self.$btn = $('<a/>', {class: 'btn btn-blue', text: 'บันทึกการเปลี่ยนแปลง'});
			self.$header.append( self.$title, $('<div/>', {class: 'actions rfloat mvm'}).append( self.$add,'<span class="fcg"> | </span>', /*self.$submit,*/ self.$cancel ) );

			self.$body = $('<div/>', {class: 'upload-image-body'});
			self.$listblox = $('<div/>', {class: 'uiEditablePhotoContainer clearfix'});

			if( self.options.caption ){
				self.$listblox.addClass('hasCaption');
			}
			
			// Event
			self.$add.click(function () {
				self.change();
			});

			self.$cancel.click(function () {
				self.delModel();
			});

			self.$selected = $('<div/>', {class: 'upload-image-selected clearfix'});
			self.$del = $('<a/>', {class: 'btn-icon'}).html( '<i class="icon-trash"></i>' );
			self.$selectCancel = $('<a/>', {class: 'btn-icon'}).html( '<i class="icon-remove"></i>' );
			self.$selectCountValue = $('<span>', {class: 'selectCountValue'});
			self.$selected.append( 
				$('<div/>', {class: 'lfloat'}).append(
					self.$selectCancel,
					'<div class="divider"></div>',
					self.$selectCountValue
				),
				$('<div/>', {class: 'rfloat'}).append(
					self.$del 
				)
			);

			self.$selectCancel.click(function () {
				self.files = [];
				self.$listblox.find('.has-checked').removeClass('has-checked');
				self.verifyElImg();
			});
			self.$del.click(function () {
				if( self.files.length==0 ) return;

				Dialog.load(self.options.remove_url, { ids: self.files}, {
					onClose: function () {},
					onSubmit: function ( dialog ) {
						$form = dialog.$pop.find('form');

						Event.inlineSubmit( $form ).done(function( result ) {
							Event.processForm($form, result);

							for (var i = 0; i < self.files.length; i++) {
								self.$listblox.find( '[data-id=' + self.files[i] + ']' ).remove();
							};

							self.files = [];
							self.verifyElImg();
						});
						
					}
				} );
			});

			self.$modelContent.append( self.$selected, self.$header, self.$body.append( self.$listblox ) );
		},
		getModel: function () {
			var self = this;

			/*if( !self.$model ){
				self.setModel();

			}else if(self.$model.hasClass('hidden_elem')){
				self.$model.removeClass('hidden_elem')
			}*/
			
			if( !self.$model.hasClass('show') ){

				if( !$( 'html' ).hasClass('hasModel') ){
					setTimeout(function () {
						$( 'html' ).addClass('hasModel');
					},200);
					// 
					self.$doc.addClass('fixed_elem').css('top', $(window).scrollTop()*-1 );
					
				}
				$(window).scrollTop( 0 );

				$( 'body' ).append( self.$model );
				self.$model.addClass('show').addClass('active').removeClass('effect-2');
			}
			
			self.resizeModel();
			$( window ).resize(function () {
				self.resizeModel();
			});
		},
		delModel:function ( length ) {
			var self = this;

			self.$model.removeClass("show").removeClass('active');

			setTimeout( function(){
				self.$model.remove();
				self.$model = null;
				self.data = null;
				// console.log( self.$model );
				// self.$model.addClass('hidden_elem');

				if( $('body').find('.model').not('.hidden_elem').length == 0 ){
					$('html').removeClass('hasModel');

					$("#doc").removeClass('fixed_elem').css('top', "");
					$(window).scrollTop( scroll );

					/*if($('body').hasClass('overflow-page')){
						$('body').removeClass('overflow-page');
					}*/
				}

			}, length || 100);
		},
		resizeModel: function () {
			var self = this;

			var outer = $(window);

			self.$modelContainer.css({
				width: outer.width(),
				height: outer.height()
			});

			self.$body.height( outer.height() );
			self.$header.width( self.$listblox.width() );

			self.$title.width( self.$listblox.width()-self.$add.parent().outerWidth()-50 )
		},

		load: function( length ){
			var self = this;

			setTimeout(function() {

				if( !self.data ){
					self.data = {};
				}
				else{
					if( self.data.more == false ) return;

					self.data.pager++;
				}

				self.fetch().done(function( results ) {

					self.data = $.extend( {}, self.data, results.options );
					self.buildFragImage( results.lists );
					
				});
			}, length );
		},
		fetch: function() {
			var self = this;
			return $.ajax({
				url: self.options.load_url,
				data: self.data,
				dataType: 'json'
			});
		},
		buildFragImage: function ( results ) {
			var self = this;

			$.each(results, function ( i, file ) {
				
				var $item = self.editablePhoto();
				self.displayImage( $item,  file);
			});
		},
		displayImage: function ( item, data, hasUpdate ) {
			var self = this;

			self.display( item, hasUpdate );
			item.find('.textCaption').val( data.caption_text );

			self.loadImageUrl(item, data.url, data.photo_id);	
		},
		loadImageUrl: function ( item, url, id ) {
			var self = this;

			var image = new Image();
			image.onload = function () {
			   	var img = this;
			    var h = item.find('.photoWrap').height() || 290;
				var w = item.find('.photoWrap').width() || 290;
				var scaled = self.resizeImage( {
					width: w, height: h
				}, { 
					width: img.width, height: img.height
				} );

				var fitHeight = scaled.width>scaled.height? false: true;
				item.find('.scaledImage').html( img );
				$( img ).addClass( fitHeight? "scaledImageFitHeight":'scaledImageFitWidth' );
				
				item
				.removeClass('has-loading')
				.addClass('has-file')
				.find('.photoWrap').addClass( fitHeight? '':'fitWidth').css({
					width: w,
					height: h,
					lineHeight: h+"px"
				}).find('.scaledImage').css({ 
					width: scaled.width,
					height: scaled.height
				});
			}
			
			image.onerror = function(e){

				item.find('.empty-message').text('ไม่สามารถดูรูปภาพได้หรือไฟล์รูปภาพถูกลบไปแล้ว');
				item.find('.textCaption').attr('disabled', true);
				item.removeClass('has-loading').addClass('has-error');
			};
			/*image.onloadprogress = function(e) { 
				var progress = e.loaded / e.total;
				// console.log( progress );
			};*/
			image.src = url;

			// remove
			item.attr('data-id', id);

			item.find('.control.checked').click(function() {
				
				// var id = id;
				var is = item.hasClass('has-checked');
				item.toggleClass('has-checked', !is);

				if( !is ){
					self.files.splice( self.files.length, 1, parseInt(id));
				}
				else{

					for (var i = 0; i < self.files.length; i++) {
						if( self.files[i]==parseInt(id) ){
							self.files.splice( i, 1 );
							break;
						}

					};
				}

				self.verifyElImg();
			});

			item.find('.textCaption').blur(function () {
				var $this = $(this);
				if( $this.val()==$this.defaultValue ) return;
				
				$.post( self.options.update_url, {photo_id: id, caption_text:$this.val()}, function () {
					$this.defaultValue = $this.val();
				});
			});
		},

		verifyElImg:function () {
			var self = this;

			if(self.files.length==0){
				self.$model.removeClass('has-selected');
				self.$selectCountValue.empty();
			}
			else{
				self.$model.addClass('has-selected');
				self.$selectCountValue.text( 'เลือก ' + self.files.length + ' รูป' );
			}
		}
	};

	$.fn.uploadPhoto = function( options ) {
		return this.each(function() {
			var upload = Object.create( uploadAlbum );
			upload.init( options, this );
			$.data( this, 'uploadPhoto', upload );
		});
	};

	$.fn.uploadPhoto.options = {
		title: "",
		activitie: 'upload',
		caption: true
	};

})( jQuery, window, document );