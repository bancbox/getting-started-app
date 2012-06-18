var GENERIC_ERROR_MSG = "Internal error. Try again later.";
/* String.prototype */

String.prototype.changeExtension = function(to)
{
	return this.replace(/^(.*?)(\.([a-z0-9-_]{1,12}))?(\?|$)/, '$1.' + to + '$4');
};

var Forms = {
	init: function() {
		$('form.ajax').bind('submit', function() {
			var form = this;
			
			var params = {};
			var inputs = $(this).find(':input, textarea, select');
			for (var i = 0; i < inputs.length; i++) {
				var input = $(inputs[i]);
				if (input.attr('name')
				&& (!input.attr('data-default')
				|| (input.attr('data-default') && input.val() != input.attr('data-default'))
				)) {
					if (input.attr('type') == 'radio' || input.attr('type') == 'checkbox') {
						if (input.attr('checked')) {
							params[input.attr('name')] = input.val();
						}
					} else {
						params[input.attr('name')] = input.val();
					}
				}
			}
			
			$.ajax({
				url: this.action.changeExtension('json'),
				type: 'POST',
				data: params,
				complete: function(request) {
					if(request.readyState != 4) {
						return;
					}
					try {
						var response = $.parseJSON(request.responseText);
						Forms.respond(form, response);
					} catch (e) { }
				}
			});
			return false;
		});
		$('form.ajax').each(function() {
			this.className = this.className.replace(/(^|\s)ajax($|\s)/g, '');
		});
		$('a.ajax').bind('click', function() {
			var a = this;
			
			if ($(a).attr('data-confirm')) {
				if (!confirm($(a).attr('data-confirm'))) {
					return false;
				}
			}
			
			if (a.className.indexOf('wait') !== -1) {
				$(a).parent().mask('wait');
			}
			
			$.ajax({
				url: this.href.changeExtension('json'),
				complete: function(request) {
					if(request.readyState != 4) {
						return;
					}
					if (a.className.indexOf('wait') !== -1) {
						$(a).parent().unmask();
					}
					try {
						var response = $.parseJSON(request.responseText);
						Forms.respond(a, response);
					} catch (e) { }
				}
			});
			return false;
		});
		$('a.ajax').each(function() {
			this.className = this.className.replace(/(^|\s)ajax($|\s)/g, ' ');
		});
	},
	respond: function(el, response) {
		if(response.code == 200)
		{
			var action = response.action.split('-');
			for (var i = 0; i < action.length; i++) {
				switch (action[i]) {
					case 'popup':
						window.open(response.target);
						break;
					case 'redirect':
						window.location = response.target;
						break;
					case 'reload':
						window.location.reload();
						break;
					case 'reload_parent':
						parent.location.reload();
						break;
					case 'close':
					//	parent.$.fancybox.close();
						parent.$('iframe.modal').modal('hide');
						break;
					case 'reset':
						el.reset();
						break;
					case 'hide':
						$(el).hide();
						break;
					case 'append':
						$(response.append_selector).append(response.content);
						break;
					case 'prepend':
						$(response.append_selector).prepend(response.content);
						break;
					case 'replace':
						if (response.replace_selector) {
							$(response.replace_selector).replaceWith(response.content);
						} else {
							$(el).replaceWith(response.content);
						}
						break;
					case 'fill':
						if (response.fill_selector) {
							$(response.fill_selector).html(response.content);
						} else {
							$(el).html(response.content);
						}
						break;
					case 'custom_script':
						try {
							eval(response.script);
						} catch (e) { }
						break;
					case 'increment':
						$(response.increment_selector).html(parseInt($(response.increment_selector).html()) + 1);
						break;
					case 'notice':
					default:
						var callback = undefined;
						if (i < action.length - 1) {
							var r = response;
							action.splice(0, i + 1);
							r.action = action.join('-');
							callback = function () {
								Forms.respond(el, r);
							};
							i = action.length;
						}
						new alertBox({
							title : response.title ? response.title : 'Notice',
							content : response.message,
							width: 400,
							onClose : callback
						});
				}
			}
		}
		else
		{
			if(el.className.indexOf('registration-form') !== -1)
			{
				var errorText = "";
				var errors = response.errors;
				for(var i in errors)
				{
					switch(errors[i])
					{
						case 'email':
							errorText += "- email is invalid or already exists in our database<br />";
							break;
						default:
							errorText += "- " + errors[i] + "<br />";
					}
				}
				new alertBox({
					title : 'Invalid fields:<br /><br />',
					content : errorText
				});
			} else {
				if (!response.action) {
					response.action = 'notice';
				}
				var action = response.action.split('-');
				for (var i = 0; i < action.length; i++) {
					switch (action[i]) {
						case 'popup':
							window.open(response.target);
							break;
						case 'redirect':
							window.location = response.target;
							break;
						case 'close':
							parent.$.fancybox.close();
							break;
						case 'notice':
						default:
							var callback = undefined;
							if (i < action.length - 1) {
								var r = response;
								action.splice(0, i + 1);
								r.action = action.join('-');
								callback = function () {
									Forms.respond(el, r);
								};
								i = action.length;
							}
							
							if (response.message) {
								new alertBox({
									title : response.title ? response.title : 'Error',
									content : response.message,
									width: 400,
									onClose : callback
								});
							} else {
								var errorText = "";
								if ($.isArray(response.errors)) {
									for(var i in response.errors)
									{
										errorText += "- " + response.errors[i] + "<br />";
									}
								} else {
									errorText += response.errors;
								}
								new alertBox({
									title : response.title ? response.title : 'Error',
									content : errorText,
									width: 400,
									onClose : callback
								});
							}
					}
				}
			}
			return;
		}
	}
};

$(document).ready(function() {
	Forms.init();
});