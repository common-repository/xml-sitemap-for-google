<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Get a list of all conflicting plugins.
 * @since 1.0.0
 */
function xmlsbw_check_conflicting_plugins()
{
    // Note: Jetpack is excluded from consideration, as they automatically disable their SEO module when ours is active.
    $conflictingPluginSlugs = array(
        'wordpress-seo/wp-seo.php',
        'seo-by-rank-math/rank-math.php',
        'wp-seopress/seopress.php',
        'autodescription/autodescription.php',
        'slim-seo/slim-seo.php',
        'squirrly-seo/squirrly.php',
        'google-sitemap-generator/google-sitemap-generator.php',
        'xml-sitemap-feed/xml-sitemap.php',
        'www-xml-sitemap-generator-org/www-xml-sitemap-generator-org.php',
        'google-sitemap-plugin/google-sitemap-plugin.php',
        'google-sitemap-generator/sitemap.php',
        'all-in-one-seo-pack/all_in_one_seo_pack.php',
        'simple-sitemap/simple-sitemap/php',
    );

    foreach ($conflictingPluginSlugs as $plugin) {
        // Check whether conflict plugin is active
        if (is_plugin_active($plugin)) {
            add_action('admin_notices', 'showNotice');
            return;
        }
    }
}

/**
 * Renders the notice if any conflicting plugin is found
 * @since 1.0.0
 */
function showNotice()
{
    $class = 'notice notice-warning';
    $message = __('Multiple XML Sitemap Generator plugins have been detected on your site. To avoid conflicts, please ensure that only one XML Sitemap Generator plugin is active.', 'xml-sitemap-for-google');
    printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
}

?>