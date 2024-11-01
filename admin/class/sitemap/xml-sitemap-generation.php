<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Generates XML Sub Sitemap for particular post types
 * @since 1.0.0
 */
function generate_sitemap_for_post_type($post_type, $excluded_post_ids = array(), $page = 1)
{
    $xmlsbw_enable_include_last_mod_time = get_option('xmlsbw_enable_include_last_mod_time');
    $links_per_sitemap = get_option('xmlsbw_links_per_sitemap') != "" ? get_option('xmlsbw_links_per_sitemap') : 1000;
    $xmlsbw_post_priority_calculation = get_option('xmlsbw_post_priority_calculation', 'no_calculation');

    $args = array(
        'post_type' => $post_type,
        'posts_per_page' => $links_per_sitemap,
        'post_status' => 'publish',
        'orderby' => 'modified',
        'order' => 'DESC',
        'paged' => $page,
        'post__not_in' => $excluded_post_ids,
    );

    if ($post_type === 'attachment') {
        $args['post_status'] = 'any';
    }

    $query = new WP_Query($args);

    header('Content-type: text/xml');
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<?xml-stylesheet type="text/xsl" href="' . esc_url(XMLSBW_URL) . 'sitemap.xsl"?>';
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

    while ($query->have_posts()) {
        $query->the_post();
        $current = get_the_ID();
        $post_type = get_post_type($current);
        $comment_count = wp_count_comments($current)->approved;

        if (!in_array($current, $excluded_post_ids)) {

            $permalink = get_permalink();
            $lastmod = get_the_modified_time('Y-m-d\TH:i:sP');
            $lastmod_utc = date_create_from_format('Y-m-d\TH:i:sP', $lastmod)->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s+00:00');

            echo "<url>";
            echo "<loc>" . esc_url($permalink) . "</loc>";

            if ($post_type == "post") {
                $xmlsbw_post_priority = '0.5';
                if ($xmlsbw_post_priority_calculation === 'comment_count') {
                    $xmlsbw_post_priority = calculate_priority_based_on_comment_count($comment_count);
                } else{
                    $xmlsbw_post_priority = esc_attr(get_option('xmlsbw_post_priority', '0.5')[$post_type]);
                }
            } else {
                $xmlsbw_post_priority = esc_attr(get_option('xmlsbw_post_priority', '0.5')[$post_type]);
            }

            echo "<priority>" . esc_attr($xmlsbw_post_priority) . "</priority>";
            echo "<changefreq>" . esc_attr(get_option('xmlsbw_post_frequency', 'weekly')[$post_type]) . "</changefreq>";
            if ($xmlsbw_enable_include_last_mod_time) {
                echo '<lastmod>' . esc_attr($lastmod_utc)  . '</lastmod>';
            }
            echo "</url>";
        }
    }
    echo "</urlset>";
}

function calculate_priority_based_on_comment_count($comment_count)
{
    if ($comment_count <= 1) {
        return '0.1';
    } elseif ($comment_count <= 4) {
        return '0.4';
    } elseif ($comment_count <= 7) {
        return '0.7';
    } elseif ($comment_count >= 8) {
        return '1';
    }
}

/**
 * Generates XML Sub Sitemap for particular taxonomy types
 * @since 1.0.0
 */
