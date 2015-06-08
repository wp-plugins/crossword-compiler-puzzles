<?php
		

add_action( 'add_meta_boxes', 'ccpuz_add_custom_box' );
function ccpuz_add_custom_box() {
	global $current_user;
		add_meta_box(
			'ccpuz_add_crossword',
			__( 'Add Crossword', 'db_domain' ),
			'ccpuz_add_crossword',
			'post' , 'advanced', 'high'
		);		
		add_meta_box(
			'ccpuz_add_crossword',
			__( 'Add Crossword', 'db_domain' ),
			'ccpuz_add_crossword',
			'page' , 'advanced', 'high'
		);	
}

function ccpuz_add_crossword(){
	global $post;
	global $current_user;
	wp_nonce_field( plugin_basename( __FILE__ ), 'ccpuz_noncename' );
	
	echo '
	<div class="tw-bs">
		<div class="form-horizontal">
				<fieldset>
				
				<div class="control-group">
					<label class="control-label" for="select01">Select Method</label>
					<div class="controls">
					  <select id="crossword_method" name="crossword_method">
						<option value="url" '.( get_post_meta( $post->ID, 'crossword_method', true ) == 'url' ? ' selected ' : '' ).' >URL</option>
						<option value="local" '.( get_post_meta( $post->ID, 'crossword_method', true ) == 'local' ? ' selected ' : '' ).' >Local File</option>
						
					  </select>
					</div>
				  </div>
				
				  <div class="control-group ccpuz_url_class">
					<label class="control-label" for="fileInput">URL Upload</label>
					<div class="controls">
						<input type="text" class="input-xlarge" name="ccpuz_url_upload_field" id="ccpuz_url_upload_field" value="'.get_post_meta( $post->ID, 'ccpuz_url_upload_field', true ).'">
						<!--
						<button type="button" class="btn btn-primary" id="url_upload_button" >Upload File</button> -->

					</div>
				  </div>
				<div class="control-group ccpuz_file_class">

					<label class="control-label" for="fileInput">HTML File</label>
					<div class="controls">
						<input class="input-file" id="ccpuz_html_file" name="ccpuz_html_file" type="file">
					</div>
					<label class="control-label" for="fileInput">JS File</label>
					<div class="controls">
						<input class="input-file" id="ccpuz_js_file" name="ccpuz_js_file" type="file">
					</div>					
				  </div>
				

				<div class="form-actions">
					<button type="button" id="ccpuz_insert_code" class="btn btn-primary">Insert</button>
				  </div>

				
				</fieldset>
		</div>
		
	</div>
	';

}

function ccpuz_applet(){
   	 $filename   ='crosswordCompiler.js';
     $upload_dir = wp_upload_dir();
     $file = $upload_dir['basedir'] .'/ccpuz/'.$filename ;
	 $uploadVersion = true; //file_exists($file);

      if ($uploadVersion){
 //upload with overwrite to latest if user has already uploaded the applet file to server
            $response = wp_remote_get('http://crossword.info/html5/js/'.$filename);
            $js= wp_remote_retrieve_body($response);
            $path = $upload_dir['basedir'] .'/ccpuz' ;
							
		if(!empty($js) && wp_mkdir_p( $path) ) {
		@file_put_contents( $file, $js );					
		} else {
 wp_die('Could not upload applet file', '<strong>Error</strong>: Could not upload applet file');
       }
 	 }			
}

function doer_of_stuff() {
  return  new WP_Error('broke', __("I've fallen and can't get up"));
}


