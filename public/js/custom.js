/**/
/* config */
var URL = window.location.origin + '/hotbet/';

var uiElem = {
	loader: function(){

		return $( '<div/>' )
			.addClass('loader')
			.append( $( '<div/>' )
				.addClass('inner')
				.append( $('<span/>').text( 'กำ' ) )
				.append( $('<span/>').text( 'ลั' ) )
				.append( $('<span/>').text( 'ง' ) )
				.append( $('<span/>').text( 'โ' ) )
				.append( $('<span/>').text( 'ห' ) )
				.append( $('<span/>').text( 'ล' ) )
				.append( $('<span/>').text( 'ด' ) )
				.append( $('<span/>').text( '.' ) )
				.append( $('<span/>').text( '.' ) )
				.append( $('<span/>').text( '.' ) )
			);
	},

	spinner: function () {
		return $( '<div/>', {class: 'sk-circle'} )
		.append( 
			$('<div/>', {class: 'sk-circle1 sk-child' })
			, $('<div/>', {class: 'sk-circle2 sk-child'})
			, $('<div/>', {class: 'sk-circle3 sk-child'})
			, $('<div/>', {class: 'sk-circle4 sk-child'})
			, $('<div/>', {class: 'sk-circle5 sk-child'})
			, $('<div/>', {class: 'sk-circle6 sk-child'})
			, $('<div/>', {class: 'sk-circle7 sk-child'})
			, $('<div/>', {class: 'sk-circle8 sk-child'})
			, $('<div/>', {class: 'sk-circle9 sk-child'})
			, $('<div/>', {class: 'sk-circle10 sk-child'})
			, $('<div/>', {class: 'sk-circle11 sk-child'})
			, $('<div/>', {class: 'sk-circle12 sk-child'})
		);
	}
}
var Calendar = {
	init: function( options ){
		var self = this;

		var defaults = {
			selectedDate: -1,
            startDate: -1,
            endDate: -1
		};

		self.options = $.extend( {}, defaults, options);

		var lang = Object.create( Datelang );
			lang.init( self.options.lang );
			self.string = lang;

		self.render();
	},

	// Render the calendar
	render: function(){
		var self = this;
		var settings = self.options;

		// Get the starting date
        var startDate = settings.startDate;
        if (settings.startDate == -1)
        {
            startDate = new Date();
            startDate.setDate(1);
        }
        startDate.setHours(0, 0, 0, 0);
        var startTime = startDate.getTime();

        // Get the end date
        var endDate = new Date(0);
        if (settings.endDate != -1)
        {
            endDate = new Date(settings.endDate);
            if ((/^\d+$/).test(settings.endDate))
            {
                endDate = new Date(startDate);
                endDate.setDate(endDate.getDate() + settings.endDate);
            }
        }
        endDate.setHours(0, 0, 0, 0);
        var endTime = endDate.getTime();

        // Get the current date to render
        var theDate = settings.theDate;
        theDate = (theDate == -1 || typeof theDate == "undefined") ? startDate : theDate;

        // Get the selected date
        var selectedDate = settings.selectedDate;
        selectedDate = (selectedDate == -1 || typeof selectedDate == "undefined") ? theDate : selectedDate;
        selectedDate.setHours(0, 0, 0, 0);
        var selectedTime = selectedDate.getTime();

        // Calculate the first and last date in month being rendered.
        // Also calculate the weekday to start rendering on
        firstDate = new Date(theDate);
        firstDate.setDate(1);
        var firstTime = firstDate.getTime();
        var lastDate = new Date(firstDate);
        lastDate.setMonth(lastDate.getMonth() + 1);
        lastDate.setDate(0);
        var lastTime = lastDate.getTime();
        var lastDay = lastDate.getDate();

        // Calculate the last day in previous month
        var prevDateLastDay = new Date(firstDate);
        prevDateLastDay.setDate(0);
        prevDateLastDay = prevDateLastDay.getDate();

        var today = new Date(); today.setHours(0, 0, 0, 0);
        var todayTime = today.getTime();

        // save Data
        self.options = $.extend( {}, {
            theDate: theDate,
            // firstDate: firstDate,
            // lastDate: lastDate,
        	startDate: startDate,
        	selectedDate: selectedDate
		}, self.options );

        // header
        self.header = [];
        // var $header = $('<tr class="header">');
        for (var i=0; i<7; i++) {
        	self.header.push({
        		text: self.string.day( i ),
        	});
        }

        // Render the cells as <TD>
        var lists = [];
	    for (var y = 0, i = 0; y < 6; y++){
	        var row = [], show=true;

	        for (var x = 0; x < 7; x++, i++) {
	            var p = ((prevDateLastDay - firstDate.getDay()) + i + 1);
	            var n = p - prevDateLastDay;
	            var sub = "";
	            var active = (x == 0) ? "sun" : ((x == 6) ? "sat" : "day");
	            var activeDate = new Date(theDate); activeDate.setHours(0, 0, 0, 0); activeDate.setDate(n);

	            // If value is outside of bounds its likely previous and next months
	            if (n >= 1 && n <= lastDay){

                    var activeTime = activeDate.getTime();
                    // Test to see if it's today

                    if(todayTime==activeTime){
                    	active +=" today";
                    }

                    if(selectedTime==activeTime){
                    	active +=" selected";
                    }
                    
	            } else {
	      			
	      			active = "noday"; // Prev/Next month dates are non-selectable by default
                    n = (n <= 0) ? p : ((p - lastDay) - prevDateLastDay);

	      			if (y > 0 && x == 0) show = false;
	            }

	            
	            row.push({
	            	text: n,
	            	date: activeDate,
	            	active: active
	            });

	        } // end for col

	        // Create the row
	         if (show && row){
	         	lists.push({
	            	data: row
	            });
	         }

	    } // end for row
	    self.lists = lists;
	 	
	}
}
var Datelang = {
	init: function( options ){
		var self = this;

		self.type = options.type || "short";
		self.lang = options.lang || "en";
	},

	display: function( theDate ){

		var fullYear = self.lang=='th'
			? theDate.getFullYear()-543
			: theDate.getFullYear();

		return this.day( theDate.getDay() ) +" "+ theDate.getDate() + " " + this.month( theDate.getMonth() ) +" "+ fullYear;
	},

	day: function( numbar, type, lang ){
		return this._day[type||this.type||'short'][lang||this.lang||'th'][numbar];
	},

	month: function( numbar, type, lang ){
		return this._month[type||this.type||'short'][lang||this.lang||'th'][numbar];
	},

	_day: {
		normal: {
			en: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
			th: ["วันอาทิตย์", "วันจันทร์", "วันอังคาร", "วันพุธ", "วันพฤหัสษบดี", "วันศุกร์", "วันเสาร์"]
		},
		short: {
			en: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
			th: ["อา.", "จ.", "อ.", "พ.", "พฤ.", "ศ.", "ส."]
		}
		
	},

	_month: {
		normal: {
			en: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],

			th: ["มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"]
		},
		short: {
			en: ["Jan.", "Feb.", "Mar.", "Apr.", "May", "Jun.", "Jul.", "Aug.", "Sep.", "Oct.", "Nov.", "Dec."],

			th: ["ม.ค.", "ก.พ.", "ม.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค."]
		}
		
	}
};
var uiLayer = {

	set: function (options, elem) {
		var self = this;

		// self.$elem = 
		self.$container = $(elem);
		self.$layer = $('<div/>', {class: 'uiContextualLayer'}).html(self.$container);
        self.$positioner = $('<div/>', {class: 'uiContextualLayerPositioner uiLayer'}).html(self.$layer);

        // var offset = self.$elem.offset();
        $( 'body' ).append( self.$positioner );

        // offset.top += self.$elem.outerHeight();
	},

	get: function( options, elem ){
		var self = this;

		self.$content = $(elem);
		self.$elem = $('<div/>', {class: 'uiContextualLayerPositioner uiLayer'});
		self.$layer = $('<div/>', {class: 'uiContextualLayer'}).html( self.$content );
		 
		self.options = options;

		self.is_open = false;
		if( typeof self.options.is_auto_position === 'undefined' ){
			self.options.is_auto_position = true;
		}
		self.$elem.html( self.$layer );

		self.initEvent();

		self.is_open = true;
		$( 'body' ).append( self.$elem );

		self.config();

		// search position
		if( self.options.is_auto_position ){
			self.searchPosition();
			$( window ).resize( function(){
				
				self.config();
				self.searchPosition();
				self.resize();
			} );
		}

		self.resize();
	},

	config: function(){
		var self = this;

		self.top = self.options.top;
		self.left = self.options.left;

		if( self.options.pointer ){
			self.$layer.addClass('uiToggleFlyoutPointer');

			self.top += 12;
			self.left -= 22;
		}
	},

	initEvent: function(){
		var self = this;

		/*$( document ).mousedown( function(){
			if( self.is_open ){
				self.$elem.remove();
			}
		});*/
	},

	resize: function(){
		var self = this;

		self.$elem.css({
			top: self.top,
			left: self.left
		});
	},
	searchPosition: function( ){
		var self = this;

		// set Width
		var maxWidth = $( window ).width(); // - (self.options.pointer? 22:0);
		var needWidth = ( self.left + self.$layer.outerWidth() );

		if( needWidth > maxWidth ){
			// overflow X
			self.$layer.addClass('uiToggleFlyoutRight');

			if(self.options.pointer){
				self.left += 44;
			}
		}else if(self.$layer.hasClass('uiToggleFlyoutRight')){
			self.$layer.removeClass('uiToggleFlyoutRight')
		}

		// set Height
		var maxHeight = $( window ).height();
		var needHeight = ( self.top + self.$content.height() );

		if( needHeight > maxHeight ){
			// overflow Y
			self.$layer.addClass('uiToggleFlyoutAbove');

			if(self.options.pointer){
				self.top -= 24;
			}
		}
		else if(self.$layer.hasClass('uiToggleFlyoutAbove')){
			self.$layer.removeClass('uiToggleFlyoutAbove')
		}
		
	}
};
var Event = {
	getCaret: function (el) {
		if (el.selectionStart) { 
	        return el.selectionStart; 
	    } else if (document.selection) { 
	        el.focus();
	        var r = document.selection.createRange(); 
	        if (r == null) { 
	            return 0;
	        }
	        var re = el.createTextRange(), rc = re.duplicate();
	        re.moveToBookmark(r.getBookmark());
	        rc.setEndPoint('EndToStart', re);
	        return rc.text.length;
	    }  
	    return 0; 
	},
	inlineSubmit: function( $form, formData, dataType ){

		var self = this;
		var dataType = dataType || 'json';

		var btnSubmit = $form.find('.btn.btn-submit');
		if( btnSubmit.hasClass('btn-error') ) btnSubmit.removeClass('btn-error');

		if( !formData ){
			var formData = new FormData();

			// set field
			$.each(self.formData($form), function (index, field) {
				formData.append(field.name, field.value);
	        });

	        // set file

	        $.each( $form.find('input[type=file]'), function (index, field) {
	        	
	        	var files = $(this)[0].files;

	        	if( files.length>0 ){
	        		formData.append(field.name, this.files[0]);
	        	}
	        });
		}

		if( $form.hasClass("loading") ) return false;
		btnSubmit.addClass('disabled');
		self.showMsg({ load: true });
		$form.find('input, textarea').attr('disabled', true);
		//  +'?__a=' + Math.random(),
		return $.ajax({
				type: "POST",
				url: $form.attr('action'),
				data: formData,
				dataType: dataType,
				processData: false, // Don't process the files
        		contentType: false, // Set content type to false as jQuery will tell the server its a query string request
			}).always(function() { 
				// complete
				self.hideMsg();
				btnSubmit.removeClass('disabled');
				$form.find('input, textarea').removeAttr('disabled', false);
			})
			.fail(function(  ) { 
				// error
				btnSubmit.removeClass('disabled');
				self.showMsg({ text: "เกิดข้อผิดพลาด...", load: true , auto: true });

			});
	},
	formData: function (form) {
        return form.serializeArray();
    },
	processForm: function( $form, result  ){
		var self = this;

		if( !result ) {
			self.showMsg({ text: "เกิดข้อผิดพลาด...", load: true , auto: true });
			return false;
		}
		if( result.form_reset ){
			$form.trigger('reset');
		}

		var $btnSubmit = $form.find('.btn-submit');
		if( $btnSubmit.hasClass('btn-error') ) $btnSubmit.removeClass('btn-error');

		if( $form.find('.control-group').hasClass('has-error') ) 
			$form.find('.control-group').removeClass('has-error');

		$form.find('.notification').empty();

		if(!result||result.error){

			$.each(result.error, function(field, msg){
				var $field = $('fieldset#'+field+"_fieldset"),
					$noity = $field.find('.notification');

				$field.addClass('has-error');
				$noity.html( msg );

				Event.plugins( $noity );
			});

			if( result.message ){
				
				if( typeof result.message === 'string' ) {
					Event.showMsg({ text: result.message, load: true , auto: true });
				}
				else{
					Event.showMsg( result.message );
				}
			}

			self.emptyForm( $form );

			$btnSubmit.addClass('btn-error');
			return false;
		}

		if( result.callback ){
			var callback = result.callback.split(",");
			$.each(callback, function (i, fun) {
				__Callback[fun](result);
			});
		}
		Dialog.close();

		if( result.link ){
			self.showMsg({ link: result.link, text: result.message, bg: 'yellow', sleep: result.link.sleep });
			return false;
		}

		if( result.url=="refresh" ){
			result.url = window.location.href;
		}

		if( result.message ){
			if( typeof result.message === 'string' ) {
				Event.showMsg({ text: result.message, load: true , auto: true });
			}
			else{
				Event.showMsg( result.message );
			}

			if( result.url ){

				setTimeout(function(){
					window.location = result.url;
				}, 2000);
			}
		}
		else if( result.url ){ window.location = result.url; }
	},
	emptyForm: function( $form ){

		var $fieldset = $form.find( 'fieldset.has-error' );
		var $btnSubmit = $form.find('.btn-submit');

		$fieldset.find(':input').blur(function(){

			if( $(this).val()!=""){
				$(this).parents( '.has-error' ).removeClass('has-error').find( '.notification' ).empty();
				if( $btnSubmit.hasClass('btn-error') ) $btnSubmit.removeClass('btn-error');
			}

		});

		$fieldset.find('select, [type=radio], [type=textbox]').change(function(){
			if( $(this).val()!=""){
				$(this).parents( '.has-error' ).removeClass('has-error').find( '.notification' ).empty();
				if( $btnSubmit.hasClass('btn-error') ) $btnSubmit.removeClass('btn-error');
			}
		});
	},
	showMsg: function( options ) {
		var self = this;
		
		var set = options || {};

		if( $('#alert-messages').length==0 ){

			var $dismiss = $('<span/>', {class: 'btn-icon icon-remove dismiss'});

			var el = $('<div/>', {class:"alert-messages", id:"alert-messages"})
				.html( $('<div/>', {class:"message"})
					.html( 
						$('<div/>', {class:"message-inside"}).append(
							$('<div/>', {class:"message-text"}),
							$dismiss
						)
					)
				);

			$('body').append(el);
		}
		else{
			var el = $('#alert-messages');
			el.removeAttr('class').addClass('alert-messages');
			var $dismiss = $('#alert-messages').find('.dismiss');
		}

		// reset
		if( set.load ){
			el.addClass("load");
			el.find('.message-text').html("กำลังโหลด...");
		}
		else el.removeClass("load");

		if( set.dismiss==false ){
			$dismiss.addClass('hidden_elem');
		}
		else if($dismiss.hasClass('hidden_elem')){
			$dismiss.removeClass('hidden_elem');
		}

		if( set.bg ){
			el.addClass( set.bg );
		}
		else{
			if( el.hasClass('yellow') ) el.removeClass('yellow');
		}

		if( set.text ){
			el.find('.message-text').html(set.text);
		}

		if( set.align ){
			el.addClass(set.align);
		}

		if( set.link ){
			el.find('.message-text').append( $('<a/>')
				.attr({
					href: set.link.url
				})
				.html( set.link.text )
			);
		}

		if ( set.auto || el.hasClass('auto') ) {
			setTimeout(function(){ self.hideMsg(); }, 3000);
		};

		if( set.sleep ){
			setTimeout(function(){ self.hideMsg(); }, set.sleep);
		}

		// event
		$dismiss.click(function(e){
			e.preventDefault();
			self.hideMsg(300);
		});

		el.stop(true,true).fadeIn(300);
	},

	hideMsg: function ( length ){

		$('#alert-messages').stop(true,true).fadeOut( length || 0, function () {
			
			$(this).remove();
		});
	},

	getPlugin: function ( name, url ) {
		var plugin_url = URL + 'public/js/plugins/';
		return $.getScript( url || plugin_url+name+".js" ); 
	},
	setPlugin: function ( $el, plugin, options, url ) {

		var self = this;
		if (typeof $.fn[plugin] !== 'undefined') {
			$el[plugin]( options );
		}
		else{
			self.getPlugin( plugin, url ).done(function () {
				$el[plugin]( options );
			}).fail(function () {
				console.log( 'Is not connect plugin:'+ plugin );
			});
		}
	},
	plugins: function ( $el ){
		var self = this;
		$elem = $el || $('html');

		$.each( $elem.find('[data-plugins]'), function(){

			var $this = $(this);

			var plugin = $this.attr('data-plugins'),
				options = {};

			$this.removeAttr('data-plugins');

			if( $this.attr('data-options') ){
				options = $.parseJSON( $this.attr('data-options') );

				$this.removeAttr('data-options');
			}
			
			// console.log(plugin);
			self.setPlugin( $this, plugin, options );
		});
	}
};

var __Callback = {
}

var Alert = {
	init: function ( data ) {
		var selt = this;
		selt.data = data;

		selt.url = selt.data[0];
		selt.load();
	},

	load: function () {
		var selt = this;

		Dialog.load( selt.url, {}, {

		});
	}
}

$(function () {

	Event.plugins();

	// submit
	$('body').delegate('form.js-submit-form','submit',function(e){
		var $form = $(this);
		e.preventDefault();

		Event.inlineSubmit( $form ).done(function( result ) {
			Event.processForm($form, result);
		});
		
	});
	
	var alert = [];
	$.each($('[data-alert]'), function () {
		alert.push( $(this).attr('data-load') );
		$(this).remove();
	});
	if( alert.length ){
		Alert.init( alert );
	}
});