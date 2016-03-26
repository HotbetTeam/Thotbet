// Utility
if ( typeof Object.create !== 'function' ) {
	Object.create = function( obj ) {
		function F() {};
		F.prototype = obj;
		return new F();
	};
}

(function( $, window, document, undefined ) {

	var Tasks = {
		init: function ( options, elem ) {
			var self = this;
			self.elem = elem;

			self.options = $.extend( {}, $.fn.tasks.options, options );
			self.setElem();

			self.resize();
			$( window ).resize(function () {
				self.resize();
			});

			self.tasks = [];

			self.setup();
			
			self.event();
		},
		setElem: function () {
			var self = this;
			self.$elem = $(self.elem);

			self.$elem.find('[role]').each(function () {
				if( $(this).attr('role') ){
					var role = "$" + $(this).attr('role');
					self[role] = $(this);
				}
				
			});
		},
		resize: function () {
			var self = this;
		},
		setup: function () {
			var self = this;

			self.selectDate = self.options.selectDate 
				? new Date( self.options.selectDate )
				: new Date();

			self.endDate = new Date();

			self.startDate = self.options.startDate 
				? new Date( self.options.startDate )
				: new Date();

			// set

			self.selectYear = self.selectDate.getFullYear();
			self.selectMonth = self.selectDate.getMonth();

			for (var i = self.selectYear; i >= self.startDate.getFullYear(); i--) {
				self.$year.append( $('<option/>', {
					value: i,
					text: (i+543)
				}) );
			};

			self.$year.selectbox({
				onComplete: function () {
					self.$year = this.$elem;
				},
				onSelected: function () {
					if( self.selectYear!=this.selected.value ){
						self.selectYear = parseInt(this.selected.value);
						self.setOptionsMonth();
						self.changeYear = true;
					}
					else{
						self.changeYear = false;
					}
				}
			});
			
			self.setOptionsMonth();
			self.updateCalendar();
		},

		setOptionsMonth: function () {
			var self = this;

			var placeholder = $('<select/>');
			
			self.$month.replaceWith(placeholder);
			self.$month = placeholder;

			for (var i = 11; i >= 0; i--) {
				if( self.selectYear==self.endDate.getFullYear()&&i>self.endDate.getMonth() || self.selectYear==self.startDate.getFullYear()&&i<self.startDate.getMonth()){
					continue;
				}

				self.$month.append( $('<option/>', {

					value: i,
					text: Datelang.month( i, 'normal' )
				}) );
			};

			self.$month.selectbox({
				onComplete: function () {
					self.$month = this.$elem;
				},
				onSelected: function () {
					if( self.selectMonth!=this.selected.value || self.changeYear){
						self.selectMonth = parseInt(this.selected.value);
						self.updateCalendar();
					}
				}
			});
		},

		updateCalendar: function () {
			var self = this;

			var theDate = new Date();
			theDate.setYear(self.selectYear);
			theDate.setMonth(self.selectMonth);

			if( !self.calendar ){

				Event.setPlugin( self.$calendar, 'calendar', {
					theDate: theDate,
					onComplete: function () {
						self.calendar = this;
						self.$calendar = this.$elem;
						self.midifyCalendar();

						self.loadTasks( theDate );
					}
				} );

			}
			else{
				self.calendar.date.theDate = theDate;
				self.calendar.refresh();
				self.midifyCalendar();
				self.loadTasks( theDate );
			}

			self.theDate = theDate;
		},

		midifyCalendar: function () {
			var self = this;

			self.$calendar.find('.calendarGridCell').not('.calendarGridCellEmpty').find('.calendarGridItem').each(function () {
				var date = parseInt( $(this).attr('data-date') );
				var month = parseInt( $(this).attr('data-month') );

				month = month<10 ? "0"+month:month;

				var f = parseInt( $(this).attr('data-year') ) + '-' +month+ '-' + $(this).attr('data-date');
				$(this).parent().attr('data-date', f );

				$(this).append( $('<ul/>', {class: 'clearfix calendar-list-tasks'}).append(

					$('<li/>').append(
						$('<label/>', {class: 'checkbox'}).append(
							$('<input/>', {
								id: 'shift-morning',
								'data-date': f,
								name: 'shift[morning]['+date+']',
								type: 'checkbox'
							}),
							$('<span/>', {class: 'mls', text: '06.00-18.00 น.'})
						) 
					),
					$('<li/>').append(
						$('<label/>', {class: 'checkbox'}).append(
							$('<input/>', {
								id: 'shift-night',
								'data-date': f,
								name: 'shift[night]['+date+']',
								type: 'checkbox'
							}),
							$('<span/>', {class: 'mls', text: '18.00-06.00 น.'})
						) 
					),
					$('<li/>').append(
						$('<span/>', {class: 'mrs', text: 'จำนวน OT'}),
						$('<label/>', {class: 'checkbox'}).append(
							$('<input/>', {
								id: 'shift-OT',
								'data-date': f,
								name: 'shift[OT]['+date+']',
								type: 'number'
							})
						) 
					)
				),  $('<ul/>', {class: 'clearfix list-on-tasks'}).append(

					$('<li/>', {class: 'shift-morning hidden_elem', text: '06.00-18.00 น.'}),
					$('<li/>', {class: 'shift-night hidden_elem', text: '18.00-06.00 น.'}),
					$('<li/>', {class: 'shift-OT hidden_elem'})

				) );
				
			});
			// calendarGridItem
		},

		loadTasks: function (date) {
			var self = this;

			$.get(self.options.load_tasks_url, {
				year: date.getFullYear(),
				month: date.getMonth()+1
			}, function ( results ) {

				self.tasks = results;
				self.setTasks();
				
			}, 'json');

		},
		setTasks: function (results) {
			var self = this;

			$.each( self.tasks, function (i, obj) {
				
				var item = self.$calendar.find('.calendarGridCell[data-date='+ obj.task_date +']');
				
				if( item.length ){
					var is_on_task = false;

					if( parseInt(obj.morning_shift )==1){
						is_on_task = true;

						item.find('.list-on-tasks .shift-morning').removeClass('hidden_elem');
					
						self.$calendar.find('input#shift-morning[data-date='+ obj.task_date +']').prop( "checked", true );
					}
					else{
						item.find('.list-on-tasks .shift-morning').addClass('hidden_elem');
						
						self.$calendar.find('input#shift-morning[data-date='+ obj.task_date +']').prop( "checked", false );
					}


					if( parseInt(obj.night_shift)==1 ){
						is_on_task = true;

						item.find('.list-on-tasks .shift-night').removeClass('hidden_elem');

						self.$calendar.find('input#shift-night[data-date='+ obj.task_date +']').prop( "checked", true );
					}
					else{
						
						item.find('.list-on-tasks .shift-night').addClass('hidden_elem');

						self.$calendar.find('input#shift-night[data-date='+ obj.task_date +']').prop( "checked", false );
					}
					
					if( parseInt(obj.task_OT_hour)>0 ){
						is_on_task = true;

						item.find('.list-on-tasks .shift-OT').text('OT '+obj.task_OT_hour+" ชม.").removeClass('hidden_elem');

						self.$calendar.find('input#shift-OT[data-date='+ obj.task_date +']').val( obj.task_OT_hour );
					}
					else{
						item.find('.list-on-tasks .shift-OT').addClass('hidden_elem');

						self.$calendar.find('input#shift-OT[data-date='+ obj.task_date +']').val( "" );
					}

					item.toggleClass('on-task', is_on_task);
				}
			} );
		},
		
		event: function () {
			var self = this;

			self.$schedule.click(function (e) {
				e.preventDefault();

				self.$ControlTitle.text('ปี '+self.theDate.getFullYear()+' เดือน '+self.theDate.getMonth()+":" )

				self.$toolbarControls.addClass('on');
				self.$main.addClass('has-event-tasks');
			});
			
			self.$toggleSchedule.click(function (e) {
				e.preventDefault();
				
				self.$toolbarControls.removeClass('on');
				self.$main.removeClass('has-event-tasks');

				self.setTasks(); 
			});

			self.$addTasks.click(function (e) {
				e.preventDefault();
				
				self.addTasks( $(this) );
			});


			self.$elem.find('input#checkboxes').change(function () {
				
				self.$calendar.find('input#'+$(this).attr('name')).prop( "checked", $(this).is( ":checked" ) );
			});

			self.$Salary_Calculator.click(function (e) {
				e.preventDefault();

				var url = $(this).attr('ajaxify');
				Dialog.load( url, {
					year: self.theDate.getFullYear(),
					month: self.theDate.getMonth()+1
				});
			});

		},

		addTasks: function ( btn ) {
			var  self = this;

			btn.addClass('disabled');
			var formData = new FormData();


			formData.append( 'year', self.theDate.getFullYear() );
			formData.append( 'month', self.theDate.getMonth()+1 );

			self.$calendar.find(':input').each(function (index, field) {
				
				var value = field.value;
				if( $(field).attr("type")=='checkbox' ){
					value = $(field).is( ":checked" ) ? 1:0;
				}

				formData.append( field.name, value);

			});

			Event.showMsg({ load: true });

			$.ajax({
				type: "POST",
				url: self.options.add_tasks_url,
				data: formData,
				dataType: 'json',
				processData: false, // Don't process the files
        		contentType: false, // Set content type to false as jQuery will tell the server its a query string request
			}).done(function(results){

				self.$toolbarControls.removeClass('on');
				self.$main.removeClass('has-event-tasks');

				Event.showMsg({ text: "บันทึกเรียบร้อย", load: true , auto: true });

				self.tasks = results;
				self.setTasks(); 
			}).always(function() { 
				// complete
				btn.removeClass('disabled');
				// Event.hideMsg();
			}).fail(function(  ) { 
				// error

				Event.showMsg({ text: "เกิดข้อผิดพลาด...", load: true , auto: true });
			});
		}

	};

	$.fn.tasks = function( options ) {
		return this.each(function() {
			var $this = Object.create( Tasks );
			$this.init( options, this );
			$.data( this, 'tasks', $this );
		});
	};

	$.fn.tasks.options = {
		onOpen: function() {},

		selectDate: null,
		startDate: null,
	};
	
})( jQuery, window, document );