window.rj = window.rj || {}
window.rj.Base = {} 

window.rj.Base.init = function () {
	jQuery.extend( jQuery.easing,
	{
        easeOutBack: function (x, t, b, c, d, s) {
                if (s == undefined) s = 0.70158;
                return c*((t=t/d-1)*t*((s+1)*t + s) + 1) + b;
        }
	});	
	
	function bindEvents() {
		var scrollTimer = 0;
		$('ul.nav a').add('.logo').on('click', function (e) {
			if ($(this).data('slug') == header.attr('class')) {
				if ($(this).data('slug') == 'products') {
					$('.products-body .bxslider').hide();
					$('.products-landing').fadeIn(500, function () {
						$('#products .bg').fadeOut(500).remove();		
						$('.products-body .skis, .products-body .snowboards').fadeOut(500);	
						$('#products-nav-skis').hide('slide', 1000);						
						$('#products-nav-snowboards').hide('slide', 1000);						

					});					
				}
				return;
			}
			e.preventDefault();
			var that = $(this);
			var hash = that.attr('href');
			var pageTop = $(hash).offset().top;
			logo.fadeOut(500);
			
			
			mainNav.animate({ top: '-=100px' }, 500, 'easeOutExpo', function () {
				header.attr('class', that.data('slug'));
			});
			
			$('body, html').animate(
				{ 
					scrollTop: pageTop 
				}, 
				{ 
					duration: 3000, 
					easing: 'easeOutBack', 
					complete: function () {	
						window.location.hash = hash;
						mainNav.css({ top: '-100px'}); 
						mainNav.show().animate({ top: '0' }, 100, 'easeOutCirc');
						logo.fadeIn(500); 					
					}
				}
			);	
		});

	
		mainNav.on('activate.bs.scrollspy', function (e) {
			if ($(e.target).find(':first-child').data('slug')) {
				header.attr('class', $(e.target).find(':first-child').data('slug'));			
			}
			else return false;
			
		});		
		mainNav.scrollspy()

		$(window).on('scroll', function(){ 
			if (header.attr('class') !== 'welcome') {
				mainNav.hide();					
			    scrollTimer = window.setTimeout(function() {
			    	mainNav.fadeIn(100);
					window.clearTimeout(scrollTimer);		
			    }, 1000);				
			}
		}); 			

		function productsSubnav(id) {
			$('#products').find('.bg').remove();					
			productNav.find('ul ul').hide();
			$('#products-nav-' + id).show('slide', 1000);
		
			$('.products-body .bxslider').hide();
			$('.products-landing').fadeOut(500, function () {
				$('.products-body').find('.' + id).fadeIn(500);	
				$('#products').prepend('<div class="bg">').addClass('detail');				
			});		
		}
		
		subnavSection.not('#news').not('#team').hide();
		subnav.on('click','a', function (e) {
			e.preventDefault();
			var page = $(this).parents('.page-body');
			
			if ($(this).attr('id') == 'skis' || $(this).attr('id') == 'snowboards') {
				productsSubnav($(this).attr('id'));	
			}
			
			else {
				$('.bxslider.load').removeClass('load');									
				var hash = $(this).attr('href');
				page.find('.subnav-section').hide();
				$(hash).fadeIn(500);				
			}
		});
		$('#products .bxslider').hide();
		
		$('.products-landing-image').on('click',function (e) {
			e.preventDefault();
			var id = $(e.currentTarget).data('link');
			productsSubnav(id);
		});
	}
	
	function setInitSection() {
		if (window.location.hash) {
			header.attr('class', window.location.hash.substring(1));	
		}
	}
	
	var logo = $('.logo');
	var mainNav = $('#main');
	var aboutNav = $('#about-nav');	
	
	var subnav = $('.subnav');	
	var productNav = $('#products-nav');
	var subnavSection = $('.subnav-section');
	var header = $('header');
	window.rj.currentContext = (function () {
		var viewportWidth = $(window).width();
		var contexts = {
			phone: [0, 319],
			phone_wide: [320, 479],
			desktop: [480, 999999999]
		}
		for (var context in contexts) {
			if (contexts.hasOwnProperty(context)) {
				var contextRange = contexts[context];
				if (viewportWidth >= contextRange[0] && viewportWidth <= contextRange[1]) {
					return context;
				}
			}
		}
	})();
	bindEvents();
	setInitSection();

}

$(document).ready(function (){
	window.rj.Base.init();
});