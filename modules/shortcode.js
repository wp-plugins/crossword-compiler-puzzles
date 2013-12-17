jQuery(document).ready(function($) {

    tinymce.create('tinymce.plugins.ccpuz_wpse72394_plugin', {
        init : function(ed, url) {
                // Register command for when button is clicked
                ed.addCommand('ccpuz_wpse72394_insert_shortcode', function() {
				
					 $('.tw-bs').dialog({	
						width:500
					  });
				
                });

            // Register buttons - trigger above command when clicked
            ed.addButton('ccpuz_wpse72394_button', {title : 'Insert puzzle', cmd : 'ccpuz_wpse72394_insert_shortcode', image: url + '/images/logo.png' });
        },   
    });

    // Register our TinyMCE plugin
    // first parameter is the button ID1
    // second parameter must match the first parameter of the tinymce.create() function above
    tinymce.PluginManager.add('ccpuz_wpse72394_button', tinymce.plugins.ccpuz_wpse72394_plugin);
	
	$('#ccpuz_insert_code').live('click', function(){
                     content =  '[crossword]';
					 tinymce.execCommand('mceInsertContent', false, content);
					 $('.tw-bs').dialog('destroy');
	})
});
