<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Function to enqueue scripts and styles
 */
function xmlsbw_enqueue_scripts_styles($hook)
{

    $current_screen = get_current_screen();
    
    // Check whether current screen is settings page of the XML sitemap generator plugin
    if ($current_screen->id === 'toplevel_page_sitemap-settings' || $current_screen->id === 'xml-sitemap-for-google_page_upgrade-to-premium') {
        // Enqueue JS
        wp_enqueue_script('xmlsbw-script-js', XMLSBW_URL . 'admin/assets/js/xml-sitemap-for-google.js', array('jquery'), XMLSBW_VERSION, true);
        // Enqueue Admin Style CSS
        wp_enqueue_style('xmlsbw-style-css', XMLSBW_URL . 'admin/assets/css/xml-sitemap-for-google.css', array(), XMLSBW_VERSION);
        // Enqueue jQuery
        wp_enqueue_script('jquery');
        // Enqueue jQuery UI and its datepicker widget
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-ui-style', XMLSBW_URL . 'admin/assets/css/jquery-ui.css', array(), '1.13.2');
        // Enqueue jQuery UI Timepicker Addon
        wp_enqueue_script('jquery-ui-timepicker', XMLSBW_URL . 'admin/assets/js/jquery-ui-timepicker-addon.min.js', array('jquery', 'jquery-ui-datepicker'), '1.6.3', true);
        wp_enqueue_style('jquery-ui-timepicker-style', XMLSBW_URL . 'admin/assets/css/jquery-ui-timepicker-addon.min.css', array(), '1.6.3');
        // Enqueue jQuery UI for Autocomplete
        wp_enqueue_script('jquery-ui-autocomplete', XMLSBW_URL . 'admin/assets/js/autocomplete.js', array('jquery'), XMLSBW_VERSION, true);
        wp_enqueue_style('jquery-ui-autocomplete-css', XMLSBW_URL . 'admin/assets/css/jquery-ui-autocomplete.css', array(), '1.12.1');
    }

    wp_localize_script('xmlsbw-script-js', 'url', array(
        'home_url' => home_url(),
        'plugin_url' => XMLSBW_URL,
        'sitemap_url' => get_option('xmlsbw_sitemap_url','sitemap'),
        'admin_email' => get_option('admin_email'),
        'plugin_name' => 'XML Sitemap For Google',
        'page_name' => get_option('selected_page'),
        'premium_access' => get_option('premium_access_allowed'),
    )
    );

}

?>