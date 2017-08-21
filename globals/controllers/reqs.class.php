<?php
class ACFWPMLReqs {
	
	function __construct() {
		add_action('admin_notices', array($this, 'notice'), 10);
		add_action('admin_init', array($this, 'check_requisites'));
	}
	
	function check_requisites() {
		if(
			!in_array( 'advanced-custom-fields/acf.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) )) && 
			!in_array( 'advanced-custom-fields-pro/acf.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) )) && 
			!in_array( 'sitepress-multilingual-cms/sitepress.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )
		) {
			
			$this->notice(array('type' => 'error', 'subtype' => 'no_reqs', 'dismissible' => 1));
			return 0;
			
		} elseif(
			!in_array( 'advanced-custom-fields/acf.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) )) && 
			!in_array( 'advanced-custom-fields-pro/acf.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ))
		) {
				
			$this->notice(array('type' => 'error', 'subtype' => 'no_acf', 'dismissible' => 1));
			return 0;
				
		} elseif(
			in_array( 'advanced-custom-fields/acf.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) )) && 
			in_array( 'advanced-custom-fields-pro/acf.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ))
		) {
			
			$this->notice(array('type' => 'error', 'subtype' => 'acf_and_pro_are_active', 'dismissible' => 1));
			return 0;
		
		} elseif(!in_array( 'sitepress-multilingual-cms/sitepress.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )) {
			
			$this->notice(array('type' => 'error', 'subtype' => 'no_wpml', 'dismissible' => 0));
			return 0;
			
		} elseif(function_exists('get_field_option')) {
			
			$this->notice(array('type' => 'error', 'subtype' => 'main_function_exists', 'dismissible' => 0));
			return 0;
			
		} else {
			
			return 1;
			
		}
	}
	
	function notice($data) {
		ob_start();
		if($data) {
?>
		<div class="notice <?php echo $data['type']; ?> <?php $data['dismissible'] ? print 'is-dismissible' : print ''; ?>">
<?php
		switch($data['subtype']) {
			
			case 'no_reqs':
?>
			<p><?php _e('Advanced Custom Fields / Advanced Custom Fields PRO and WPML have not been detected. You cannot use ACF WPML Theme Options without these plugins.', 'acf-wpml-to'); ?></p>
<?php	
			break;
			
			case 'no_acf':
?>
			<p><?php _e('Advanced Custom Fields / Advanced Custom Fields PRO has not been detected. You cannot use ACF WPML Theme Options without it.', 'acf-wpml-to'); ?></p>
<?php	
			break;
			
			case 'no_wpml':
?>
			<p><?php _e('WPML has not been detected. You cannot use ACF WPML Theme Options without it.', 'acf-wpml-to'); ?></p>
<?php
			break;

			case 'acf_and_pro_are_active':
?>
			<p><?php _e('Advanced Custom Fields & Advanced Custom Fields PRO have been detected as active. You have to disable one of it before being able to use ACF WPML Theme Options.', 'acf-wpml-to'); ?> <a class="button button-primary" href="<?php echo admin_url(); ?>plugins.php">Go to Plugins manager</a></p>
<?php			
			break;
			
			case 'main_function_exists':
?>
			<p><?php _e('ACF WPML Theme Options has detected that your website uses the function get_field_option(). You will need to target it and disable it before being able to use the plugin.', 'acf-wpml-to'); ?></p>
<?php
			break;
		
		}
?>
		</div>
<?php

		}
		
	}

}
new ACFWPMLReqs;
?>