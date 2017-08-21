<?php
class ACFWPMLConfigAdmin {
	
	var $interface, $acfwpmloptions;
	
	function __construct() {
		$this->interface = new ACFWPMLInterfaceAdmin;
		global $acfwpmloptions;
		$acfwpmloptions = $this->get_options();
		add_action('admin_enqueue_scripts', array($this, 'scripts'));
		add_action('admin_menu', array($this, 'menu'));
		add_action('admin_init', array($this, 'save_options'));
		add_action('plugins_loaded', array($this, 'save_lang_options'));
		add_filter('plugin_action_links_acf-wpml-theme-options/acf-wpml-theme-options.php', array($this, 'action_links'));
	}
	
	function scripts() {
		global $post_type;
		wp_enqueue_style('acf-wpml-theme-options-admin-css', acfwpmlurl.'admin/assets/css/acf-wpml-theme-options.css');
		wp_register_script('acf-wpml-settings', acfwpmlurl.'admin/assets/js/acf-wpml-settings.js', array('jquery'), '', true);
		wp_register_script('acf-wpml-block-js', '//malsup.github.io/jquery.blockUI.js', array('jquery'), '', true);
		wp_register_script('acf-wpml-set-post-as-active-js', acfwpmlurl.'admin/assets/js/set-acf-wpml-post-active.js', array('jquery'), '', true);
		if($post_type == 'acf_wpml_to') {
			$scripts = array('acf-wpml-block-js', 'acf-wpml-set-post-as-active-js');
			foreach($scripts as $script) {
				wp_enqueue_script($script);
			}
			wp_localize_script('acf-wpml-set-post-as-active-js', 'postactive', array(
				'ajaxurl' => admin_url( 'admin-ajax.php' )
			));
		}
		if(isset($_GET['page']) && $_GET['page'] == 'acf-wpml-theme-options') {
			wp_enqueue_script('acf-wpml-settings');
		}
	}
	
	function menu() {
		add_submenu_page( 'options-general.php', __( 'ACF WPML Theme Options', 'acf-wpml-to' ),  __( 'ACF WPML Theme Options', 'acf-wpml-to' ) , 'manage_options', 'acf-wpml-theme-options', array($this->interface, 'acfwpml_interface') );
	}
	
	function action_links( $links ) {
		$plugin_links = array();
		$plugin_links[] = '<a href="'.admin_url().'/options-general.php?page=acf-wpml-theme-options">'.__( 'Settings', 'acf-wpml-to' ).'</a>';
		return array_merge( $plugin_links, $links );
	}
	
	function save_options() {
		
		global $acfwpmloptions, $wpdb, $sitepress;
		if(isset($_REQUEST['save-awp-settings'])) {
			
			$acfwpmloptions['to-name'] = $_REQUEST['to-name'];
			
			if(!isset($acfwpmloptions['acf_wpml_to_config'])) {
				$subtype = 'options_saved_initial';
				$acfwpmloptions['acf_wpml_to_config'] = 1;
			} else {
				$subtype = 'options_saved';
			}
			
			if(isset($_REQUEST['to-active-post'])) {
				$acfwpmloptions['acf_wpml_to_active_post'] = $_REQUEST['to-active-post'];
			}
			
			update_option('acf_wpml_to_options', $acfwpmloptions, 1);
			
			if(!isset($acfwpmloptions['acf_wpml_to_config'])) {
				$this->interface->notice(array('type' => 'success', 'subtype' => $subtype, 'dismissible' => 1));
			}


		}
	}
	
	function save_lang_options() {
		global $sitepress, $acfwpmloptions;
		$langoptions = array('langs' => icl_get_languages('skip_missing=0'), 'default_lang' => $sitepress->get_default_language(), 'current_lang' => ICL_LANGUAGE_CODE);
		$acfwpmloptions = get_option('acf_wpml_to_options') ? get_option('acf_wpml_to_options') : array();
		foreach($langoptions as $optionname => $optionvalue) {
			$acfwpmloptions[$optionname] = $optionvalue;
		}
		update_option('acf_wpml_to_options', $acfwpmloptions, 1);
	}
	
	function get_options() {
		return get_option('acf_wpml_to_options');
	}
	
	function create_initial_to_post() {
		$hasposts = get_posts(array('post_type' => 'acf_wpml_to'));
		if(!$hasposts) {
			wp_insert_post(array('post_title' => $_REQUEST['to-name'], 'post_type' => 'acf_wpml_to', 'post_status' => 'publish'));
		}
	}
	
}
new ACFWPMLConfigAdmin;
?>