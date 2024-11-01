<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Registers the XML sitemap endpoint.
 * @since 1.0.0
 */
function xmlsbw_register_sitemap_endpoint()
{

    $xmlsbw_sitemap_url = get_option('xmlsbw_sitemap_url');
    global $premium_access_allowed;
    $xmlsbw_sitemap_url = !empty($xmlsbw_sitemap_url) ? $xmlsbw_sitemap_url . ".xml" : "sitemap.xml";

    // Add rewrite rule for main sitemap.xml
    add_rewrite_rule($xmlsbw_sitemap_url, 'index.php?sitemap=1', 'top');

    $plugins = array(
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

    // Check if any of the plugins are active
    $active = false;
    foreach ($plugins as $plugin) {
        if (is_plugin_active($plugin)) {
            $active = true;
            break;
        }
    }

    if($active){
        // Add rewrite tag for xml_sitemap query variable
        add_rewrite_tag('%sitemap%', '1');

        // Add rewrite rules for other sitemap variations
        add_rewrite_rule('sitemap-misc\.xml$', 'index.php?sitemap=misc', 'top');
        add_rewrite_rule('author-sitemap\.xml$', 'index.php?sitemap=author', 'top');
        add_rewrite_rule('additional-sitemap\.xml$', 'index.php?sitemap=additional', 'top');

        $post_types = get_option('xmlsbw_selected_post_types', array());
        $taxonomies = get_option('xmlsbw_selected_taxonomies', array());

        // Add rewrite rules for post type specific sitemaps
        foreach ($post_types as $post_type) {
            add_rewrite_rule("{$post_type}-sitemap\.xml$", "index.php?post_type={$post_type}&sitemap=1", 'top');
            add_rewrite_rule('^([^/]+)-sitemap-([0-9]+)?\.xml$','index.php?post_type={$post_type}&sitemap=1', 'top');
        }

        // Add rewrite rules for taxonomy specific sitemaps
        foreach ($taxonomies as $taxonomy) {
            add_rewrite_rule("{$taxonomy}-sitemap\.xml$", "index.php?taxonomy={$taxonomy}&xml_sitemap=1", 'top');
        }
    }else{
        // Add rewrite tag for xml_sitemap query variable
        add_rewrite_tag('%xml_sitemap%', '1');

        // Add rewrite rules for other sitemap variations
        add_rewrite_rule('sitemap-misc\.xml$', 'index.php?xml_sitemap=misc', 'top');
        add_rewrite_rule('author-sitemap\.xml$', 'index.php?xml_sitemap=author', 'top');
        add_rewrite_rule('additional-sitemap\.xml$', 'index.php?xml_sitemap=additional', 'top');

        // Get public post types and taxonomies
        $post_types = get_post_types(array('public' => true), 'names');
        $taxonomies = get_taxonomies(array('public' => true), 'names');

        // Add rewrite rules for post type specific sitemaps
        foreach ($post_types as $post_type) {
            add_rewrite_rule("{$post_type}-sitemap\.xml$", "index.php?post_type={$post_type}&xml_sitemap=1", 'top');
            add_rewrite_rule('^([^/]+)-sitemap-([0-9]+)?\.xml$','index.php?post_type={$post_type}&xml_sitemap=1', 'top');
        }

        // Add rewrite rules for taxonomy specific sitemaps
        foreach ($taxonomies as $taxonomy) {
            add_rewrite_rule("{$taxonomy}-sitemap\.xml$", "index.php?taxonomy={$taxonomy}&xml_sitemap=1", 'top');
        }

    }

    // Flush rewrite rules to apply changes
    flush_rewrite_rules();
}

?>