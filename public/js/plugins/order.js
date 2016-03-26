// Utility
if ( typeof Object.create !== 'function' ) {
	Object.create = function( obj ) {
		function F() {};
		F.prototype = obj;
		return new F();
	};
}

(function( $, window, document, undefined ) {

	var Order = {
		init: function ( options, elem ) {
			var self = this;
			self.elem = elem;
			self.$elem = $( elem );

			self.$elem.find('[qty]').change(function (w) {
				self.change( $(this).parents('tr') );
			}).keydown(function () {
				
			});
		},

		change: function ( row ) {
			var self = this;
			var onhandmax = row.find('[onhand-max]').attr('onhand-max');

			var qty = row.find('[qty]').val();
			var price = row.find('[price]').val();

			var qtyc = onhandmax - qty;
			if( qtyc<0 ){

				qtyc = 0;
				row.find('[qty]').val( onhandmax );
				qty = onhandmax;
			}

			row.find('[onhand]').text( qtyc );
			row.find('[amount]').text( parseFloat(qty*price).toFixed(2) );

			self.total();
		},

		total: function () {
			var self = this;

			var total_amount = 0;
			self.$elem.find('tr').each(function () {
				if( $(this).find('[amount]').length ){
					total_amount += parseFloat($(this).find('[amount]').text());
				}
			});

			self.$elem.find('[total-amount]').text( total_amount.toFixed(2) );
		}	


	};

	$.fn.order = function( options ) {
		return this.each(function() {
			var $this = Object.create( Order );
			$this.init( options, this );
			$.data( this, 'order', $this );
		});
	};

	$.fn.order.options = {
		onOpen: function() {},
		onClose: function() {}
	};
	
})( jQuery, window, document );