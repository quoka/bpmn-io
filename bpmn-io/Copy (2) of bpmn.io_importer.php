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



// Include bpmn.io when post contains a .bpmn media attachment
function bpmn_mediaType_handler($content) {
	$attachments = get_attached_media( 'application/bpmn+xml');
	if (!empty($attachments)) {
		wp_enqueue_script( 'bpmn-io_navigated', plugins_url('/bower_components/bpmn-js/dist/bpmn-navigated-viewer.min.js',__FILE__) );
	}
	return $content;
}
add_filter('the_content', 'bpmn_mediaType_handler');



// Include bpmn.io when post contains a [bpmn] short code media attachment
function bpmn_shortcode_handler($atts) {
	wp_enqueue_script( 'bpmn-io_navigated', plugins_url('/bower_components/bpmn-js/dist/bpmn-navigated-viewer.min.js',__FILE__) );
	return $atts;
}
add_shortcode('bpmn', 'bpmn_shortcode_handler');























?>