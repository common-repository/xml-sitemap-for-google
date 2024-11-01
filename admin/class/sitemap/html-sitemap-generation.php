<?php
// If this file is called directly, abort.
if (!defined('ABSPATH'))
    exit;

/**
 * Handles the HTML sitemap using shortcode.
 */
function xmlsbw_html_sitemap_shortcode_handler($atts)
{

    //Generates regular sitemap
    if (get_option('xmlsbw_enable_html_sitemap') == '1' && get_option('xmlsbw_compact_archives') != '1') {

        $sitemap_items = array();

        $xmlsbw_sort_order = get_option('xmlsbw_sort_order', 'title');
        $xmlsbw_sort_direction = get_option('xmlsbw_sort_direction', 'ASC');

        if (!(get_option('xmlsbw_include_all_post_type_html'))) {
            $post_types_html = get_option('xmlsbw_selected_post_types_html', array());
            $taxonomies_html = get_option('xmlsbw_selected_taxonomies_html', array());
        } else {
            $post_types_html = get_post_types(['public' => true], 'names');
            $taxonomies_to_exclude = array('post_format', 'product_type', 'product_visibility', 'product_shipping_class', 'pa_color', 'pa_size');
            $taxonomies_html = get_taxonomies(['public' => true], 'names');
            $taxonomies_html = array_diff($taxonomies_html, $taxonomies_to_exclude);
        }

        // Posts to be excluded
        $xmlsbw_excluded_posts_html = get_option('xmlsbw_excluded_posts_html', array());
        $excluded_posts_html_string = '';
        if (!empty($xmlsbw_excluded_posts_html)) {
            $excluded_posts_html_string = implode(',', $xmlsbw_excluded_posts_html);
        }
        $excluded_post_html_ids = array_map('trim', explode(',', $excluded_posts_html_string));
        $excluded_post_html_ids = get_option('xmlsbw_enable_advanced_settings_html') ? $excluded_post_html_ids : array();

        // Get all post types
        foreach ($post_types_html as $post_type_name) {
            $posts = get_posts(
                array(
                    'post_type' => $post_type_name,
                    'post_status' => 'publish',
                    'posts_per_page' => -1,
                    'orderby' => $xmlsbw_sort_order,
                    'order' => $xmlsbw_sort_direction
                )
            );
            foreach ($posts as $post) {
                if (in_array($post->ID, $excluded_post_html_ids)) {
                    continue; // Excluded post
                }
                $post_type_obj = get_post_type_object($post->post_type);
                $post_type_label = $post_type_obj->labels->name;
                $publication_date = get_the_date('F d,Y', $post);
                $sitemap_items[$post_type_label][] = array(
                    'title' => $post->post_title,
                    'url' => get_permalink($post->ID),
                    'publication_date' => $publication_date ? $publication_date : '',
                );
            }
        }

        // Terms to be excluded
        $xmlsbw_excluded_terms_html = get_option('xmlsbw_excluded_terms_html', array());
        $excluded_terms_html_string = '';
        if (!empty($xmlsbw_excluded_terms_html)) {
            $excluded_terms_html_string = implode(',', $xmlsbw_excluded_terms_html);
        }
        $excluded_term_html_ids = array_map('trim', explode(',', $excluded_terms_html_string));
        $excluded_term_html_ids = get_option('xmlsbw_enable_advanced_settings_html') ? $excluded_term_html_ids : array();

        // Get all taxonomies
        $taxonomies = get_taxonomies(array('public' => true, ), 'objects');
        foreach ($taxonomies as $taxonomy) {
            if (!in_array($taxonomy->name, $taxonomies_html)) {
                continue; // Skip taxonomies not in the selected list
            }
            $terms = get_terms(
                array(
                    'taxonomy' => $taxonomy->name,
                    'hide_empty' => false,
                    'orderby' => $xmlsbw_sort_order,
                    'order' => $xmlsbw_sort_direction
                )
            );
            foreach ($terms as $term) {
                if (in_array($term->term_id, $excluded_term_html_ids)) {
                    continue; // Excluded term
                }
                $sitemap_items[$taxonomy->label][] = array(
                    'title' => $term->name,
                    'url' => get_term_link($term),
                );
            }
        }

        // Generate HTML sitemap
        $html = '<ul class="html-sitemap">';
        foreach ($sitemap_items as $section => $items) {
            $html .= '<li>' . $section . '<ul>';
            foreach ($items as $item) {
                $html .= '<li><a style="color:black; text-decoration:none;" href="' . esc_url($item['url']) . '">' . $item['title'] . '</a>';
                if (get_option('xmlsbw_include_publication_date') == '1') {
                    if (!empty($item['publication_date'])) {
                        $html .= ' (' . $item['publication_date'] . ') ';
                    }
                    $html .= '</li>';
                }
            }
            $html .= '</ul></li>';
        }
        $html .= '</ul>';

        return $html;
    }
    //Generates compact date archives
    elseif (get_option('xmlsbw_enable_html_sitemap') == '1' && get_option('xmlsbw_compact_archives') == '1') {
        global $wpdb;

        $args = array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'fields' => 'ids',
            'posts_per_page' => -1,
            'orderby' => 'date',
            'order' => 'DESC',
        );

        $posts = get_posts($args);

        $years_with_posts = array();
        foreach ($posts as $post_id) {
            $post_year = get_post_field('post_date', $post_id);
            $year = gmdate('Y', strtotime($post_year));

            if (!in_array($year, $years_with_posts)) {
                $years_with_posts[] = $year;
            }
        }

        $html = '<ul class="compact-archive">';
        foreach ($years_with_posts as $year) {
            $year_link = get_year_link($year);
            $html .= '<li><strong><a href="' . esc_url($year_link) . '">' . $year . '</strong></a>: ';

            $month_links = [];
            for ($month = 1; $month <= 12; $month++) {
                $month_name = gmdate('F', mktime(0, 0, 0, $month, 1));
                $args = array(
                    'post_type'      => 'post',
                    'post_status'    => 'publish',
                    'year'           => $year,
                    'monthnum'       => $month,
                    'fields'         => 'ids',
                    'no_found_rows'  => true,
                );
                $query = new WP_Query($args);
                $month_has_posts = $query->post_count;
                if ($month_has_posts > 0) {
                    $month_links[] = '<a href="' . get_month_link($year, $month) . '">' . $month_name . '</a>';
                } else {
                    $month_links[] = $month_name;
                }
            }
            $html .= implode(' ', $month_links);
            $html .= '</li>';
        }
        $html .= '</ul>';
        return $html;
    }
    
}

/**
 * Handles the HTML sitemap for theme function.
 */
function show_html_sitemap()
{
    $html_sitemap_theme = do_shortcode('[show_html_sitemap]');
    add_filter('the_content', function ($content) use ($html_sitemap_theme) {
        return $content . $html_sitemap_theme;
    });
}

/**
 * Handles the HTML sitemap for particular URL.
 */
function xmlsbw_show_html_sitemap_url()
{
    $current_url = get_permalink();
    if ($current_url == get_option('xmlsbw_dedicated_url')) {
        // Get the HTML sitemap generated by the shortcode handler
        $html_sitemap = do_shortcode('[show_html_sitemap]');

        // Append the HTML sitemap to the content
        add_filter('the_content', function ($content) use ($html_sitemap) {
            return $content . $html_sitemap;
        });
    }
}


?>