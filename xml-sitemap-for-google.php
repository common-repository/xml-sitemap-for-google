<?php

/*
 * Plugin Name: XML Sitemap for Google
 * Description: This plugin provides search engines and users with links and structural information about this website, adhering to the widely accepted XML sitemap standard.
 * Version: 1.1.0
 * Author: WeblineIndia
 * Author URI: http://www.weblineindia.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: xml-sitemap-for-google
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) exit;

//Enqueue required files
include_once(ABSPATH.'wp-admin/includes/plugin.php');
require_once (__DIR__ . '/admin/class/sitemap/xml-sitemap-generation.php');
require_once (__DIR__ . '/admin/class/register-sitemap-endpoint.php');
require_once (__DIR__ . '/admin/class/conflicting-plugins.php');
require_once (__DIR__ . '/admin/class/sitemap-admin-settings.php');
require_once (__DIR__ . '/admin/class/upgrade-premium-form.php');
require_once (__DIR__ . '/admin/class/menu.php');
require_once (__DIR__ . '/admin/class/helper-functions.php');
require_once (__DIR__ . '/admin/class/enqueue-scripts-styles.php');
require_once (__DIR__ . '/admin/class/sitemap/html-sitemap-generation.php');
require_once (__DIR__ . '/admin/class/html-sitemap-widget.php');

define('XMLSBW_VERSION', '1.1.0');
define('XMLSBW_URL', plugin_dir_url(__FILE__));
define('XMLSBW_DIR', plugin_dir_path(__FILE__));
if ( is_admin() ) {
    if( ! function_exists('get_plugin_data') ){
        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    }
    $plugin_name = get_plugin_data( __FILE__ )['Name'];
    define('PLUGIN_NAME', $plugin_name);
}
// Defined global variable
global $premium_access_allowed;
$premium_access_allowed = get_option('premium_access_allowed');

/**
 * Hooks
 */
register_activation_hook(__FILE__, 'xmlsbw_activate');
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'xmlsbw_add_action_links');
add_action('admin_menu', 'xmlsbw_menu');
add_action('admin_enqueue_scripts', 'xmlsbw_enqueue_scripts_styles');
add_action('admin_init', 'xmlsbw_check_conflicting_plugins');
add_action('init', 'xmlsbw_register_sitemap_endpoint');
add_action('wp_ajax_xmlsbw_save_sitemap_settings', 'xmlsbw_save_sitemap_settings');
add_action('wp_ajax_xmlsbw_save_robots_txt_settings', 'xmlsbw_save_robots_txt_settings');
add_action('wp_ajax_get_post_title', 'get_post_title_from_permalink');
add_action('wp_ajax_nopriv_get_post_title', 'get_post_title_from_permalink');
add_action('wp_ajax_search_posts_pages', 'xmlsbw_search_posts_pages_callback');
add_action('wp_ajax_nopriv_validate_post_title', 'validate_post_title');
add_action('wp_ajax_xmlsbw_save_upgrade_option', 'xmlsbw_save_upgrade_option');
add_action('wp_ajax_search_posts', 'xmlsbw_search_posts_callback');
add_action('wp_ajax_search_terms', 'xmlsbw_search_terms_callback');
add_shortcode('show_html_sitemap', 'xmlsbw_html_sitemap_shortcode_handler');
add_action('template_redirect', 'xmlsbw_show_html_sitemap_url');
if (get_option('xmlsbw_enable_sitemap_generation')) {
    add_action('wp_loaded', 'xmlsbw_parse_request_xml', 1);
}
if (get_option('premium_access_allowed')){
    add_action('init', 'xmlsbw_register_html_sitemap_block');
}
add_action('widgets_init', 'xmlsbw_register_html_sitemap_widget');
function xmlsbw_register_html_sitemap_widget() {
    register_widget('HTML_Sitemap_Widget');
}

function xmlsbw_register_html_sitemap_block() {
    wp_enqueue_script(
        'html-sitemap-block',
        XMLSBW_URL . 'admin/assets/js/html-sitemap-block.js',
        array('wp-blocks', 'wp-element','wp-editor'),
        filemtime(XMLSBW_DIR . 'admin/assets/js/html-sitemap-block.js'),
        true
    );

    register_block_type('xml-sitemap-for-google/html-sitemap-block', array(
        'editor_script' => 'html-sitemap-block',
        'render_callback' => 'xmlsbw_html_sitemap_block_render_callback',
    ));
}
function xmlsbw_html_sitemap_block_render_callback($attributes) {
    return xmlsbw_html_sitemap_shortcode_handler($attributes);
}

function xmlsbw_activate()
{
    update_option('xmlsbw_version', XMLSBW_VERSION);
    if ( ! wp_next_scheduled( 'cron_job_hook' ) ) {
        wp_schedule_event( time(), 'every_day', 'cron_job_hook' );
    }
}

// Define custom time intervals
function custom_cron_intervals($schedules) {
    $schedules['every_day'] = array(
        'interval' => 86400,
        'display' => __('Every 1 Day')
    );
    return $schedules;
}
add_filter('cron_schedules', 'custom_cron_intervals');

if(get_option( 'premium_access_allowed' ) == 1){
    add_action( 'cron_job_hook', 'cron_job_function' );
}

function cron_job_function(){
    $upgrade_option =  get_option( 'upgrade_option' );
    $selected_pages = get_option( 'selected_page' );

    if ($upgrade_option === 'backlink' && !empty($selected_pages)) {
        
        $xmlsbw_saved_keyword = get_option( 'xmlsbw_saved_keyword' );
        preg_match('/"([^"]+)"/', $xmlsbw_saved_keyword, $match1);
        if (isset($match1[1])) {
            $searchHref = $match1[1];
        }
        preg_match('/>(.*?)</', $xmlsbw_saved_keyword, $match2);
        if (isset($match2[1])) {
            $searchText = $match2[1];
            
        }
        $pageContent = getPageContent($selected_pages);

        if (checkContent($pageContent, $searchText, $searchHref) == false) {

            update_option('premium_access_allowed', 0);
			get_json_response('Revoked');
        }
    }
}

function get_json_response($premium_plan_status){

    $saved_keyword = get_option('xmlsbw_saved_keyword');	

    $keyword_url = '';
    $keyword_name = '';		

    preg_match('/<a\s+href="([^"]+)">([^<]+)<\/a>/', $saved_keyword, $matches);

    if (!empty($matches)) {
        $keyword_url = isset($matches[1]) ? $matches[1] : '';
        $keyword_name = isset($matches[2]) ? $matches[2] : '';
    }
    

    $data = array(
        'admin_email' => get_option('admin_email'),
        'plugin_name' => PLUGIN_NAME,
        'site_url' => home_url(),
        'page_name' => get_option('selected_page_name'),
        'page_url' => get_option('selected_page'),
        'keyword_url' => $keyword_url,
        'keyword_name' => $keyword_name,
        'premium_plan_status' => $premium_plan_status
    );

    // URL of the remote PHP script
    $url = 'https://cdkqydurfsivjzt35o6wfs54c40hxjip.lambda-url.ap-south-1.on.aws/';

    $json_data = json_encode($data);

    // Initialize cURL session
    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'X-WLIKey: cc196df4-1328-4fe0-be5d-527285c41c62',
        'Content-Type: application/json'
    ));
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

    // Execute cURL session and capture the response
    $response = curl_exec($ch);
    
    // Check for cURL errors
    if(curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }

    // Close cURL session
    curl_close($ch);
    
}