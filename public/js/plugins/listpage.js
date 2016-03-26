// Utility
if ( typeof Object.create !== 'function' ) {
	Object.create = function( obj ) {
		function F() {};
		F.prototype = obj;
		return new F();
	};
}

(function( $, window, document, undefined ) {

	var List = {
		init: function ( options, elem ) {
			var self = this;
			self.elem = elem;

			self.setElem();
			self.data = $.extend( {}, $.fn.listpage.options, options );

			self.resize();
			$( window ).resize(function () {
				self.resize();
			});
			self.$elem.addClass('on');

			self.ids = [];
			self.Events();

		},
		setElem: function () {
			var self = this;
			self.$elem = $(self.elem);
			self.$elem.find('[ref]').each(function () {
				if( $(this).attr('ref') ){
					var ref = "$" + $(this).attr('ref');
					self[ref] = $(this);
				}
				
			});
		},
		resize: function () {
			var self = this;

			var outer = $( window ); // $( window );
			var offset = self.$elem.offset();
			var right = 0;
			var fullw = outer.width()- (offset.left+right);
			var fullh = outer.height(); // + outer.scrollTop();

			var headerH = 0;
			if( self.$header ){
				headerH = self.$header.outerHeight();

				self.$header.css({
					top: offset.top,
					left: offset.left,
					width: fullw,
					// right: right,
					position: 'fixed'
				});
			}

			self.$table.css({
				paddingTop: headerH,
				width: fullw,
				height: fullh - (offset.top),
				overflow:'hidden'
			});

			self.$tabletitle.css({
				position: 'fixed',
				left: offset.left,
				right: right,
				zIndex: 20
			});
			
			self.$tablelists.css({
				marginTop: self.$tabletitle.outerHeight(),
				height: fullh - (offset.top+headerH+self.$tabletitle.outerHeight()),
				overflowY: 'auto'
			});

			var totalW = 0;
			if(self.$tablelists.find('table tr:first>td').hasClass('empty')){ return false; }
				self.$tablelists.find('table tr:first>td').each(function ( i ) {

				var td = $(this);
				var th = self.$tabletitle.find('table th[data-col='+i+']');

				if( td.hasClass('name') ){
					return
				}

				var outerW = td.outerWidth()
				var width = td.width();

				if( th.width() > width){
					outerW = th.outerWidth();
					width = th.width();
				}
				
				totalW+=outerW;
				td.width( width );
				th.width( width );
			});

			if( self.$table.hasClass('has-empty') ){
				self.$tabletitle.css( 'padding-right', 0 );

				self.$table.find('.listpage-table-empty').css( 'padding-top', self.$tabletitle.outerHeight()+headerH );
			}
			else{
				self.$tabletitle.css( 'padding-right', self.$tabletitle.outerWidth() - self.$tablelists.find('table').outerWidth() );
			}
			// -parseInt(self.$tabletitle.css('padding-right'))
		},

		Events: function () {
			var self = this;

			self.$elem.mouseover(function () {
				self.resize();
			});

			$('input#checkboxes', self.$tabletitle).change( function (e) {
				e.preventDefault();
				self.selection($(this).is(':checked'), 'all');
			});

			$('input#toggle_checkbox', self.$tablelists).change( function (e) {
				e.preventDefault();
				self.selection($(this).is(':checked'), $(this).parents('tr') );
			});

			$('.navigation-trigger').click( function (e) {
				// setTimeout(function () {
					
					self.resize();
				// }, 230);
			});

			self.$elem.find('.js-refresh').click(function (e) {

				self.refresh( 200 );
				e.preventDefault();
			});

			self.$elem.find('#more-link').delegate('a.next, a.prev', 'click', function (e) {
				
				pager = parseInt( self.data.options.pager );
				pager += $(this).hasClass('prev')? -1 : 1;

				self.data.options.pager = pager;
				self.refresh( 1 );
				e.preventDefault();
			});
			
			$('a.link-sort', self.$tabletitle).click( function (e) {

				var val = $(this).attr('data-sort') || 'asc';

				val = val=='asc' ? 'desc' : 'asc';
				$(this).attr('data-sort', val);

				// set data
				self.data.options.sort_field = $(this).attr('data-sort-val');
				self.data.options.sort = val;
				self.refresh( 1 );

				// set elem
				self.$tabletitle.find( '.sorttable.asc' ).removeClass('asc');
				self.$tabletitle.find( '.sorttable.desc' ).removeClass('desc');
				$(this).parent().addClass( val );

				e.preventDefault();
			});

			if( self.$elem.find('[ref=rangeSelector]').length ){
				self.setDate.init( self, self.$elem.find('[ref=rangeSelector]'), function (start_date, end_date) {

					var startDate = "";
					if( start_date ){
						startDateMonth = start_date.getMonth()+1;
						startDateMonth = startDateMonth<10 ? "0"+startDateMonth: startDateMonth;

						startDateDate = start_date.getDate();
						startDateDate = startDateDate<10 ? "0"+startDateDate: startDateDate;

						startDate = start_date.getFullYear() + "-" + startDateMonth + '-' + startDateDate;
					}
					self.data.options.start_date = startDate;

					endDateMonth = end_date.getMonth()+1;
					endDateMonth = endDateMonth<10 ? "0"+endDateMonth: endDateMonth;

					endDateDate = end_date.getDate();
					endDateDate = endDateDate<10 ? "0"+endDateDate: endDateDate;
 						
					self.data.options.end_date = end_date.getFullYear() + "-" + endDateMonth+ '-' + endDateDate;
					self.refresh( 1 );
				} );
			}
			else{
				self.refresh( 1 );
			}

			/**/
			/* search */
			self.$elem.find('.search-input').keydown(function(e){
				var text = $.trim( $(this).val() );

				if(e.keyCode == 13 && text!='') {
					self.search( text );
				}

			}).keyup(function(e){
				var text = $.trim( $(this).val() );

				if( text=='' && text!= self.data.options.q ){
					self.search( text );
				}
			});
			self.$elem.find('.form-search').submit(function (e) {
				var text = $.trim( $(this).find('.search-input').val() );


				if( text!='' ){
					self.search( text );
				}
				
				e.preventDefault();
			});

			if( self.$header ){

				self.$header.find('select[ref=selector]').change(function () {

					self.data.options[ $(this).attr('name') ] = $(this).val();

					self.refresh( 1 );
				});
			}
			
			
		},
		search: function (text) {
			var self = this;

			self.data.options.pager = 1;
			self.data.options[ 'q' ] = text;
			self.is_search = true;
			self.refresh( 500 );
		},

		setDate: {
			init: function (settings, $elem, call) {
				var self = this;
				self.$elem = $elem;

				self.today = new Date();
				self.today.setHours(0, 0, 0, 0);
				self.call = call;

				self.$select = self.$elem.find('select[name=range_selector]');
				self.selected( self.$select.val() );
				self.$select.change(function () {
					self.selected( $(this).val() );
				});
			},
			selected: function ( value ) {
				var self = this;

				self.endDate = new Date();
				self.endDate.setHours(0, 0, 0, 0);

				self.startDate = new Date( self.endDate );
				self.startDate.setHours(0, 0, 0, 0);

				if( value=="last30days" ){
					self.startDate.setDate( self.startDate.getDate()-30 );
				}
				else if( value=="last7days" ){
					self.startDate.setDate( self.startDate.getDate()-7 );
				}
				else if( value=="last24hours" ){
					self.startDate.setDate( self.startDate.getDate() );
				}
				else if( value=='custom' ){
					return false;
				}
				// year
				else{

					if( self.$sMonth ) self.$sMonth.remove();
					if( self.$sDay ) self.$sDay.remove();
					
					self.openMonth();
					self.setTime();

					return false;
				}

				if( self.$sMonth ) self.$sMonth.remove();
				if( self.$sDay ) self.$sDay.remove();

				self.setCalendar();
				self.active();
			},
			openMonth: function  () {
				var self = this;

				self.$sMonth = $('<select>', {name: "month"});
				var aMonth = ["มกราคม", "กุมภาาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฏาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"];

				self.$sMonth.append( $('<option>', {
					value: 'all',
					text: "ทุกเดือน"
				}) );

				// var today

				for (var i = 12; i >= 1; i--) {

					var d = new Date( parseInt( self.$select.val() ), (i-1), 0 );
					if( d < self.today){

						self.$sMonth.append( $('<option>', {
							value: i,
							text: aMonth[(i-1)]
						}) );
					}
				};

				self.$select.after( self.$sMonth );
				
				self.$sMonth.change(function () {

					if( self.$sDay ){
						self.$sDay.remove();
					}
					self.setTime();
					if( $(this).val()=='all' ) return false;
					self.openDay( $(this).val()-1 );
				});
			},
			openDay: function ( month ) {
				var self = this;

				self.$sDay = $('<select>', {name: "day"});

				self.$sDay.append( $('<option>', {
					value: 'all',
					text: "ทุกวัน"
				}) );

				var d = new Date(self.$select.val(), month + 1, 0);

				for (var i = d.getDate(); i >= 1; i--) {

					var d = new Date( parseInt( self.$select.val() ), parseInt( self.$sMonth.val() )-1, i );

					if( d <= self.today ){
						self.$sDay.append( $('<option>', {
							value: i,
							text: i
						}) );
					}
				};

				self.$sMonth.after( self.$sDay );

				self.$sDay.change(function () {

					self.setTime();
				});
			},
			setTime: function () {
				var self = this;

				var date = 1, month = self.$sMonth.val()=='all' ? 0: parseInt( self.$sMonth.val() )-1;

				if( self.$sDay ){
					date = self.$sDay.val()=='all' ? 1: parseInt( self.$sDay.val() );
				}

				self.startDate = new Date( self.$select.val(), month, date );
				self.startDate.setHours(0, 0, 0, 0);
					

				// end
				if( self.$sMonth.val()=='all' ) month = 11;

				if( self.$sDay ){
					if( self.$sDay.val()=='all' ){

						date = 0;
					}
					else{
						month--;
					}
				}
				else{
					date = 0;
				}
				

				self.endDate = new Date( self.$select.val(), month + 1, date );
				self.startDate.setHours(0, 0, 0, 0);

				self.setCalendar();
				self.active();
			},

			setCalendar: function () {
				var self = this;

				// 
				self.setCalendarStart();

				self.setCalendarEnd();		
			},
			setCalendarStart: function () {
				var self = this;

				$startDate = $('<input/>',{ type:"text", name:"start_date" });
				self.$elem.find('.date-start').html( $startDate );
				$startDate.datepicker({
					format: 'range start',
					selectedDate: self.startDate,
					start: self.startDate,
					end: self.endDate,
					onSelected: function( ){

						var fdata = $(this).data('datepicker');
						if( fdata ){
							self.$select.find('option[value=custom]' ).prop("selected", true);

							self.startDate = new Date( fdata.options.start );
							self.setCalendarEnd();
							self.active();

							if( self.$sMonth ) self.$sMonth.remove();
							if( self.$sDay ) self.$sDay.remove();
						}
					}
				});
			},
			setCalendarEnd: function () {
				var self = this;

				$endDate = $('<input/>',{ type:"text", name:"end_date" });
				self.$elem.find('.date-end').html( $endDate );
				$endDate.datepicker({
					format: 'range end',
					selectedDate: self.endDate,
					start: self.startDate,
					end: self.endDate,
					onSelected: function( fdata ){
						var fdata = $(this).data('datepicker');
						if( fdata ){
							self.$select.find('option[value=custom]' ).prop("selected", true);
							
							self.endDate = new Date( fdata.options.end );
							self.setCalendarStart();
							self.active();

							if( self.$sMonth ) self.$sMonth.remove();
							if( self.$sDay ) self.$sDay.remove();
						}
					}
				});
			},
			active: function () {
				var self = this;

				if( typeof self.call === 'function' ){
					self.call( self.startDate, self.endDate );
				}
			}
		},

		setSelectedDate: function () {
			var self = this;
		},

		selection: function (checked, item) {
			var self = this;

			if( item == 'all' ){
				$.each(self.$tablelists.find('tr'), function (i, obj) {
					var item = $(this);

					if(checked==true && !item.hasClass('has-checked')){
						self.selectItem(item);
					}
					else if(checked==false && item.hasClass('has-checked')){
						self.cancelItem(item);
					}
				});
			}
			else{
				if(checked){
					self.selectItem(item);
				}
				else{
					self.cancelItem(item);
				}
			}
		},
		selectItem: function (el) {
			var self = this;
			var toggle_checkbox = el.find('input#toggle_checkbox');
			var id = el.attr('data-id');
			toggle_checkbox.prop('checked', true);
			el.addClass('has-checked');

			self.ids.push( parseInt(id) );
			self.active();
		},
		cancelItem: function (el) {
			var self = this;

			var toggle_checkbox = el.find('input#toggle_checkbox');
			var id = el.attr('data-id');
			toggle_checkbox.prop('checked', false);
			el.removeClass('has-checked');

			self.ids.splice(self.ids.length-1, parseInt(id));
			self.active();
		},
		active: function () {
			var self = this;

			if(self.ids.length > 0){
				self.$actions.addClass('hidden_elem');
				self.$selection.removeClass('hidden_elem').find('.count-value').text('เลือกแล้ว '+ self.ids.length + ' รายการ');
			}
			else{

				self.$selection.addClass('hidden_elem').find('.count-value').text("");
				self.$tabletitle.find('input#checkboxes').prop('checked', false);
				self.$actions.removeClass('hidden_elem');
			}

			self.resize();
		},

		refresh: function ( length ) {
			var self = this;

			// url = 
			// console.log( JSON.stringify(self.data.options) );
			// console.log( $.param(self.data.options) );

			if( self.is_loading ) clearTimeout( self.is_loading ); 
			self.$elem.addClass('has-loading');

			// console.log( 'loading...', length );
			self.is_loading = setTimeout(function () {
				self.fetch().done(function( results ) {

					self.data = $.extend( {}, self.data, results.settings );

					// console.log( results.settings.total );
					self.$tablelists.parent().toggleClass( 'has-empty', parseInt(self.data.total)==0 ? true: false );
					
					self.setMore();

					if( results.selector ){
						self.setSelector( results.selector );
					}
					// self.buildFrag();
					// console.log( self.data.options.sql );
					self.display( results.body );
				});
			}, length || 1);
		},
		fetch: function() {
			var self = this;

			// var qLoad = setTimeout( function () {
			
			// set url
			var returnLocation = history.location || document.location,
				href = self.data.url+"?"+$.param(self.data.options),
				title = "";
			// history.pushState('', title, href);
			// document.title = title;

			return $.ajax({
				url: self.data.url,
				data: self.data.options,
				dataType: 'json'
			}).always(function () {

				self.$elem.removeClass('has-loading');
				// console.log( 'always...' );
				/*clearTimeout( qLoad );
				if( self.$elem.hasClass('has-loading') ){}*/
				
			}).fail(function() { 
				self.$elem.addClass('has-error');
			});
		},
		buildFrag: function( results ) {
			var self = this;

			self.tweets = $.map( results, function( obj, i) {
				return $( self.options.wrapEachWith ).append ( obj.text )[0];
			});
		},
		display: function( item ) {
			var self = this;

			$item = $( item );
			self.$tablelists.html( item );
			self.resize();

			Event.plugins( self.$tablelists );
			// console.log( $item  );
			/*if ( self.options.transition === 'none' || !self.options.transition ) {
				self.$elem.html( self.tweets ); // that's available??
			} else {
				self.$elem[ self.options.transition ]( 500, function() {
					$(this).html( self.tweets )[ self.options.transition ]( 500 );
				});
			}*/
		},

		setMore: function () {
			var self = this;

			var options = self.data.options;
			var total = self.data['total'],
				pager = options['pager'],
				limit = options['limit'];

			self.$elem.find('#more-link').empty();
			if( total==0 ) return false;
			
			var length = parseInt(total/limit); // floor( );

			if(total%limit){
				length++;
			}

			var first = (limit*pager)-limit+1,
				last = limit*pager;

			if( last>total ){
				last = total;
			}

			self.$elem.find('#more-link').append(
				  $('<span>', {class: 'mhs'}).append(
					  first
					, '-'
					, last
					, $('<span>', {class: 'mhs', text: 'จาก'})
					, total
				)

				, pager > 1 
					? $('<a>', {class: 'prev'}).html( $('<i>', {class: 'icon-angle-left'}) )
					: $('<span>', {class: 'prev disabled fcg'}).html( $('<i>', {class: 'icon-angle-left'}) )

				, pager==length
					? $('<span>', {class: 'next disabled fcg'}).html( $('<i>', {class: 'icon-angle-right'}) )
					: $('<a>', {class: 'next'}).html( $('<i>', {class: 'icon-angle-right'}) )
			);
		},

		setSelector: function ( results ) {
			var self = this;

			$.each(results, function (key, lists) {
				
				$item = self.$elem.find('select[ref=selector][name='+key+']');
				
				if( $item ){
					$item.empty();

					$.each(lists, function (i, val) {
						$item.append( $('<option>', {
								'text': val.text + ( val.total>0? ' ('+val.total+')':'' ) ,
								'value': i,
								'selected': val.current
							})
						);
					});
					
				}
			});
		}
	};

	$.fn.listpage = function( options ) {
		return this.each(function() {
			var $this = Object.create( List );
			$this.init( options, this );
			$.data( this, 'listpage', $this );
		});
	};

	$.fn.listpage.options = {
		/*onOpen: function() {},
		onClose: function() {}*/
	};
	
})( jQuery, window, document );