<?php
class ACFWPMLAjaxAdmin {
	
	function __construct() {
		$ajax = array('set_acf_wpml_post_active');
		foreach($ajax as $action) {
			add_action('wp_ajax_'.$action, array($this, $action));
			add_action('wp_ajax_nopriv_'.$action, array($this, $action));
		}
	}
	
	function set_acf_wpml_post_active() {
		global $wpdb, $acfwpmloptions;
		$postid = $_POST['postid'];
		
		// $wpdb->delete(
			// $wpdb->prefix.'postmeta',
			// array('meta_key' => 'acf_wpml_active_post')
		// );
		//update_post_meta($postid, 'acf_wpml_active_post', 1);
		$acfwpmloptions['acf_wpml_to_active_post'] = $postid;
		update_option('acf_wpml_to_options', $acfwpmloptions, 1);
		//update_option('acf_wpml_to_active_post', $postid, 1);
		
		wp_die();
	}
		
}
new ACFWPMLAjaxAdmin;
?>