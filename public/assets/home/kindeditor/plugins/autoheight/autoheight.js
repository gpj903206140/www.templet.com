/*******************************************************************************
* KindEditor - WYSIWYG HTML Editor for Internet
* Copyright (C) 2006-2011 kindsoft.net
*

*******************************************************************************/

KindEditor.plugin('autoheight', function(K) {
	var self = this;

	if (!self.autoHeightMode) {
		return;
	}

	var edit = self.edit;
	var body = edit.doc.body;
	var minHeight = K.removeUnit(self.height);

	edit.iframe[0].scroll = 'no';
	body.style.overflowY = 'hidden';

	edit.afterChange(function() {
		self.resize(null, Math.max((K.IE ? body.scrollHeight : body.offsetHeight) + 62, minHeight));
	});
});
