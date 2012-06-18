$(document).ready(function () {
	// input focus
	$("input[type=text],input[type=password").focus(function() {
		var inputElem = $(this);
		var defaultValue = inputElem.data('default');
		
		if (inputElem.val() == defaultValue) {
			inputElem.val('');
		}
	});
	
	// input blur
	$("input[type=text],input[type=password").blur(function() {
		var inputElem = $(this);
		var defaultValue = inputElem.data('default');
		
		if (inputElem.val() == '') {
			inputElem.val(defaultValue);
		}
	});
	
	$('a.modal-iframe').click(function () {
		try {
			var el = $(this);
		
		var iframe = $('<iframe src="' + el.attr('href') + '" class="modal" style="width: 600px; height: 450px;"></iframe>');
			$(document.body).append(iframe);
			
			iframe.modal('show');
		} catch (e) {
			console.log(e.message);
			console.log(e);
		}
		
		return false;
	});
	
	// scroll to users
	$('#admin-users').on('click', function() {
		$('html, body').animate({
			scrollTop: $('#user-list').offset().top
		}, 800);
		
		return false;
	});
	
	// Admin list evaluation tooltip
	$('.evaluation-wrap').hover(function() {
		var thisElem = $(this);
		var tooltip = $('.evaluation-tooltip');
		
		thisElem.find(tooltip).stop().fadeIn();
	}, function() {
		var thisElem = $(this);
		var tooltip = $('.evaluation-tooltip');
		
		thisElem.find(tooltip).stop().fadeOut();
	});
});
