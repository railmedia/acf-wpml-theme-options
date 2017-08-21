<?php
class ACFWPMLInterfaceAdmin {
	
	var $views;
	
	function __construct() {
		$this->views = new ACFWPMLInterfaceAdminViews;
		add_action('admin_notices', array($this, 'notices'));
		add_filter('manage_acf_wpml_to_posts_columns' , array($this, 'unset_acf_wpml_to_date_column'));
		add_action('plugins_loaded', array($this, 'init'));
		add_action('admin_notices', array($this, 'to_post_is_translated'));
	}
	
	function init() {
		global $sitepress;
		if($sitepress->get_default_language() == ICL_LANGUAGE_CODE) { 
			add_action('add_meta_boxes', array($this, 'meta_boxes'));
			add_filter('manage_acf_wpml_to_posts_columns' , array($this, 'acf_wpml_to_columns_init'));
			add_action('manage_acf_wpml_to_posts_custom_column' , array($this, 'acf_wpml_to_columns'), 10, 2 );
		}
	}
	
	function notices() {
		global $acfwpmloptions;
		$config = isset($acfwpmloptions['acf_wpml_to_config']) ? $acfwpmloptions['acf_wpml_to_config'] : 0;
		
		(!isset($_GET['page']) || ($_GET['page'] != 'acf-wpml-theme-options')) ? $display = 'all' : $display = 'config';
		
		if(!$config) {
			$this->notice(array('type' => 'error', 'subtype' => 'no_config', 'dismissible' => 0, 'display' => $display));
		} elseif($config && !$this->to_post_is_translatable()) {
			$this->notice(array('type' => 'error', 'subtype' => 'to_post_not_translatable', 'dismissible' => 0, 'display' => $display, 'to_page_name' => isset($acfwpmloptions['to-name']) ? $acfwpmloptions['to-name'] : ''));
		} elseif($config && !$this->has_to_post()) {
			$this->notice(array('type' => 'error', 'subtype' => 'no_to_post', 'dismissible' => 0, 'display' => $display));
		} elseif($config && !$this->has_active_to_post()) {
			$this->notice(array('type' => 'error', 'subtype' => 'no_active_to_post', 'dismissible' => 0, 'display' => $display));
		} elseif($config && !$this->field_groups_exist()) {
			$this->notice(array('type' => 'error', 'subtype' => 'no_field_group', 'dismissible' => 0, 'display' => $display, 'to_page_name' => isset($acfwpmloptions['to-name']) ? $acfwpmloptions['to-name'] : ''));
		}
		
	}
	
