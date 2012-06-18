$(document).ready(function () {
	$('[data-dismiss=modal]').click(function () {
		parent.$('iframe.modal').modal('hide');
		return false;
	});
});