function generate_sitemap_for_taxonomy($taxonomy, $excluded_term_ids = array())
{
    $xmlsbw_enable_include_last_mod_time = get_option('xmlsbw_enable_include_last_mod_time');
    $terms = get_terms(
        array(
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
        )
    );

    header('Content-type: text/xml');
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<?xml-stylesheet type="text/xsl" href="' . esc_url(XMLSBW_URL) . 'sitemap.xsl"?>';
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

    $post_types = get_post_types(array('public' => true), 'names');

    foreach ($terms as $term) {
        $term_link = get_term_link($term);
        $lastmod = '';

        $args = array(
            'post_type' => $post_types,
            'posts_per_page' => -1,
        );
        $posts = get_posts($args);

        foreach ($posts as $post) {
            if (has_term($term->slug, $taxonomy, $post)) {
                $lastmod = get_the_modified_time('Y-m-d\TH:i:sP', $post);
                $lastmod_utc = date_create_from_format('Y-m-d\TH:i:sP', $lastmod)->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s+00:00');

                $current = $term->term_id;
                if (!in_array($current, $excluded_term_ids)) {
                    echo "<url>";
                    echo "<loc>" . esc_url($term_link) . "</loc>";
                    echo "<priority>" . esc_attr(get_option('xmlsbw_taxonomy_priority', '0.5')[$taxonomy]) . "</priority>";
                    echo "<changefreq>" . esc_attr(get_option('xmlsbw_taxonomy_frequency', 'weekly')[$taxonomy]) . "</changefreq>";
                    if ($xmlsbw_enable_include_last_mod_time) {
                        echo '<lastmod>' . esc_attr($lastmod_utc) . '</lastmod>';
                    }
                    echo "</url>";
                    break;
                }
            }
        }
    }

    echo "</urlset>";
    exit;
}

/**
 * Generates XML Sitemap with indices based on selected content
 * @since 1.0.0
 */
