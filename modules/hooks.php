<?php
 // init process for registering our button
 add_action('init', 'ccpuz_wpse72394_shortcode_button_init');
 function ccpuz_wpse72394_shortcode_button_init() {
      if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') && get_user_option('rich_editing') == 'true')
           return;
      add_filter("mce_external_plugins", "ccpuz_wpse72394_register_tinymce_plugin");
      add_filter('mce_buttons', 'ccpuz_wpse72394_add_tinymce_button');
}


//This callback registers our plug-in
function ccpuz_wpse72394_register_tinymce_plugin($plugin_array) {
    $plugin_array['ccpuz_wpse72394_button'] = plugins_url('/shortcode.js', __FILE__ );
    return $plugin_array;
}

//This callback adds our button to the toolbar
function ccpuz_wpse72394_add_tinymce_button($buttons) {
            //Add the button ID to the $button array
    $buttons[] = "ccpuz_wpse72394_button";
    return $buttons;
}

add_filter( 'wp_head', 'ccpuz_add_cf' );
function ccpuz_add_cf($content){
	global $post;
	
	if( is_single() || is_page() ){
	echo '

	';
	}else{
	return $content;
	}
}

