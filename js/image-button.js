(function() {
    tinymce.PluginManager.add('imgbutton', function( editor, url ) {
        editor.addButton( 'imgbutton', {
            text: 'Insert Image',
            icon: false,
			onclick: function() {
				// find image button id
				btnid = jQuery('.mce-btn[aria-label="Insert/edit image (⌃⌥M)"]').attr('id');
				jQuery('#' + btnid).click();
			}
        });
    });
})();