	function field_groups_exist() {
		global $wpdb;
		if(in_array( 'advanced-custom-fields/acf.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )) {
			$acf_post_type = 'acf';
		} elseif(in_array( 'advanced-custom-fields-pro/acf.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )) {
			$acf_post_type = 'acf-field-group';
		}
		$acffields = get_posts(array('post_type' => $acf_post_type, 'posts_per_page' => 999));
		$hasacf = 0;
		if($acffields) {
			$hasacf = 0;
			foreach($acffields as $acffield) {
				setup_postdata( $acffield );
				$fieldsid = $acffield->ID;
				if(in_array( 'advanced-custom-fields/acf.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )) {
					$rules = get_post_meta($fieldsid, 'rule');
					foreach($rules as $rule) {
						if($rule['value'] == 'acf_wpml_to') {
							$hasacf = 1;
							break;
						} else {
							$hasacf = 0;
						}
					}
				} elseif(in_array( 'advanced-custom-fields-pro/acf.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )) {
					$postcontent = $wpdb->get_var("SELECT post_content FROM {$wpdb->prefix}posts WHERE ID={$fieldsid}");
					$rules = unserialize($postcontent);
					foreach($rules['location'] as $rule) {
						foreach($rule as $r) {
							foreach($r as $key => $value) {
								if($key == 'value' && $value == 'acf_wpml_to') {
									$hasacf = 1;
									break 4;
								} else {
									$hasacf = 0;
								}
							}
						}
					}
				}
			}
		}
		wp_reset_postdata();
		return $hasacf;
	}
	
	function has_to_post() {
		$to_posts = get_posts(array('post_type' => 'acf_wpml_to', 'post_status' => 'publish'));
		$to_posts ? $result = 1 : $result = 0;
		return $result;
	}
	
	function has_active_to_post() {
		global $acfwpmloptions;
		$active_post = isset($acfwpmloptions['acf_wpml_to_active_post']) ? $acfwpmloptions['acf_wpml_to_active_post'] : 0;
		$active_post ? $result = $active_post : $result = 0;
		return $result;
	}
	
	function get_to_posts() {
		global $sitepress;
		
		$current_lang = ICL_LANGUAGE_CODE;
		$default_lang = $sitepress->get_default_language();
		$sitepress->switch_lang($default_lang);
		$to_posts = new WP_Query(array('post_type' => 'acf_wpml_to', 'posts_per_page' => 999, 'post_status' => 'publish', 'orderby' => 'title', 'order' => 'ASC'));
		$sitepress->switch_lang($current_lang);
		
		return $to_posts;
		
	}
	
	function to_post_is_translatable() {
		$istranslatable = get_option('icl_sitepress_settings');
		(isset($istranslatable['custom_posts_sync_option']['acf_wpml_to']) && $istranslatable['custom_posts_sync_option']['acf_wpml_to'] == "1") ? $result = 1 : $result = 0;
		return $result;
	}
	
	function to_post_is_translated() {
		global $acfwpmloptions, $wpdb;
		if(isset($acfwpmloptions['langs']) && $acfwpmloptions['langs'] && isset($acfwpmloptions['acf_wpml_to_active_post']) && $acfwpmloptions['acf_wpml_to_active_post']) {
			$no_tr = array();
			foreach($acfwpmloptions['langs'] as $lang => $details) {
				if($acfwpmloptions['default_lang'] != $lang) {
					$tr_active_to = $wpdb->get_var("SELECT trid FROM {$wpdb->prefix}icl_translations WHERE element_id={$acfwpmloptions['acf_wpml_to_active_post']} AND language_code='{$acfwpmloptions['default_lang']}'");
					$tr_active_to = $wpdb->get_var("SELECT element_id FROM {$wpdb->prefix}icl_translations WHERE trid={$tr_active_to} AND language_code='{$lang}'");
					if(!$tr_active_to) {
						$no_tr[] = $details['english_name'];
					}
				}
			}
			
			if($no_tr && isset($acfwpmloptions['acf_wpml_to_config']) && $acfwpmloptions['acf_wpml_to_config'] && isset($acfwpmloptions['acf_wpml_to_active_post']) && $acfwpmloptions['acf_wpml_to_active_post']) {
				$this->notice(array('type' => 'warning', 'subtype' => 'active_to_not_translated', 'dismissible' => 1, 'lang' => $no_tr));
			}
		}
	}
	
	function notice($data=null) {
		if($data) {
			echo $this->views->notice($data);
		}
	}
	
	function acfwpml_interface() {
		echo $this->views->acfwpml_interface(array(
			'has_to_post' 		=> $this->has_to_post(), 
			'active_to_post' 	=> $this->has_active_to_post(), 
			'to_posts' 			=> $this->get_to_posts())
		);
	}
	
	function meta_boxes() {
		global $post, $wpdb;
		$postid = $post->ID;
		if(get_post_status($postid) == 'publish') {
			add_meta_box( 'acf_wpml_active_to', __( 'Active', 'acf-wpml-to' ), array($this, 'meta_box'), 'acf_wpml_to', 'normal', 'high', array('type' => 'active') );
		}
	}
	
	function meta_box($post, $data) {
		global $wpdb, $acfwpmloptions;
		$postid = $post->ID;
		switch($data['args']['type']) {
			
			case 'active':
				if(get_post_status($postid) == 'publish') {
					$activepost = isset($acfwpmloptions['acf_wpml_to_active_post']) ? $acfwpmloptions['acf_wpml_to_active_post'] : 0;
					($postid == $activepost) ? $isactive = 1 : $isactive = 0;
					echo $this->views->meta_boxes(array('type' => 'active', 'isactive' => $isactive, 'activepost' => $activepost, 'currentpost' => $postid));
				}		
			break;
			
		}
	}
	
	function unset_acf_wpml_to_date_column($columns) {
		unset($columns['date']);
		return $columns;
	}
	
	function acf_wpml_to_columns_init($columns) {
		$columns['enabled'] = 'Enabled';
  		return $columns;
	}

	function acf_wpml_to_columns($column, $postid) {		
		global $acfwpmloptions;
    	switch ( $column ) {
			
			case 'enabled':
				$activepost = $acfwpmloptions['acf_wpml_to_active_post'];
				$activepost == $postid ? 
					print '<label class="switch enabled-switch"><input type="checkbox" checked="checked"><span class="rc-enable-disable enabled slider round" data-postid="'.$postid.'" data-status="enabled"></span></label>' : 
					print '<label class="switch disabled-switch"><input type="checkbox"><span class="rc-enable-disable disabled slider round" data-postid="'.$postid.'" data-status="disabled"></span></label>';
			break;

    	}
	}
	
}
?>