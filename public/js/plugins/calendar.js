// Utility
if ( typeof Object.create !== 'function' ) {
	Object.create = function( obj ) {
		function F() {};
		F.prototype = obj;
		return new F();
	};
}

(function( $, window, document, undefined ) {
	
	var Render = {
		init: function( options, elem ){
			var self = this;

			self.elem = elem;
			self.$elem = $( elem );

			self.options = $.extend( {}, $.fn.calendar.options, options );

			self.config();
			self.setElem();
			self.refresh();

			if ( typeof self.options.onComplete === 'function' ) {
				self.options.onComplete.apply( self, arguments );
			}
		},

		config:function () {
			var self = this;

			self.options.lang = {
				lang: self.options.lang,
				type: (self.options.summary==true)? "short":"normal"
			}

			if( !self.options.selectedDate ){
				self.options.selectedDate = new Date();
			}
			self.options.selectedDate.setHours(0, 0, 0, 0);

			self.options.theDate = new Date(self.options.selectedDate);
			self.options.theDate.setHours(0, 0, 0, 0);

			// set date
			self.date = {
				today: new Date(),
				theDate: self.options.theDate,
				selected: self.options.selectedDate
			};
			self.date.today.setHours(0, 0, 0, 0);

			var lang = Object.create( Datelang );
			lang.init( self.options.lang );
			self.string = lang;
		},
		setElem: function(){
			var self = this;

            var original = self.$elem, placeholder = $('<div class="calendarGridRoot"></div>');
            self.$elem.replaceWith(placeholder);
            self.$elem = placeholder;

            self.$wrapper = $('<div class="calendarWrapper"></div>');
            self.$root = $('<div class="calendarMonthRoot"></div>');

            self.$wrapper.append( self.$root ); // .append( self.$view )
            self.$elem.append( self.$wrapper );
		},
		refresh: function () {
			var self = this;

			self.update();
			self.display();
			self.initEvent();
			self.resize();
		},
		update: function() {
			var self = this;

			var settings = self.calculation( self.date.theDate );
			self.$calendar = self.Theme[ self.options.theme ]( settings, self.date, self.string );

			//
			self.$calendar.find('.calendarBoxHeaderRoot').addClass('clearfix').append( 
				$('<a/>',{class: 'prevnext prev'}).html( $('<i/>', {class:'icon-chevron-left'}) ),
				$('<a/>',{class: 'title'}).text( self.string.month( self.date.theDate.getMonth(), "normal" ) + " " + self.date.theDate.getFullYear() ),
				$('<a/>',{class: 'prevnext next rfloat'}).html( $('<i/>', {class:'icon-chevron-right'}) ),
				$('<a/>',{class: 'today rfloat'}).text( 'วันนี้' )
		    );
		},

		display:  function() {
			this.$root.html( this.$calendar );
		},
		initEvent: function () {
			var self = this;

			self.$calendar.find(".prevnext").click(function(e){

				var offset = $(this).hasClass("prev") ? -1 : 1;
				var newDate = new Date( self.date.theDate );

				newDate.setMonth( self.date.theDate.getMonth() + offset);
				
				self.date.theDate = newDate;
                self.refresh();
			});

			self.$calendar.find(".today").click(function(e){

				self.date.theDate = new Date();
				self.date.selected = new Date();
                self.refresh();

			});

		},
		resize: function () {
			var self = this;
			if( self.options.theme=='month' ){
				var fullWidth = self.$elem.parent().width();

				var width = fullWidth/self.$calendar.find('th.calendarGridCell').length;

				self.$calendar.find('.calendarGridItem,.calendarGridDayHeader').css('width', width-4);	
			}
		},
		calculation: function( date ){
			var self = this;
			var data = {};
			var theDate = date || self.date.today;

			var firstDate = new Date( theDate.getFullYear(), theDate.getMonth(), 1);
			firstDate = new Date(theDate);
	        firstDate.setDate(1);
	        var firstTime = firstDate.getTime();
			var lastDate = new Date(firstDate);
	        lastDate.setMonth(lastDate.getMonth() + 1);
	        lastDate.setDate(0);
	        var lastTime = lastDate.getTime();
	        var lastDay = lastDate.getDate();

			// Calculate the last day in previous month
	        var prevDateLast = new Date(firstDate);
	        prevDateLast.setDate(0);
	        var prevDateLastDay = prevDateLast.getDay();
	        var prevDateLastDate = prevDateLast.getDate();

	        var prevweekDay = self.options.weekDayStart;

	        if( prevweekDay>prevDateLastDay ){
	        	prevweekDay = 7-prevweekDay;
	        }
	        else{
	        	prevweekDay = prevDateLastDay-prevweekDay;
	        }

			data.lists = [];
			for (var y = 0, i = 0; y < 7; y++){

				var row = [];
				var weekInMonth = false;

				for (var x = 0; x < 7; x++, i++) {
					var p = ((prevDateLastDate - prevweekDay ) + i);

					var call = {};
					var n = p - prevDateLastDate;
					call.date = new Date( theDate ); 
					call.date.setHours(0, 0, 0, 0); 
					call.date.setDate( n );

					// If value is outside of bounds its likely previous and next months
	            	if (n >= 1 && n <= lastDay){
	            		weekInMonth = true;

	            		if( self.date.today.getTime()==call.date.getTime()){
	                    	call.today = true;
	                    }

	                    if( self.date.selected.getTime()==call.date.getTime() ){
	                    	call.selected = true;
	                    }
	            	}
	            	else{
	            		call.noday = true;
	            	}
					row.push(call);
				}

				if( row.length>0 && weekInMonth ) data.lists.push(row);
			}

			data.header = [];
			for (var x=0,i=self.options.weekDayStart; x<7; x++, i++) {
				if( i==7 ) i=0;
				data.header.push({
	        		key: i,
	        		text: self.string.day( i, 'normal' )
	        	});
			};

			self.date.first = firstDate;
			self.date.end = lastDate;
			return data;
		},
		Theme: {
			month: function ( settings, options, string ) {
				var $tbody = $('<tbody/>');
				// header
				var $header = $("<tr/>");
				$.each( settings.header, function(i, obj){

					$text = $('<div>').addClass('calendarGridDayHeader').html( $('<h2>').addClass('pas fsm fwn fcg').text( obj.text ) ); // 

					$header.append( $('<th>').append( $text ) );
				});
				$tbody.append( $header );

				// lists days
				$.each( settings.lists, function(y, row){
					$tr = $('<tr>');
					$.each(row, function(x, call){

						$fade = $('<div class="calendarFade"></div>');

						var monthStr = call.date.getMonth()+1;
						monthStr = monthStr<10? '0'+monthStr:monthStr;
						var dateStr = call.date.getDate();
						dateStr = dateStr<10? '0'+dateStr:dateStr;

						// options.end = call.date.getFullYear()+"-"+monthStr+"-"+dateStr;

						if( x==0 && y==0 ){
							// self.date.start = self.date.end;
						}
						
						$td = $('<td>')
							.append( $('<div>', { 
									class: 'calendarGridItem', 
									'data-date': dateStr,
									'data-month': monthStr,
									'data-year': call.date.getFullYear(),
									// 'data-row': 0,
									// 'data-col': 0
								})
								.append( $('<a>', {class: 'calendarItemDay calendarItemDayLink'})
									.html( $('<span>', {class: 'mrs calendarItemDayNumber'})
										.text( call.date.getDate() ) 
									)
								)
							);

						if( call.noday ){
							$td .addClass('calendarGridCellEmpty')
								.find('.calendarItemDayNumber').addClass('fsm fwn fcg');

							if( call.date.getDate()==1 || (y==0&&x==0)){
								$td .find('.calendarItemDayNumber')
									.text( call.date.getDate() +' '+ string.month( call.date.getMonth(),"normal" ) );
							}
						} //self.
						else{
							$td.find('.calendarItemDayNumber').addClass('fwb fcb');
						}

						$td .addClass( call.today?'today':"" )
							.addClass( call.date.getDay()==6 || call.date.getDay()==0?'weekHoliday':'' );

						$tr.append( $td );
					});

					$tbody.append( $tr );
				});

				// ser addClass
				$tbody.find('tr').addClass('calendarGridRow');
				$tbody.find('th,td').addClass('calendarGridCell calendarGridCellLeftBorder');
				$tbody.find('th').addClass('calendarGridCellSubtitle');
				$tbody.find('tr').find("td:first,th:first").removeClass('calendarGridCellLeftBorder');
				
				$tbody.find('td.today').removeClass('today').addClass('calendarGridToday').parent().addClass('calendarGridWeek');

				var $table = $('<table/>', {
					class: 'calendarGridTable',
					cellspacing:0,
					cellpadding:0

				});
				$table.html( $tbody );

				var $calendar = $('<div/>')
					.addClass('calendarBox calendarGrid')
					.append( 
						// $('<div/>',{class: 'calendarBoxHeaderRoot'}),
						$('<div/>',{class: 'calendarBoxContentRoot'}).html( $table )
					);

				return $calendar;
			}
		}
	}

	$.fn.calendar = function( options ) {
		return this.each(function() {
			var calendar = Object.create( Render );
			calendar.init( options, this );
			$.data( this, 'calendar', calendar );
		});
	};
	
	$.fn.calendar.options = {
		// string
		lang: "th",
		theme: 'month', 
		summary: true,

		$header: null,

		// date
		weekDayStart: 1,
		theDate: null,
		selectedDate: null,

		// resize
		resize: true,
		bordertop: 1,
		borderleft: 1,

		onComplete: function () {}
	};
	
})( jQuery, window, document );