add_action( 'save_post', 'ccpuz_save_postdata' );
function ccpuz_save_postdata( $post_id ) {
global $current_user;

 if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
      return;

  if ( 'page' == $_POST['post_type'] )
  {
    if ( !current_user_can( 'edit_page', $post_id->ID ) )
        return;
  }
  else
  {
    if ( !current_user_can( 'edit_post', $post_id->ID ) )
        return;
  }

 //global $error;
//$error = new WP_Error();

    //return $error;

  if( get_post_type( $post_id ) == 'post' || get_post_type( $post_id ) == 'page' ){
		
		$should_be = 0;
		if( substr_count( get_post()->post_content, '[crossword]' ) > 0  ){
			$should_be = 1;
		}
		
		update_post_meta( $post_id, 'crossword_method', $_POST['crossword_method'] );
		if( $_POST['crossword_method'] == 'url' ){
				if( $_POST['ccpuz_url_upload_field'] ){
				if( substr_count( $_POST['ccpuz_url_upload_field'], 'crossword.info' ) > 0 ){
					//var_dump(file_get_contents( $_POST['ccpuz_file_upload_field'] ));
					$response = wp_remote_get( $_POST['ccpuz_url_upload_field'] );
                    $file_source= wp_remote_retrieve_body($response);
					preg_match ( '/src="[^"]+"/i' , $file_source , $arr );
					
					$res_command = get_string_between( $file_source, '$(function(){', '})' );
					
					$id = get_string_between( $res_command, '$("', '")');
					$res_command = str_replace($id, '#CrosswordCompilerPuz', $res_command);
					$res_command = preg_replace( '/$\("#[^"]+"\)/i', '$("#SSS")', $res_command );
					$res_command = preg_replace( '/ROOTIMAGES: "[^"]+"/i', 'ROOTIMAGES: ""', $res_command );
					$res_command = preg_replace( '/PROGRESS:[^,]+"/i', '', $res_command );

					//var_Dump( $res_command );
					
					$res_command = str_replace( 'ROOTIMAGES: "', 'ROOTIMAGES: "'.plugins_url( 'inc/CrosswordCompilerApp/CrosswordImages/', __FILE__ ), $res_command );
                    $res_command =str_replace('\\', '\\\\', $res_command);
					###############
					
					
					foreach( $arr as $single ){
						if( substr_count( $single, '_xml.js' ) > 0 ){
							$js_url = 'http://crossword.info'.str_replace('src=', '', str_replace('"', '', $single ) );
							$upload_dir = wp_upload_dir();
							$filename   = sanitize_file_name( basename($js_url) ) ;
							
							if( wp_mkdir_p( $upload_dir['path'] ) ) {
								$file = $upload_dir['path'] . '/' . $filename;
								$url = $upload_dir['url'] . '/' . $filename;
							} else {
								$file = $upload_dir['basedir'] . '/' . $filename;								
								$url = $upload_dir['baseurl'] . '/' . $filename;
							}

							$response = wp_remote_get($js_url );
                            $image_data = wp_remote_retrieve_body($response);
							@file_put_contents( $file, $image_data );
						}
					}
				}
			
			if( !$url  && !get_post_meta( $post_id, 'ccpuz_url_upload_field', true ) && $should_be == 1 ){
				wp_die('Oops, something wrong: must give full crossword.info puzzle URL.', '<strong>Error</strong>: Something went wrong.');
			}
     		
			### adding custom fields
			$str_parent = ' jQuery(".entry-content").attr( "class", "entry-content puzzle"); ';
            ccpuz_applet();			
            update_post_meta( $post_id, 'ccpuz_js_url', $url );
			update_post_meta( $post_id, 'ccpuz_js_run', '<script>jQuery(document).ready(function($) { '.$str_parent.' '.$res_command.' });</script>' );
	
			update_post_meta( $post_id, 'ccpuz_url_upload_field', $_POST['ccpuz_url_upload_field'] );
			}
		}
		
		
		if( $_POST['crossword_method'] == 'local' ){
			
			if( $_FILES["ccpuz_html_file"]["tmp_name"] ){			
				$file_source = file_get_contents( $_FILES["ccpuz_html_file"]["tmp_name"] );
				$res_command = get_string_between( $file_source, '$(function(){', '})' );
				$res_command = preg_replace( '/ROOTIMAGES: "[^"]+"/i', 'ROOTIMAGES: ""', $res_command );
				$res_command = str_replace( 'ROOTIMAGES: "', 'ROOTIMAGES: "'.plugins_url( 'inc/CrosswordCompilerApp/CrosswordImages/', __FILE__ ), $res_command );
				$str_parent = ' $(".entry-content").attr( "class", "entry-content puzzle"); ';
				if( !$res_command ){
					wp_die('Oops, Something wrong with html file.', '<strong>Error</strong>: Something wrong with puzzle html file.');
				}
				update_post_meta( $post_id, 'ccpuz_js_run', '<script>jQuery(document).ready(function($) { '.$str_parent.' '.$res_command.' });</script>' );
			}else{
				
				if( get_post_meta( $post_id, 'ccpuz_js_run', true) == ''  && $should_be == 1 ){
					wp_die('Oops, Add HTML file exported by Crossword Compiler.', '<strong>Error</strong>: Something went wrong.');
				}
			}
			if( $_FILES["ccpuz_js_file"]["name"] ){
				$upload_dir = wp_upload_dir();
				$filename   = sanitize_file_name( $_FILES["ccpuz_js_file"]["name"] ) ;
				if( wp_mkdir_p( $upload_dir['path'] ) ) {
					$file = $upload_dir['path'] . '/' . $filename;
					$url = $upload_dir['url'] . '/' . $filename;
				}else {
					$file = $upload_dir['basedir'] . '/' . $filename;$url = $upload_dir['baseurl'] . '/' . $filename;
				}
				$image_data = @file_get_contents( $_FILES["ccpuz_js_file"]["tmp_name"] );
				@file_put_contents( $file, $image_data );
                ccpuz_applet();
				update_post_meta( $post_id, 'ccpuz_js_url', $url );
			}else{
				if( get_post_meta( $post_id, 'ccpuz_js_url', true) == '' && $should_be == 1 ){
				wp_die('Oops, Add puzzle .js file exported by Crossword Compiler.', '<strong>Error</strong>: Something went wrong.');
				}
		
			}
			
			
	
		}
		
  }

}

