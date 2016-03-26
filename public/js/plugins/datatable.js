// Utility
if ( typeof Object.create !== 'function' ) {
	Object.create = function( obj ) {
		function F() {};
		F.prototype = obj;
		return new F();
	};
}

(function( $, window, document, undefined ) {

	var Datatable = {
		init: function ( options, elem ) {
			var self = this;
			self.elem = elem;

			self.setElem();

			self.resize();
			$( window ).resize(function () {
				self.resize();
			});

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
			var fullh = outer.height() + outer.scrollTop();

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

			self.$tabletitle.css( 'padding-right', self.$tabletitle.width()-self.$tablelists.find('table').width() );

			var totalW = 0;
			if(self.$tablelists.find('table tr:first>td').hasClass('empty')){ return false; }
			self.$tablelists.find('table tr:first>td').each(function ( i ) {

				var td = $(this);
				var th = self.$tabletitle.find('table tr:first th').eq( i );

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
		},

		Events: function () {
			var self = this;

			$('input#checkboxes', self.$tabletitle).change( function (e) {
				e.preventDefault();
				self.selection($(this).is(':checked'), 'all');
			});

			$('input#toggle_checkbox', self.$tablelists).change( function (e) {
				e.preventDefault();
				self.selection($(this).is(':checked'), $(this).parents('tr') );
			});
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
		}
	};

	$.fn.datatable = function( options ) {
		return this.each(function() {
			var $this = Object.create( Datatable );
			$this.init( options, this );
			$.data( this, 'datatable', $this );
		});
	};

	$.fn.datatable.options = {
		onOpen: function() {},
		onClose: function() {}
	};
	
})( jQuery, window, document );