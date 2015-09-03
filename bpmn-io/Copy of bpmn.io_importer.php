<?php
/*
 Plugin Name: bpmn.io
 Plugin URI: http://bpmn.io/
 Description: A BPMN 2.0 rendering toolkit and web modeler. bpmn.io simplifies creating, embedding and extending BPMN diagrams.
 Author: Camunda Services GmbH
 Version: 1.0
 Author URI: http://www.camunda.org
 */



// Register BPMN mime type
function mime_type_bpmn($existing_mimes) {
	$existing_mimes['bpmn'] = 'application/bpmn+xml';
	return $existing_mimes;
}
add_filter('mime_types', 'mime_type_bpmn');



// Register BPMN as Media Type
function media_type_bpmn($post_mime_types) {
	$post_mime_types['application/bpmn+xml'] = array(
		__('BPMN'), 
		__('Manage BPMN Diagrams'), 
		_n_noop('BPMN <span class="count">(%s)</span>', 'BPMN <span class="count">(%s)</span>')
	);
	return $post_mime_types;
}
add_filter('post_mime_types', 'media_type_bpmn');









// The bpmn-io HTML - load .bpmn file
function bpmnio_html( $bpmn_data ) {
	$id = uniqid();
	$html =  '<div id="bpmnio-'. $id .'" class="bpmn-io"></div>'."\n";
	$html .= '<script type="text/javascript">'."\n";
	$html .= 'jQuery(document).ready(function($) {'; 
	$html .= "'use strict';";
	$html .= "var BpmnViewer = window.BpmnJS;";
	$html .= "var viewer = new BpmnViewer({ container: '#bpmnio-". $id ."' });";
	$html .= "var xhr = new XMLHttpRequest();";
	$html .= "xhr.onreadystatechange = function() {";
	$html .= "if (xhr.readyState === 4) {";
	$html .= "viewer.importXML(xhr.response, function(err) {";
	$html .= "if (!err) {";
	$html .= "console.log('success!');";
	$html .= "viewer.get('canvas').zoom('fit-viewport');";
	$html .= "} else {";
	$html .= "console.log('something went wrong:', err);";
	$html .= "}";
	$html .= "});";
	$html .= "}";
	$html .= "};";
	$html .= "xhr.open('GET', '". $bpmn_data ."', true);";
	$html .= "xhr.send(null);";
	$html .= "});"."\n";
	$html .= '</script>'."\n";
	return $html;
}



// Bind BPMN media formatting to editor
function bpmn_media_to_editor( $html, $id, $attachment ) {
	$mime = get_post_mime_type( $id ); 
	global $add_bpmn_script;
	$add_bpmn_script = true;
	if ($mime == 'application/bpmn+xml') {
		$html = '[bpmn]'. $attachment['url'] .'[/bpmn]';
	}
	return $html;
}
add_filter( 'media_send_to_editor', 'bpmn_media_to_editor', 7, 3 );








/*
// Add bpmn.io to included scripts
function load_js_bpmnio($hook) {
	wp_enqueue_script('bpmn-io_navigated', plugins_url('/bower_components/bpmn-js/dist/bpmn-navigated-viewer.min.js',__FILE__) );
	echo $hook;
}
add_action('wp_head', 'load_js_bpmnio');

// Add bpmn.io to admin scripts
function load_admin_js_bpmnio($hook) {
	global $pagenow, $typenow;
	if (empty($typenow) && !empty($_GET['post'])) {
		$post = get_post($_GET['post']);
		$typenow = $post->post_type;
	}
	if (is_admin() && $pagenow=='post-new.php' OR $pagenow=='post.php' && $typenow=='events') {
		wp_enqueue_script('bpmn-io_navigated', plugins_url('/bower_components/bpmn-js/dist/bpmn-navigated-viewer.min.js',__FILE__) );
	}
	echo $hook;
}
add_action('admin_enqueue_scripts', 'load_admin_js_bpmnio');
*/








//register bpmn.io for insertion
function register_bpmn_script() {
	wp_register_script('bpmn-io_navigated', plugins_url('/bower_components/bpmn-js/dist/bpmn-navigated-viewer.min.js', __FILE__), array('jquery'), '1.0', true);
}
add_action('init', 'register_bpmn_script');



// Conditonally insert bpmn.io when required  
function print_bpmn_script() {
	global $add_bpmn_script;
	if ( ! $add_bpmn_script )
		return;
	wp_print_scripts('bpmn-io_navigated');
}
add_action('wp_footer', 'print_bpmn_script');



// render shortcode HTML 
function bpmn_shortcode_handler($atts) {
	//flag bpmn.io for insertion
	global $add_bpmn_script;
	$add_bpmn_script = true;
	// actual shortcode handling here
	$html = bpmnio_html('http://local.wordpress/wp-content/uploads/2015/09/nopassword.bpmn');
	return print_r(get_attached_media( '', $atts ));
}
add_shortcode('bpmn', 'bpmn_shortcode_handler');








?>