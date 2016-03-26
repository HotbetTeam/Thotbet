$(function () {

	Page.init();
});

var $window = $( window ),
	$html = $('html'),
	$body = $('body'),
	
	Page = {
		init: function () {
			var self = this;


			// self.resize();
			self.config();			
			$( window ).resize(function () {
				self.resize();
			});


			self.event();
		},

		config: function () {
			var self = this;

			self.fullWidth = $( window ).width();
			// $('body').toggleClass('is-pushed-left', self.fullWidth > 979 ? true: false);
			self.isPushedLeft = $('body').hasClass('is-pushed-left');
		},
		

		resize: function () {
			var self = this;

			self.fullWidth = $( window ).width();

			if( self.fullWidth < 979 && $('body').hasClass('is-pushed-left') ){
				$('body').removeClass('is-pushed-left');
			}
			else{
				$('body').toggleClass('is-pushed-left', self.isPushedLeft || false);
			}

			
		},

		event: function () {
			var self = this;

			$('.navigation-trigger').click(function(e){
				e.preventDefault();

				self.isPushedLeft = !$('body').hasClass('is-pushed-left');
				$('body').toggleClass('is-pushed-left', self.isPushedLeft);


				$.get( URL + 'manage/navTrigger', {
					'status': self.isPushedLeft ? 1:0
				});
			});
		}
	}