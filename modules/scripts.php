<?php
add_action('wp_print_scripts', 'ccpuz_add_script_fn', 1);

function ccpuz_applet_js(){
   	 $filename   ='crosswordCompiler.js';
     $upload_dir = wp_upload_dir();
     $path = $upload_dir['basedir'] .'/ccpuz/'.$filename ;
	  if (file_exists($path)){
        //if applet file has been uploaded by user
        return  $upload_dir['baseurl'] .'/ccpuz/'.$filename ;
      } else 									
   return 'http://crossword.info/html5/js/crosswordCompiler.js';
}


function ccpuz_add_script_fn()
    {
    global $post;
    if (is_admin())
        {
        wp_enqueue_style('bootstrap_css', plugins_url('/css/boot-cont.css', __FILE__));
        wp_enqueue_media();
        wp_enqueue_style('jquery-ui-standard-css', plugins_url('/css/jquery-ui.css', __FILE__));
        wp_enqueue_script('tiny_mce');
        wp_enqueue_script('ccpuz_admin_js', plugins_url('/js/admin.js', __FILE__) , array(
            'jquery',
            'jquery-ui-core',
            'jquery-ui-widget',
            'jquery-ui-position',
            'jquery-ui-button',
            'jquery-ui-draggable',
            'jquery-ui-resizable',
            'jquery-ui-dialog',
            'jquery-effects-core'
        ) , '1.0');
        }
      else
        {
        if (is_single() || is_page())
            {
            if (substr_count($post->post_content, '[crossword]') > 0)
                {
                wp_enqueue_script('raphael', plugins_url('/inc/CrosswordCompilerApp/raphael.js', __FILE__) , array(
                    'jquery'
                ) , '2.1.0');
               $js_url = ccpuz_applet_js();
                wp_enqueue_script('ccpuz_CrosswordCompilerApp', $js_url , array(
                    'jquery',
                    'raphael'
                ) , '9');

                // wp_enqueue_style('ccpuz_front_css', plugins_url('/css/front.css', __FILE__ ) ) ;

                }
            }
        }
    }
