window.rj = window.rj || {};

$(document).ready(function(){
	$('.preview-swatches').on('click','a', function (e) {
		e.preventDefault();
		var preview = $(this).closest('.preview-swatches').prev();
		var index = ($(e.currentTarget).index()) + 1;
		var previewLi = preview.find('li');
		previewLi.fadeOut(500).removeClass('current');
		previewLi.eq(index).addClass('current', 500).fadeIn(500);		
	})

	$('.configure-options .configure-button').on('click', function (e) {
		$(this).parents('.product-configure').find('.hide-group').show();
	});

	$('.configure-options .close').on('click', function (e) {
		e.preventDefault();
		$(this).parents('.product-configure').find('.hide-group').hide();
	});

	$('.configure-option input').on({
		focus: function() {
	    	$(this).siblings('menu').show(); }, 
		blur: function() {  }
	});

	$('.configure-option menu').on('click', function (e) {
		var option = $(e.target).closest('li');
		var input = option.closest('.configure-option').find('.configure-input');
		input.val(option.text());
		input.data('value', option.data('value'));
		$(this).hide();
		var configure = input.parents('.product-configure')
		var productcode = configure.data('productcode');
		var hasCoreOptions = productcode == 'HON' || productcode == 'TYC' || productcode == 'RHY'
		if (input.hasClass('core-value') && hasCoreOptions) {
			configure.find('.price').hide();
			configure.find('.price' + (option.parent().find('li').index(option) + 1)).show();
		}
	});

	$('.swatches.configure-input').on('click', 'a', function (e) {
		e.preventDefault();
		target = $(e.target).closest('a')
		swatches = target.siblings('a');
		if (target.hasClass('current')) {
			swatches.css( "display", "block");	
		}
		else {
			swatches.removeClass('current');
			target.addClass('current').after(target.siblings('.pointer'));
			swatches.not('.current').hide();
		}

	});
	
	cartTotal = 0;
	$('.buy, .add-to-cart').on('click', function (e) {
		e.preventDefault();
		var is_buy = $(this).hasClass('buy');
		var is_add = $(this).hasClass('add-to-cart');
		var button = $(this);
		button.text('Adding');
		var configure = $(this).parents('.product-configure');
		var productid = configure.data('productid');
		var productcode = configure.data('productcode');
		var data = {
			mode: 'add',
			productid: productid,
			cat: productcode == 'MAJ' || productcode == 'RHY' ? 246 : 245,
			page: 1,
			prod_notif_email: 'e-mail'	
		};
		configure.find('.configure-input').each(function () {
			var classid = $(this).data('classid');
			if ($(this).hasClass('swatches')) {
				var value = $(this).find('.current').data('value');
				if (value == '0') {
					alert ("You must choose a topsheet swatch");
					return false;
				}
			} 
			else {
				var value = $(this).data('value');				
			}
			if (value) {
				if ($(this).hasClass('quantity-value')) {
					data['amount'] = value || 1			
				}
				else {
					data['product_options[' + classid + ']'] = getOptionId({ productid: productid, productcode: productcode, classid: classid, option_name: value });
				}
			}
		});
		$.ajax({
			type: "POST",
			url: "/xcart/cart.php",
			data: data,
			success: function (resp) {
				cartTotal += data.amount;
				if (cartTotal) {
					$('#main .total').text(' (' + cartTotal + ')');
				}
				
				if (is_buy) {
					window.location = '/xcart/cart.php';
				}
				else {
					button.text('Add to Cart');
				}
			}
		})		
	});
	
	
	(function () {
		$('.product-configure').each(function () {
			$(this).find('.swatches').append($(this).parent().find('.products-swatches-pager').contents().clone());
		});
	})();

	(function () {
		$.getJSON( "/xcart/products_all.php?format=json", function(resp) {
		  window.rj.Cart = resp;
		});
	})();
	
	var getOptionId = function (hash) {
		for (var i=0, l=window.rj.Cart.length; i<l; i++) {
			var o = window.rj.Cart[i];
			if (
				hash.productid == o.productid && 
				hash.productcode == o.productcode && 
				hash.classid == o.classid && 
				hash.option_name == o.option_name
			) {
				return o.optionid;
			}
			
		}
	}

});
