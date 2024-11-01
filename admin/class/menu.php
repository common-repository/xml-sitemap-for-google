<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Function for activation hook
 */
// function xmlsbw_activate()
// {
//     update_option('xmlsbw_version', XMLSBW_VERSION);
//     update_option('xmlsbw_sitemap_data', array());
// }

/**
 * Function for add action link
 */
function xmlsbw_add_action_links($links_array)
{
    array_unshift($links_array, '<a href="options-general.php?page=sitemap-settings">Settings</a>');
    return $links_array;    
}

/**
 * Function for main admin menu
 */
function xmlsbw_menu()
{
    add_menu_page('XML Sitemap for Google', 'XML Sitemap for Google', 'manage_options', 'sitemap-settings', 'xmlsbw_sitemap_settings_options');
    add_submenu_page('sitemap-settings', 'Sitemaps', 'Sitemaps', 'manage_options', 'sitemap-settings', 'xmlsbw_sitemap_settings_options');
    add_submenu_page('sitemap-settings', 'Free Upgrade to PRO', 'Free Upgrade to PRO', 'manage_options', 'upgrade-to-premium', 'xmlsbw_upgrade_to_premium');
}
?>