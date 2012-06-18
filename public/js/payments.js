$(document).ready(function () {
	if ($('#payee-name-input').length == 0) {
		return false;
	}
	
	$.ajax({
		url : baseURL + '/payment/payees.json',
		dataType : 'json',
		success : function (r) {
			$('#payee-name-input').typeahead({
				source : r
			});
		}
	});
});