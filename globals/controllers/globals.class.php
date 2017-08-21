<?php
class ACFWPMLGlobals {
	
	var $config;
	
	function __construct() {
		global $acfwpmloptions;
		$this->config = isset($acfwpmloptions['acf_wpml_to_config']) ? $acfwpmloptions['acf_wpml_to_config'] : 0;
		if($this->config) {
			add_action('init', array($this, 'cpt'), 0);
		}
	}
	
	function cpt() {
		global $acfwpmloptions;
		$posts = array(
			'acf_wpml_to' => array(
				'single_label' 		=> isset($acfwpmloptions['to-name']) && $acfwpmloptions['to-name'] ? $acfwpmloptions['to-name'] : 'Global Settings',
				'multi_label' 		=> isset($acfwpmloptions['to-name']) && $acfwpmloptions['to-name'] ? $acfwpmloptions['to-name'] : 'Global Settings',
				'icon' 				=> 'dashboard',
				'supports' 			=> array('title'),
				'priority'			=> 90,
				'show_in_menu'		=> true,
				'search'			=> false
			)
		);
		
		foreach($posts as $posttype => $details) {
			
			$labels = array(	
				'name'                  => $details['multi_label'],
				'singular_name'         => $details['single_label'],
				'menu_name'             => $details['multi_label'],
				'parent_item_colon'     => 'Parent Item:',
				'all_items'             => 'All Items',
				'add_new_item'          => 'Add New '.$details['single_label'],
				'add_new'               => 'Add '.$details['single_label'],
				'new_item'              => 'New '.$details['single_label'],
				'edit_item'             => 'Edit '.$details['single_label'],
				'update_item'           => 'Update '.$details['single_label'],
				'view_item'             => 'View '.$details['single_label'],
				'view_items'            => 'View '.$details['multi_label'],
				'not_found'				=> 'No '.$details['multi_label'].' found',
				'search_items'          => 'Search '.$details['multi_label'],
				'items_list'            => $details['multi_label'],
				'items_list_navigation' => $details['multi_label'],
				'filter_items_list'     => 'Filter '.$details['multi_label']
			);
		
			$args = array(
				'labels' => $labels,
				'public' => true,	
				'publicly_queryable' => true,
				'show_ui' => true,
				'query_var' => true,
				'rewrite' => true,
				'capability_type' => 'post',
				'hierarchical' => true,
				'menu_position' => null,
				'menu_icon' => 'dashicons-'.$details['icon'],
				'exclude_from_search' => $details['search'],
				'show_in_menu'        => $details['show_in_menu'],
				'menu_position'       => $details['priority'] ? $details['priority'] : 10,
				'rewrite' => array(
					'slug' => $posttype,
					'with_front' => false
				),
				'supports' => $details['supports']
			);
			
			register_post_type( $posttype, $args );
		}
		$acfwpmloptions['acf_wpml_active_to'] = 1;
		update_option('acf_wpml_to_options', $acfwpmloptions, 1);
		
	}
	
}
global $acfwpmlglobals;
$acfwpmlglobals = new ACFWPMLGlobals;
?>