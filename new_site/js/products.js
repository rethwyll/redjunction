$(document).ready(function(){
	$('.preview-swatches').on('click','a', function (e) {
		e.preventDefault();
		var preview = $(this).closest('.preview-swatches').prev();
		var index = ($(e.currentTarget).index()) + 1;
		var previewLi = preview.find('li');
		previewLi.fadeOut(500).removeClass('current');
		previewLi.eq(index).addClass('current', 500).fadeIn(500);		
	})

});
