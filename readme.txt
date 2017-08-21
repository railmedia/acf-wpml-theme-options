=== ACF WPML Theme Options ===
Contributors: railmediaro
Tags: acf, acfpro, wpml, theme, settings, options
Requires at least: 3.0
Tested up to: 4.8.1

Plugin which adds another way of displaying global options created with ACF on websites which use WPML for multilanguage purposes.

== Description ==
ACF WPML Theme Options allows you to use another way of displaying Theme Options fields defined with ACF and translated with WPML allowing you to avoid any kind of problems in the translation process.

Instead of performing queries on translated ACF options pages, it allows you to assign custom fields to a custom post type and display the fields in your plugins and themes with a single function.

After it is properly configured and all the requisites are met, you can use it by calling get_field_option(\'option_name\') or by triggering it as a shortcode [get_field_option option=\"option_name\"] - this feature allowing you to display options inside your posts and pages content.

The plugin is dependent on ACF or ACF Pro and WPML. However, it will not work if you are running a website which has both ACF and ACF Pro enabled at the same time.

== Installation ==
1. Upload and activate plugin to your WP installation
2. Go to Settings -> ACF WPML Theme Options
3. Replace the default name of the Custom Post Type to be added (default is Global Settings) and save. (don\'t worry if the new Custom Post Type doesn\'t appear. You can click on the link which appears at the top in the body of the notification or simply refresh the screen or navigate to another page. The custom post type should appear afterwards).
4. Create a post of the newly acf_wpml_to post type, save it and set it as active. You will be guided through the entire process by the notifications which appear at the top of the screen.
5. In ACF / ACF Pro, create a new field group, add a few options and assign it to your post type (for ACF -> Post Type = acf_wpml_to; for ACF Pro -> Post Type = the name you chose in step #3 above).
6. You\'re good to go. Don\'t forget to translate the acf_wpml_to posts you create in order to benefit of the full functionality of this plugin. (you will be nagged by a warning notification until you do that) :)