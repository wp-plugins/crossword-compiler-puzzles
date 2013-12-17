<?php
add_shortcode( 'crossword', 'ccpuz_shortcode_handler' );

function ccpuz_shortcode_handler( $atts, $content = null ) {
   global $post;
   $js_url = get_post_meta( $post->ID, 'ccpuz_js_url', true );
   $js_run =  get_post_meta( $post->ID, 'ccpuz_js_run', true );
   if (empty($js_url)){
   #old version
    $js_url = get_post_meta( $post->ID, 'js_url', true );
    $js_run = get_post_meta( $post->ID, 'js_run', true );
    }
   $out =  '<script src="'. $js_url . '"></script>' .$js_run.'<div id="CrosswordCompilerPuz"></div>';	
	return $out;
}
