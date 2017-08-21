<?php
class ACFWPMLInterfaceAdminViews {
	
	function notice($data) {
		ob_start();
		switch($data['type']) {
			
			case 'error':
				$noticetype = 'notice-error'; 
			break;
			
			case 'success':
				$noticetype = 'notice-success';
			break;
			
			case 'warning':
				$noticetype = 'notice-warning';
			break;
			
		}
		$content = $this->notice_content(array('type' => $data['subtype'], 'display' => isset($data['display']) ? $data['display'] : '', 'lang' => isset($data['lang']) ? $data['lang'] : '', 'to_page_name' => isset($data['to_page_name']) ? $data['to_page_name'] : ''));
?>
		<div class="notice <?php echo $noticetype; ?> <?php $data['dismissible'] ? print 'is-dismissible' : print ''; ?>">
        	<?php echo $content; ?>
    	</div>
<?php
		return ob_get_clean();
	}
	
	function notice_content($data) {
		ob_start();
		$topagename = isset($data['to_page_name']) && $data['to_page_name'] ? $data['to_page_name'] : 'Theme Options';
		switch($data['type']) {
			
			case 'no_config':
?>
			<p>
				<?php _e('ACF WPML Theme Options needs to be configured before it can be used.', 'acf-wpml-to'); ?> 
				<?php if($data['display'] == 'all') { ?>
				<a href="<?php echo admin_url(); ?>options-general.php?page=acf-wpml-theme-options" class="button button-primary"><?php _e('Go to ACF WPML Theme Options Settings page', 'acf-wpml-to'); ?></a>
				<?php } ?>
			</p>
<?php
			break;
			
			case 'no_field_group':
				if(in_array( 'advanced-custom-fields/acf.php', apply_filters( 'active_plugins', get_option( 'active_plugins' )))) {
					$acflink = 'acf';
				} elseif(in_array( 'advanced-custom-fields-pro/acf.php', apply_filters( 'active_plugins', get_option( 'active_plugins' )))) {
					$acflink = 'acf-field-group';
				}
?>
			<p><?php echo sprintf(__('There are no Custom Field Groups defined for or assigned to <a href="%sedit.php?post_type=acf_wpml_to"><strong>%s</strong></a>.', 'acf-wpml-to'), admin_url(), $topagename); ?> <a href="<?php echo admin_url(); ?>edit.php?post_type=<?php echo $acflink; ?>" class="button button-primary"><?php _e('Create/Edit a Custom Field Group', 'acf-wpml-to'); ?></a></p>
<?php	
			break;
			
			case 'no_to_post':
?>
			<p><?php _e('There are no Theme Options posts defined or published for ACF WPML Theme Options.', 'acf-wpml-to'); ?> <a href="<?php echo admin_url(); ?>post-new.php?post_type=acf_wpml_to" class="button button-primary"><?php _e('Create/Edit a Theme Options post', 'acf-wpml-to'); ?></a></p>
<?php	
			break;
			
			case 'to_post_not_translatable':
				if(in_array( 'wpml-translation-management/plugin.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )) {
					$buttonlink = admin_url().'admin.php?page=wpml-translation-management/menu/main.php&sm=mcsetup#ml-content-setup-sec-7';
				} elseif(in_array( 'advanced-custom-fields-pro/acf.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )) {
					$buttonlink = admin_url().'admin.php?page=sitepress-multilingual-cms/menu/translation-options.php#ml-content-setup-sec-7';
				}
?>
			<p><?php echo sprintf(__('The system has detected that the <a href="%sedit.php?post_type=acf_wpml_to"><strong>%s</strong></a> posts are not translatable. You need to set this in the WPML Translations Options panel. Please make sure WPML is configured beforehand.', 'acf-wpml-to'), admin_url(), $topagename); ?></p>
			<p>
				<a href="<?php echo $buttonlink; ?>" class="button button-primary"><?php _e('Go to WPML Translations Options panel', 'acf-wpml-to'); ?></a>
			</p>
<?php	
			break;
			
			case 'no_active_to_post':
			if($data['display'] == 'all') {
				$button = '<a href="'.admin_url().'options-general.php?page=acf-wpml-theme-options" class="button button-primary">'.__('Go to ACF WPML Theme Options Settings page', 'acf-wpml-to').'</a>';
				$messagesuffix = '';
			} elseif($data['display'] == 'config') {
				$button = '';
				$messagesuffix = ' Please set one as active from the dropdown below.';
			}
			
?>
			<p><?php echo sprintf(__('The system has detected Theme Options posts, but none is set as active.%s', 'acf-wpml-to'), $messagesuffix); ?> <?php echo $button; ?></p>
<?php	
			break;
			
			case 'options_saved_initial':
?>
			<p><?php echo sprintf(__('Options saved. ACF WPML Theme options has been activated. Please go to <a href="%sedit.php?post_type=acf_wpml_to"><strong>%s</strong></a> and start adding options.', 'acf-wpml-to'), admin_url(), $_REQUEST['to-name']); ?></p>
<?php				
			break;
			
			case 'options_saved':
?>
			<p><?php _e('Options saved', 'acf-wpml-to'); ?></p>
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
			
			case 'active_to_not_translated':
				$langstring = '';
				foreach($data['lang'] as $lang) {
					$langstring .= $lang;
					$lang === end($data['lang']) ? $langstring .= '' : $langstring .= ', ';
				}
?>
			<p><?php echo sprintf(__('ACF WPML Theme Options has detected that your active Theme Options post is not translated for the following languages: <strong>%s</strong>', 'acf-wpml-to'), $langstring); ?> <a href="<?php echo admin_url(); ?>edit.php?post_type=acf_wpml_to" class="button button-primary"><?php _e('Go to ACF WPML Theme Options Posts page', 'acf-wpml-to'); ?></a></p>
			
<?php	
			break;
			
		}

		return ob_get_clean();
	}

	function acfwpml_interface($data) {
		global $acfwpmloptions;
		ob_start();
?>
		
		<div class="wrap acfwpml-main-container">
			<h1 class="wp-heading-inline">ACF WPML Theme Options Settings</h1>
			<nav class="nav-tab-wrapper acfwpml-tab-wrapper">
				<a id="acfwpml_tab_trigger_1" data-tabid="1" class="nav-tab nav-tab-active">Config</a>
				<a id="acfwpml_tab_trigger_2" data-tabid="2" class="nav-tab">FAQ</a>
			</nav>
			<div id="acfwpml_tab_1" class="acfwpml-options-block acfwpml-active-block">
				<h1 class="wp-heading-inline">Config</h1>
				<form action="" method="post">
				<div class="wp-list-table widefat fixed acfwpml-form-row">
					<div class="acfwpml-form-block">
						<div class="acfwpml-label">
							<label for="to-name">Options Page Name</label>
						</div>
						<div class="acfwpml-field">
							<?php
								if(isset($_REQUEST['to-name'])) {
									$tonameval = $_REQUEST['to-name'];
								} elseif(isset($acfwpmloptions['to-name']) && $acfwpmloptions['to-name']) {
									$tonameval = $acfwpmloptions['to-name'];
								} else {
									$tonameval = 'Global Settings';
								}
							?>
							<input type="text" id="to-name" name="to-name" value="<?php echo $tonameval; ?>" />
						</div>
					</div>
				</div>
				<?php if($data['has_to_post']) { ?>
				<div class="wp-list-table widefat fixed acfwpml-form-row">
					<div class="acfwpml-form-block">
						<div class="acfwpml-label">
							<label for="to-active-post">Active Theme Options post</label>
						</div>
						<div class="acfwpml-field">
							<select id="to-active-post" name="to-active-post">
								<option value="">- Please Select -</option>
								<?php 
								while($data['to_posts']->have_posts()) {
									$data['to_posts']->the_post();
									$postid = get_the_ID();
									if((isset($_REQUEST['to-active-post']) && $_REQUEST['to-active-post'] == $postid)) {
										$selected = 'selected="selected"';
									} elseif(isset($data['active_to_post']) && $data['active_to_post'] == $postid) {
										$selected = 'selected="selected"';
									} else {
										$selected = '';
									}
								?>
								<option <?php echo $selected; ?> value="<?php echo $postid; ?>"><?php the_title(); ?></option>
								<?php } wp_reset_query(); ?>
							</select>
						</div>
					</div>
				</div>
				<?php } ?>
				<div class="wp-list-table widefat fixed acfwpml-form-row">
					<div class="acfwpml-form-block">
						<div class="acfwpml-save-button">
							<button type="submit" class="button button-primary" name="save-awp-settings">Save Settings</button>
						</div>
					</div>
				</div>
				</form>
			</div>
			<div id="acfwpml_tab_2" class="acfwpml-options-block">
				<h1 class="wp-heading-inline">How it works.</h1>
				<p>The plugin adds a new function named get_field_option() which you can use to display options created in the Global Options post type you define.</p>
				<p>Usage:
					<ol>
						<li>echo get_field_option('option_slug');</li>
						<li>echo do_shortcode('[get_field_option option="site_name"]');</li>
						<li>(in post content body/editor): [get_field_option option="site_name"]</li>
					</ol> 
				</p>
			</div>
		</div>
<?php
		return ob_get_clean();
	}

	function meta_boxes($data) {
		ob_start();
		switch($data['type']) {
			
			case 'active':
				!$data['isactive'] ? 
					$message = '<font color="red"><strong>This post is not the active one for ACF WPML Theme Options. Option fields are being taken from the following post: <a href="'.admin_url().'post.php?post='.$data['activepost'].'&action=edit" target="_blank">'.get_the_title($data['activepost']).'</a>.</strong></font>' : 
					$message = '<font color="green"><strong>This post is the active one for ACF WPML Theme Options</strong></font>';
?>
			<p><?php echo $message; ?></p>
			<?php if(!$data['isactive']) { ?>
			<p><button id="set-acf-wpml-post-active" class="button button-primary" data-postid="<?php echo $data['currentpost']; ?>">Set this post as active</button></p>
			<?php } ?>
<?php
			break;
			
		}
		
		return ob_get_clean();
	}
	
}
?>