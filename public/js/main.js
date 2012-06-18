$(document).ready(function () {
	// input focus
	$("input[type=text],input[type=password],textarea").focus(function() {
		var inputElem = $(this);
		var defaultValue = inputElem.data('default');
		
		if (inputElem.val() == defaultValue) {
			inputElem.val('');
		}
	});
	
	// input blur
	$("input[type=text],input[type=password],textarea").blur(function() {
		var inputElem = $(this);
		var defaultValue = inputElem.data('default');
		
		if (inputElem.val() == '') {
			inputElem.val(defaultValue);
		}
	});
	
	// date picker
	$("#show-calendar").datepicker({
		showOn: "button",
		buttonImage: baseURL + "/images/calendar.png",
		buttonImageOnly: true
	});
	
	$('a.modal-iframe').click(function () {
		var width = 520;
		var height = 480;
		var r;
		if (r = this.className.match(/width([0-9]+)/)) {
			width = r[1];
		}
		;
		if (r = this.className.match(/height([0-9]+)/)) {
			height = r[1];
		}
		try {
			var el = $(this);
			
			var iframe = $('<iframe src="' + el.attr('href') + '" class="modal" style="width: ' + width + 'px; height: ' + height + 'px;"></iframe>');
			$(document.body).append(iframe);
			
			iframe.modal('show');
			iframe.on('hidden', function () {
				$(this).remove();
			});
		} catch (e) {
			
		}
		
		return false;
	});
});