function generate_xml_sitemap_with_indices($query)
{
    if (isset($query->query_vars['xml_sitemap']) || isset($query->query_vars['sitemap'])) {

        global $file_path_xml;
        $file_path_xml = $query->request;
        
        add_filter('w3tc_can_cache', 'xmlsbw_exclude_xml_xsl_from_cache', 10, 2);

        if(!(get_option('xmlsbw_include_all_post_type'))){
            $post_types = get_option('xmlsbw_selected_post_types', array());
            $taxonomies = get_option('xmlsbw_selected_taxonomies', array());
        }else{
            $post_types = get_post_types(['public' => true], 'names');
            $taxonomies_to_exclude = array('post_format', 'product_type', 'product_visibility', 'product_shipping_class', 'pa_color', 'pa_size','post_translations','language');
            $taxonomies = get_taxonomies(['public' => true], 'names');
            $taxonomies = array_diff($taxonomies, $taxonomies_to_exclude);
        }

        //Posts to be excluded
        $xmlsbw_excluded_posts = get_option('xmlsbw_excluded_posts', array());
        $excluded_posts_string = '';
        if (!empty($xmlsbw_excluded_posts)) {
            $excluded_posts_string = implode(',', $xmlsbw_excluded_posts);
        }
        $excluded_post_ids = array_map('trim', explode(',', $excluded_posts_string));
        $excluded_post_ids = get_option('xmlsbw_enable_advanced_settings') ? $excluded_post_ids : array();

        //Taxonomies to be excluded
        $xmlsbw_excluded_terms = get_option('xmlsbw_excluded_terms', array());
        $excluded_terms_string = '';
        if (!empty($xmlsbw_excluded_terms)) {
            $excluded_terms_string = implode(',', $xmlsbw_excluded_terms);
        }
        $excluded_term_ids = array_map('trim', explode(',', $excluded_terms_string));
        $excluded_term_ids = get_option('xmlsbw_enable_advanced_settings') ? $excluded_term_ids : array();

        //Get additional pages data
        $additional_pages_data = get_option('xmlsbw_additional_pages_data', array());
        //Get link per pages count
        $links_per_sitemap = get_option('xmlsbw_links_per_sitemap') ? get_option('xmlsbw_links_per_sitemap') : 1000;

        // Check whether home page is to be included
        $xmlsbw_include_home_page = get_option('xmlsbw_include_home_page');
        // Check whether author archive page is to be included
        $xmlsbw_include_author_pages = get_option('xmlsbw_include_author_pages');
        // Check whether last mod time is to be included
        $xmlsbw_enable_include_last_mod_time = get_option('xmlsbw_enable_include_last_mod_time');

        $requested_sitemap = isset($_SERVER['REQUEST_URI']) ? sanitize_text_field($_SERVER['REQUEST_URI']) : '';

        $args = array(
            'post_type' => 'any',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'fields' => 'ids',
        );

        $query = new WP_Query($args);

        if ($query->have_posts()) {
            foreach ($query->posts as $post_id) {
                $post_modified_gmt_home = get_post_modified_time('Y-m-d H:i:s', true, $post_id);
                $post_modified_gmt_array_home[] = $post_modified_gmt_home;
            }
            wp_reset_postdata();
        }
        $lastmod_date_time_home = max($post_modified_gmt_array_home);

        if(get_option( 'xmlsbw_enable_additional_pages')){
            if ($requested_sitemap === '/additional-sitemap.xml') {
                header('Content-type: text/xml');
                echo '<?xml version="1.0" encoding="UTF-8"?>';
                echo '<?xml-stylesheet type="text/xsl" href="' . esc_url(XMLSBW_URL) . 'sitemap.xsl"?>';
                echo '<urlset  xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
                foreach ($additional_pages_data['url'] as $index => $url) {
                    if (!empty($url) && isset($additional_pages_data['priority'][$index]) && isset($additional_pages_data['frequency'][$index]) && isset($additional_pages_data['last_modified'][$index])) {

                        $priority = isset($additional_pages_data['priority'][$index]) ? $additional_pages_data['priority'][$index] : 'always';
                        $frequency = isset($additional_pages_data['frequency'][$index]) ? $additional_pages_data['frequency'][$index] : 0;
                        $lastmod = isset($additional_pages_data['last_modified'][$index]) ? $additional_pages_data['last_modified'][$index] : gmdate('c');
                        $lastmodformatted = gmdate('Y-m-d\TH:i:s+00:00', strtotime($lastmod));

                        echo "<url>";
                        echo "<loc>" . esc_url($url) . "</loc>";
                        echo "<priority>" . esc_attr($priority) . "</priority>";
                        echo "<changefreq>" . esc_attr($frequency) . "</changefreq>";
                        if ($xmlsbw_enable_include_last_mod_time) {
                            echo '<lastmod>' . esc_attr($lastmodformatted) . '</lastmod>';
                        }
                        echo "</url>";
                    }
                }
                echo '</urlset>';
                exit;
            }
        }

        if ($requested_sitemap === '/author-sitemap.xml') {

            $authors = get_users();

            header('Content-type: text/xml');
            echo '<?xml version="1.0" encoding="UTF-8"?>';
            echo '<?xml-stylesheet type="text/xsl" href="' . esc_url(XMLSBW_URL) . 'sitemap.xsl"?>';
            echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

            $cache_key = 'author_post_times_cache';
            $author_post_times = wp_cache_get($cache_key);

            if (false === $author_post_times) {
                $author_ids = wp_list_pluck($authors, 'ID');

                $args = array(
                    'author__in' => $author_ids,
                    'post_type' => 'post',
                    'post_status' => 'publish',
                    'fields' => 'ids',
                );

                $query = new WP_Query($args);

                $author_post_times = array();
                foreach ($query->posts as $post_id) {
                    $author_id = get_post_field('post_author', $post_id);
                    $post_modified_gmt = get_post_field('post_modified_gmt', $post_id);

                    if (!isset($author_post_times[$author_id]) || strtotime($post_modified_gmt) > strtotime($author_post_times[$author_id])) {
                        $author_post_times[$author_id] = $post_modified_gmt;
                    }
                }

                wp_cache_set($cache_key, $author_post_times);
            }

            foreach ($author_post_times as $author_id => $author_latest_modified_time) {

                $priority_home = get_option('xmlsbw_post_priority')['author'];
                $frequency_home = get_option('xmlsbw_post_frequency')['author'];

                echo '<url>';
                echo '<loc>' . esc_url(get_author_posts_url($author_id)) . '</loc>';
                echo '<changefreq>' . esc_attr($frequency_home) . '</changefreq>';
                echo '<priority>' . esc_attr($priority_home) . '</priority>';
                if ($xmlsbw_enable_include_last_mod_time) {
                    $lastmod = gmdate('Y-m-d\TH:i:s+00:00', strtotime($author_latest_modified_time));
                    echo '<lastmod>' . esc_attr($lastmod) . '</lastmod>';
                }
                echo '</url>';
            }
            echo '</urlset>';
            exit;
        }

        if (
            in_array($requested_sitemap, array_map(function ($taxonomy) {
                return '/' . $taxonomy . '-sitemap.xml';
            }, $taxonomies))
        ) {
            $taxonomy = str_replace('-sitemap.xml', '', basename($requested_sitemap));
            $sub_sitemap = generate_sitemap_for_taxonomy($taxonomy, $excluded_term_ids);
            echo esc_html($sub_sitemap);
            exit;
        }

        if (preg_match('/^\/(.+?)-?sitemap-(\d*)\.xml$/', $requested_sitemap, $matches) || preg_match('/^\/(.+?)-sitemap\.xml$/', $requested_sitemap, $matches)) {
            $post_type = $matches[1];
            $page = isset($matches[2]) && !empty($matches[2]) ? intval($matches[2]) : 1;
            $sub_sitemap = generate_sitemap_for_post_type($post_type, $excluded_post_ids, $page);
            echo esc_html($sub_sitemap);
            exit;
        }

        if ($requested_sitemap == '/sitemap-misc.xml') {

            $priority_home = get_option('xmlsbw_post_priority')['home'];
            $frequency_home = get_option('xmlsbw_post_frequency')['home'];

            header('Content-type: text/xml');
            echo '<?xml version="1.0" encoding="UTF-8"?>';
            echo '<?xml-stylesheet type="text/xsl" href="' . esc_url(XMLSBW_URL) . 'sitemap.xsl"?>';
            echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
            // Checks if home page is included or not
            if ($xmlsbw_include_home_page == 1) {
                echo '<url>';
                echo '<loc>' . esc_url(home_url('/')) . '</loc>';
                echo "<priority>" . esc_xml($priority_home) . "</priority>";
                echo "<changefreq>" . esc_xml($frequency_home) . "</changefreq>";

                if ($xmlsbw_enable_include_last_mod_time) {
                    $lastmod_date_time_home = gmdate('c', strtotime($lastmod_date_time_home));
                    echo '<lastmod>' . esc_attr($lastmod_date_time_home) . '</lastmod>';
                }
                echo '</url>';
            }
            echo '</urlset>';
            exit;
        }

        /**
         * Main Sitemap Starts
         */
        header('Content-type: text/xml');
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<?xml-stylesheet type="text/xsl" href="' . esc_url(XMLSBW_URL) . 'sitemap.xsl"?>';
        echo '<sitemapindex  xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Sitemap for Home page
        echo "<sitemap>";
        echo "<loc>" . esc_url(home_url('/sitemap-misc.xml')) . "</loc>";
        if ($xmlsbw_enable_include_last_mod_time) {
            $lastmod_date_time_home = gmdate('c', strtotime($lastmod_date_time_home));
            echo '<lastmod>' . esc_attr($lastmod_date_time_home) . '</lastmod>';
        }
        echo "</sitemap>";

        // Sitemap for Author pages
        if ($xmlsbw_include_author_pages == 1) {
            echo "<sitemap>";
            echo "<loc>" . esc_url(home_url('/author-sitemap.xml')) . "</loc>";
            if ($xmlsbw_enable_include_last_mod_time) {
                $lastmod_date_time_home = gmdate('c', strtotime($lastmod_date_time_home));
                echo '<lastmod>' . esc_attr($lastmod_date_time_home) . '</lastmod>';
            }
            echo "</sitemap>";
        }

        // Sitemap for Additional Pages
        if(get_option( 'xmlsbw_enable_additional_pages')){
            if (isset($additional_pages_data['url']) && !empty(array_filter($additional_pages_data['url']))) {
                $most_recent_date_add_url = max($additional_pages_data['last_modified']);
                $most_recent_date_add_url_formatted = (new DateTime($most_recent_date_add_url, new DateTimeZone('UTC')))->format('Y-m-d\TH:i:s+00:00');

                echo '<sitemap>';
                echo '<loc>' . esc_url(get_site_url() . "/additional-sitemap.xml") . '</loc>';
                if ($xmlsbw_enable_include_last_mod_time) {
                    echo '<lastmod>' . esc_attr($most_recent_date_add_url_formatted) . '</lastmod>';
                }
                echo '</sitemap>';
            }
        }

        foreach ($post_types as $post_type) {

            $posts = get_posts(
                array(
                    'post_type' => $post_type,
                    'posts_per_page' => -1,
                )
            );

            $post_ids = wp_list_pluck($posts, 'ID');
            $filtered_post_ids = array_diff($post_ids, $excluded_post_ids);

            if (!empty($posts)) {

                $total_posts = count($filtered_post_ids);
                $total_sitemaps = ceil($total_posts / $links_per_sitemap);
                for ($i = 0; $i < $total_sitemaps; $i++) {
                    $start_index = $i * $links_per_sitemap;
                    $end_index = min(($i + 1) * $links_per_sitemap, $total_posts);
                    $current_posts = array_slice($posts, $start_index, $end_index - $start_index);

                    $post_modified_gmt_values_posts = array_map(function ($post) {
                        return $post->post_modified_gmt;
                    }, $current_posts);
                    $most_recent_date_post = max($post_modified_gmt_values_posts);
                    $datetime = new DateTime($most_recent_date_post);
                    $formatted_date = $datetime->format('Y-m-d\TH:i:s+00:00');

                    echo '<sitemap>';
                    if ($i != 0) {
                        echo '<loc>' . esc_url(get_site_url() . "/{$post_type}-sitemap-" . ($i + 1) . ".xml") . '</loc>';
                    } else {
                        echo '<loc>' . esc_url(get_site_url() . "/{$post_type}-sitemap.xml") . '</loc>';
                    }
                    if ($xmlsbw_enable_include_last_mod_time) {
                        echo '<lastmod>' . esc_attr($formatted_date) . '</lastmod>';
                    }
                    echo '</sitemap>';
                }
            }
            wp_reset_postdata();
        }

        // Sitemap for Taxonomy types
        foreach ($taxonomies as $taxonomy) {

            $post_modified_gmt_values_terms = array();

            $terms = get_terms(
                array(
                    'taxonomy' => $taxonomy,
                    'hide_empty' => false,
                )
            );

            $post_types = get_post_types(array('public' => true), 'names');

            foreach ($terms as $term) {
                $args = array(
                    'post_type' => $post_types,
                    'posts_per_page' => -1,
                    'orderby' => 'modified',
                    'order' => 'DESC',
                );

                $all_posts = get_posts($args);
                $filtered_posts = array();

                foreach ($all_posts as $post) {
                    $terms = wp_get_post_terms($post->ID, $taxonomy);
                    if (!empty($terms) && $terms[0]->term_id === $term->term_id) {
                        $filtered_posts[] = $post;
                    }
                }

                if (!empty($filtered_posts)) {
                    $post_modified_gmt_values_terms[] = $filtered_posts[0]->post_modified_gmt;
                }
            }



            $most_recent_date_taxonomy = !empty($post_modified_gmt_values_terms) ? max($post_modified_gmt_values_terms) : '';
            if (!empty($post_modified_gmt_values_terms)) {
                $most_recent_date_taxonomy = max($post_modified_gmt_values_terms);

                echo '<sitemap>';
                echo '<loc>' . esc_url(get_site_url() . "/{$taxonomy}-sitemap.xml") . '</loc>';
                if ($xmlsbw_enable_include_last_mod_time) {
                    echo '<lastmod>' . esc_attr(gmdate('Y-m-d\TH:i:s+00:00', strtotime($most_recent_date_taxonomy))) . '</lastmod>';
                }
                echo '</sitemap>';
            }
        }

        echo '</sitemapindex>';
        /**
         * Main Sitemap Ends
         */
        exit;
    }
}

function xmlsbw_exclude_xml_xsl_from_cache($can_cache)
{
    global $file_path_xml;
    if (strpos($file_path_xml, '.xml') !== false || strpos($file_path_xml, '.xsl') !== false) {
        return false;
    }
    return $can_cache;
}

?>