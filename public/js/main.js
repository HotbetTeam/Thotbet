$(function () {
	
	// latest products
	// $('#new-products').find()
	
	/**/
	/* products showcase */
	/**/
	$.each( $('#featured-products .item'), function () {
		$(this).find('#products-showcase>li').first().css('display', 'block').animate({opacity:1, left:'0%'});
		$(this).find('.dotnav>li').first().addClass('active');


		$(this).find('.dotnav').on('click', 'a', function()
		{
			var li = $(this).closest('li');
			
			// ol = $(this).closest('ol');
			// li = $(this).closest('li');
			// prev = li.prev().length ? li.prev() : ol.find('li:last-child');
			
			/*li.animate({opacity:0, left:'100%'}, function()
			{
				li.hide();
			});*/
			
			// prev.css('left', '-100%').css('display', 'block').animate({opacity:1, left:'0%'});
			
			return false;
		});
	});
	

	
});