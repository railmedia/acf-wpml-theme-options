<?php
/*
* Plugin Name: ACF - WPML Theme Options
* Plugin URI: https://www.tudorache.me
* Description: Creates a new Custom Post Type for Theme Options
* Version: 1.0.0
* Author: Adrian-Emil Tudorache
* Author URI: https://www.tudorache.me
* Text Domain: acf-wpml-to
*/

define('acfwpmlpath', plugin_dir_path( __FILE__ ));
define('acfwpmlurl', plugin_dir_url( __FILE__ ));

//The plugin must have ACF or ACF Pro and WPML enabled in order to work.
if ( 
	(in_array( 'advanced-custom-fields/acf.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && in_array( 'sitepress-multilingual-cms/sitepress.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && !function_exists('get_field_option') ) ||
	 (in_array( 'advanced-custom-fields-pro/acf.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && in_array( 'sitepress-multilingual-cms/sitepress.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && !function_exists('get_field_option') )
	) {
	
	if((in_array( 'advanced-custom-fields/acf.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && in_array( 'advanced-custom-fields-pro/acf.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ))) {
		// The plugin cannot be used when both ACF and ACF Pro are active
		require_once(acfwpmlpath.'/globals/controllers/reqs.class.php');
		
	} else {
		//Fire!
		if(is_admin()) {
			require_once(acfwpmlpath.'/admin/init.php');
		}
		
		require_once(acfwpmlpath.'/globals/init.php');
		
		//Main function
		function get_field_option($option) {
			global $wpdb, $sitepress;
			$acfwpmloptions = get_option('acf_wpml_to_options');
			$current_lang = ICL_LANGUAGE_CODE;
			$default_lang = $sitepress->get_default_language();
			$active_to = $acfwpmloptions['acf_wpml_to_active_post'];
			if($current_lang != $default_lang) {
				$tr_active_to = $wpdb->get_var("SELECT trid FROM {$wpdb->prefix}icl_translations WHERE element_id={$active_to} AND language_code='{$default_lang}'");
				$tr_active_to = $wpdb->get_var("SELECT element_id FROM {$wpdb->prefix}icl_translations WHERE trid={$tr_active_to} AND language_code='{$current_lang}'");
				if($tr_active_to) {
					$active_to = $tr_active_to;
				} else {
					$active_to = $active_to;
				}
			}
			return get_field($option, $active_to);
		}
		
		//Shortcode version
		function get_field_option_shortcode($atts=null, $content=null) {
			$atts = shortcode_atts( array(
				'option' => '',
			), $atts );
			return get_field_option($atts['option']);
		}
		add_shortcode('get_field_option', 'get_field_option_shortcode');
		
		//Cleanup database after deletion
		register_uninstall_hook(__FILE__, 'acf_wpml_to_uninstall');
		
	}
} else {
	// Check what requisites
	require_once(acfwpmlpath.'/globals/controllers/reqs.class.php');
}
?>