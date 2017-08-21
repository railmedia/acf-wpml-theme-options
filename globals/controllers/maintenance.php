<?php
function acf_wpml_to_uninstall() {
	
	global $wpdb;
	
	delete_option('acf_wpml_to_options');
	
	$to_posts = $wpdb->get_results("SELECT ID FROM {$wpdb->prefix}posts WHERE post_type='acf_wpml_to'");
	foreach($to_posts as $to_post) {
		wp_delete_post($to_post->ID, 1);
	}
	
}
?>