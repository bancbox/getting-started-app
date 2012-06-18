function alertBox(args) {
	if (typeof args == 'string') {
		args = {
			content : args
		};
	}
	
	this.init(args);
	this.show();
};
alertBox.prototype = {
	container : null,
	content : null,
	title : '',
	top : null,
	success : function () {},
	failure : function () {},
	onClose : function () {},
	init : function (args) {
		args = args || {};
		args.type = args.type || 1;
		args.title = args.title || (args.type == 3 ? 'Error' : 'Notification');
		args.content = args.content || (args.type == 3 ? GENERIC_ERROR_MSG : '');
		args.width = args.width || null;
		args.top = args.top || 170;
		args.success = args.success || function () {};
		args.failure = args.failure || function () {};
		args.onClose = args.onClose || function () {};
		
		this.success = args.success;
		this.failure = args.failure;
		this.onClose = args.onClose;
		this.top = args.top;
		
		this.create(args);
	},
	create : function (args) {
		args = args || {};
		args.title = args.title || '';
		args.content = args.content || '';
		args.width = args.width || null;
		args.cssClass = args.cssClass || null;
		args.type = args.type || 1;
		
		args.texts = args.texts || {};
		args.texts.close = args.texts.close || 'Close';
		
		this.title = args.title;
		this.container = $(document.createElement('div'));
		
		this.container.addClass('modal');
		this.container.addClass('hide');
		if (args.cssClass) {
			this.container.addClass(args.cssClass);
		}
		if (args.width) {
			this.container.css('width', args.width + 'px');
		}
		
		this.container.append('<div class="modal-header"><button class="close" data-dismiss="modal">close</button></div>');
		
		var me = this;
		
		this.content = $(document.createElement('div'));
		this.content.addClass('modal-body');
		this.content.append('<h2>' + this.title + '</h2>');
		if (typeof args.content  == 'object' && $(args.content)) {
			this.content.append(args.content);
		} else {
			this.content.append('<div class="modal-text">' + args.content + '</div>');
		}
		
		var button, buttonContainer;
		buttonContainer = $(document.createElement('div'));
		buttonContainer.addClass('modal-footer marginT32');
		switch (args.type) {
			// empty/custom box, no buttons
			case -1:
				break;
			// error
			case 1:
				button = $(document.createElement('a'));
				button.attr('href', '#');
				button.addClass('btn');
				button.addClass('btn-modal');
				button.attr('title', args.texts.close);
				button.html(args.texts.close);
				button.bind('click',  function () {
					me.destroy();
					me.success();
				});
				buttonContainer.append(button);
				break;
		}
		
		this.content.append(buttonContainer);
		this.container.append(this.content);
		
		this.container.on('hidden', function () {
			$(this).remove();
		});
		$(document.body).append(this.container);
	},
	show : function () {
		this.container.modal('show');
	},
	hide : function () {
		this.destroy();
	},
	destroy : function (success) {
		this.container.modal('hide');
		this.container.remove();
		this.onClose();
	}
};
