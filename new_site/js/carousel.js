$(document).ready(function(){
	var slider = $('.bulletin-body .bxslider, .artists-body .bxslider');
	slider.each(function () {
		$(this).children('li:first-child').addClass('current');
		if ($(this).closest('.artists-body').length) {
			$(this).children('.current').next().addClass('current');
		}
		$(this).prepend($(this).children('li:last-child'));
	});
	
	var slideHandler = function($slideElement, oldIndex, newIndex) { 
		$thisSlider = $slideElement.closest('.bxslider');
		$thisSlider.children('li').removeClass('current');
		$slideElement.next().addClass('current');
	}
	
	
	$('.bulletin-body .bxslider').bxSlider({
		pager: false,
	    slideWidth: 100,
	    minSlides: 2,
	    maxSlides: 3,
	    moveSlides: 1,
		onSlideAfter: slideHandler
	  });	

	$('.artists-body .bxslider').bxSlider({
		pager: false,
	    slideWidth: 100,
	    minSlides: 3,
	    maxSlides: 4,
	    moveSlides: 1,
		onSlideAfter: function($slideElement, oldIndex, newIndex) { 
			$thisSlider = $slideElement.closest('.bxslider');
			$thisSlider.find('li').removeClass('current');
			var next = $slideElement.next();
			next.add(next.next()).addClass('current');
		}
	  });

	$('.bxslider.skis').bxSlider({
		pagerCustom: '#products-nav-skis',		
		infiniteLoop: false,
	    slideWidth: 100,
	    minSlides: 1,
	    maxSlides: 1,
	    moveSlides: 1,
		hideControlOnEnd: true,
		onSlideAfter: slideHandler
	  });

	$('.bxslider.snowboards').bxSlider({
		pagerCustom: '#products-nav-snowboards',		
		infiniteLoop: false,
	    slideWidth: 100,
	    minSlides: 1,
	    maxSlides: 1,
	    moveSlides: 1,
		hideControlOnEnd: true,
		onSlideAfter: slideHandler
	  });
	
	$('.bxslider.snowboards').css('-webkit-transform', 'translate3d(-40px,0,0)');
		
		
	$('.bx-wrapper').on('swipeleft', function () {
		$(this).find('.bx-next').trigger('click');
	});

	$('.bx-wrapper').on('swiperight', function () {
		$(this).find('.bx-prev').trigger('click');
	});
	
});
