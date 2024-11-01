<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) exit;
require_once ('header-footer.php');

/**
 * Callback function to search for posts and return results excluding specified posts.
 * @since 1.0.0
 */
function xmlsbw_search_posts_callback()
{
    $xmlsbw_excluded_posts = (array) get_option('xmlsbw_excluded_posts', array());
    $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
    if ( ! $nonce || ! wp_verify_nonce( $nonce, 'search_posts_nonce' ) ) {
    
        $term = sanitize_text_field($_GET['term']);
        $posts = get_posts(
            array(
                'post_type' => 'any',
                'posts_per_page' => -1,
                's' => $term,
                'post_status' => 'publish',
                'fields' => 'ids',

            )
        );

        $posts = array_diff($posts, $xmlsbw_excluded_posts);
        $results = array();
        foreach ($posts as $post_id) {
            $post_title = get_the_title($post_id);
            $results[] = array(
                'label' => $post_title,
                'value' => $post_id,
            );
        }
    }
    wp_send_json($results);
}

/**
 * Callback function to search for terms and return results excluding specified terms.
 * @since 1.0.0
 */
function xmlsbw_search_terms_callback()
{
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'search_terms_nonce' ) ) {

        $term = isset( $_GET['term'] ) ? sanitize_text_field( $_GET['term'] ) : '';

        $taxonomies = get_taxonomies(array('public' => true), 'objects');
        $all_terms = array();

        foreach ($taxonomies as $taxonomy) {
            $terms = get_terms(
                array(
                    'taxonomy' => $taxonomy->name,
                    'search' => $term,
                    'hide_empty' => false,
                    'fields' => 'id=>name',
                )
            );
            $all_terms = array_merge($all_terms, $terms);
        }

        $results = array();
        foreach ($all_terms as $term_id => $term_name) {
            $results[] = array(
                'label' => $term_name,
                'value' => $term_id,
            );
        }
    }

    wp_send_json($results);
}

/**
 * Render the settings form
 *  @since 1.0.0
 */
function xmlsbw_sitemap_settings_options()
{
    ?>
        <div class="wrap-xmlsbw">
            <div class="inner-xmlsbw">
                <div class="left-box-xmlsbw">
                    <h2>
                        <?php esc_html_e('XML Sitemap for Google - Configuration Settings', 'xml-sitemap-for-google'); ?>
                    </h2>
                    <?php settings_errors(); ?>
                    <h1 class="nav-tab-wrapper">
                        <a href="#general" class="nav-tab nav-tab-active" id="xml_tab">
                            <?php esc_html_e('XML Sitemap', 'xml-sitemap-for-google'); ?>
                        </a>
                        <a href="#html_sitemap" class="nav-tab" id="html_tab">
                            <?php esc_html_e('HTML Sitemap', 'xml-sitemap-for-google'); ?>
                        </a>
                    </h1>
                    <form method="post" action="" id="xmlsbw-sitemap-settings-form">
                        <table width="100%" style="margin-bottom: 10px;">
                            <tr>
                                <td>
                                    <div id="general" class="tab-content">
                                        <table>
                                            <tr id="enable_sitemap_generation_tr">
                                                <th>
                                                    <?php esc_html_e('Enable Sitemap Generation:', 'xml-sitemap-for-google'); ?>
                                                </th>
                                                <td>
                                                    <div class="toggle-switch">
                                                        <input type="hidden" name="xmlsbw_enable_sitemap_generation" value="0">
                                                        <input type="checkbox" id="xmlsbw_enable_sitemap_generation" name="xmlsbw_enable_sitemap_generation" value="1" <?php checked(get_option('xmlsbw_enable_sitemap_generation'), 1); ?>>
                                                        <label for="xmlsbw_enable_sitemap_generation" class="toggle"></label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr id="rename_sitemap_url_tr" class="pro-banner">
                                                <th id="rename_sitemap_url_th">
                                                    <?php esc_html_e('Rename Sitemap URL:', 'xml-sitemap-for-google'); ?>
                                                </th>
                                                <?php
                                                if(get_option('premium_access_allowed') == 1){
                                                    ?>
                                                    <td id="rename-td">
                                                        <input type="text" id="xmlsbw_sitemap_url" name="xmlsbw_sitemap_url"
                                                        value="<?php echo esc_attr( (get_option('xmlsbw_sitemap_url') != "") ? get_option('xmlsbw_sitemap_url') : 'sitemap' ); ?>">.xml
                                                        <p class="description">
                                                            <?php esc_html_e('Organize sitemap entries into distinct files in your sitemap.', 'xml-sitemap-for-google'); ?>
                                                        </p>
                                                    </td>
                                                    <?php
                                                }else{
                                                    ?>
                                                    <td id="pro-rename-td" class="hide-show">
                                                        <div class="pro-ribbon-banner">
                                                            <div class="file-input">
                                                                <input type="text" id="xmlsbw_sitemap_url" name="xmlsbw_sitemap_url"
                                                                value="sitemap" disabled>
                                                                <span class="extension">.xml</span>
                                                            </div>                                                        
                                                            <p class="description">
                                                                <?php esc_html_e('Replace the default sitemap URL with a custom one that reflects your brand.', 'xml-sitemap-for-google'); ?>
                                                            </p>
                                                            <div class="ribbon">
                                                                <svg width="167" height="167" viewBox="0 0 167 167" fill="none">
                                                                    <path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
                                                                    <path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
                                                                    <path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
                                                                    <path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
                                                                    <defs>
                                                                    <linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
                                                                    <stop stop-color="#FDAB00"/>
                                                                    <stop offset="1" stop-color="#CD8F0D"/>
                                                                    </linearGradient>
                                                                    <linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
                                                                    <stop stop-color="#FDAB00"/>
                                                                    <stop offset="1" stop-color="#CD8F0D"/>
                                                                    </linearGradient>
                                                                    </defs>
                                                                </svg>
                                                            </div>
                                                            <div class="learn-more">
                                                                <a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium">Upgrade to Premium</a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                <?php
                                                }
                                                ?>
                                            </tr>
                                            <tr id="links_per_sitemap_tr" class="pro-banner">
                                                <th id="links_per_sitemap_th">
                                                    <?php esc_html_e('Links Per Sitemaps:', 'xml-sitemap-for-google'); ?>
                                                </th>
                                                <?php
                                                if(get_option('premium_access_allowed') == 1){
                                                    ?>
                                                    <td>
                                                        <input type="number" id="xmlsbw_links_per_sitemap" name="xmlsbw_links_per_sitemap"
                                                        
                                                        value="<?php echo esc_attr( (get_option('xmlsbw_links_per_sitemap') != "") ? get_option('xmlsbw_links_per_sitemap') : '1000' ); ?>"
                                                        >
                                                        <p class="description">
                                                            <?php esc_html_e('You can specify the maximum number of links in a sitemap and organize entries into distinct files. Enable this if your sitemap has over 1,000 URLs.', 'xml-sitemap-for-google'); ?>
                                                        </p>
                                                    </td>
                                                <?php
                                                }else{
                                                    ?>
                                                    <td id="pro-enables-indices-td" class="hide-show">
                                                    <div class="pro-ribbon-banner">
                                                            <div class="file-input">
                                                                <input type="number" id="" name="" value="1000" disabled>
                                                            </div>                                                        
                                                            <p class="description">
                                                                <?php esc_html_e('You can specify the maximum number of links in a sitemap and organize entries into distinct files. Enable this if your sitemap has over 1,000 URLs.', 'xml-sitemap-for-google'); ?>
                                                            </p>
                                                            <div class="ribbon">
                                                                <svg width="167" height="167" viewBox="0 0 167 167" fill="none">
                                                                    <path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
                                                                    <path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
                                                                    <path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
                                                                    <path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
                                                                    <defs>
                                                                    <linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
                                                                    <stop stop-color="#FDAB00"/>
                                                                    <stop offset="1" stop-color="#CD8F0D"/>
                                                                    </linearGradient>
                                                                    <linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
                                                                    <stop stop-color="#FDAB00"/>
                                                                    <stop offset="1" stop-color="#CD8F0D"/>
                                                                    </linearGradient>
                                                                    </defs>
                                                                </svg>
                                                            </div>
                                                            <div class="learn-more">
                                                                <a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium">Upgrade to Premium</a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                <?php
                                                }
                                                ?>
                                            </tr>
                                            <tr id="select_post_type_tr">
                                                <th>
                                                    <?php esc_html_e('Configure Post & Taxonomy types:', 'xml-sitemap-for-google'); ?>
                                                </th>
                                                <td style="width:923px;">
                                                <input id="xmlsbw_include_all_post_type" type='checkbox' name="xmlsbw_include_all_post_type" value="1" <?php checked(get_option('xmlsbw_include_all_post_type'), 1); ?>>
                                                <label><?php esc_html_e('Include All Post Types & Taxonomy Types', 'xml-sitemap-for-google'); ?>
                                                </label>
                                                    <div class="select-post-type">
                                                        <div class="post-type-wrapper">
                                                            <?php
                                                            $all_post_types = get_post_types(['public' => true], 'objects');
                                                            foreach ($all_post_types as $post_type) {
                                                                $post_type_name = esc_attr($post_type->name);
                                                                $post_type_label = esc_html($post_type->label);
                                                                echo '<a href="#' . esc_attr($post_type_name) . '" class="post-type-tab">' . esc_html($post_type_label, 'xml-sitemap-for-google') . '</a>';
                                                            }
                                                            ?>
                                                        </div>
                                                        <?php
                                                        $include_all_post_type = get_option( 'xmlsbw_include_all_post_type' );
                                                        $xmlsbw_selected_post_types = get_option('xmlsbw_selected_post_types', array());
                                                        foreach ($all_post_types as $post_type) {
                                                            // Post type 
                                                            if($include_all_post_type){
                                                                $checked = 'checked';
                                                                $disabled = 'disabled';
                                                            } else{
                                                                $checked = in_array($post_type->name, $xmlsbw_selected_post_types) ? 'checked' : '';
                                                                $disabled ='';
                                                            }

                                                            echo '<div id="' . esc_attr($post_type->name) . '" class="tab-content-post-type">';

                                                            echo "<div class='post-type'>";
                                                            echo "<div class='post-type-div'>";
                                                            echo "<h4>". esc_html_e('Select post type:', 'xml-sitemap-for-google') . "</h4>";
                                                            echo '<input type="checkbox" class="post-type-class" name="post_types[]" value="' . esc_attr($post_type->name) . '" ' . esc_attr($checked) . ' id="post_type_' . esc_attr($post_type->name) . '" style="margin:10px;" ' . esc_attr($disabled) . '>';
                                                            echo '<label for="post_type_' . esc_attr($post_type->name) . '">' . esc_html($post_type->label) . '</label>';
                                                            echo "</div>";

                                                            // Priority Dropdown for post type
                                                            echo "<div class='post-type-priority-div'>";
                                                            echo "<h4>". esc_html_e('Priority:', 'xml-sitemap-for-google') . "</h4>";
                                                            echo '<select name="xmlsbw_post_priority[' . esc_attr($post_type->name) . ']" id="priority-' . esc_attr($post_type->name) . '">';
                                                            $priority_options = array("0", "0.1", "0.2", "0.3", "0.4", "0.5", "0.6", "0.7", "0.8", "0.9", "1");
                                                            foreach ($priority_options as $priority_option) {
                                                                $selected = (get_option('xmlsbw_post_priority')[$post_type->name] == $priority_option) ? 'selected' : '';
                                                                echo '<option value="' . esc_attr($priority_option) . '" ' . esc_attr($selected) . '>' . esc_html($priority_option) . '</option>';
                                                            }
                                                            echo "</select>";
                                                            echo "</div>";

                                                            // Frequency Dropdown for post type
                                                            echo "<div class='post-type-frequency-div'>";
                                                            echo "<h4>". esc_html_e('Frequency:', 'xml-sitemap-for-google') . "</h4>";
                                                            echo '<select name="xmlsbw_post_frequency[' . esc_attr($post_type->name) . ']" id="frequency-' . esc_attr($post_type->name) . '">';
                                                            $frequency_options = array("always", "hourly", "daily", "weekly", "monthly", "yearly", "never");
                                                            foreach ($frequency_options as $frequency_option) {
                                                                $selected = (get_option('xmlsbw_post_frequency')[$post_type->name] == $frequency_option) ? 'selected' : '';
                                                                echo '<option value="' . esc_attr($frequency_option) . '" ' . esc_attr($selected) . '>' . esc_html(ucwords($frequency_option)) . '</option>';

                                                            }
                                                            echo "</select>";
                                                            echo "</div>";

                                                            echo "</div>";

                                                            // Taxonomies
                                                            $taxonomies = get_object_taxonomies($post_type->name, 'objects');
                                                            $taxonomies_to_be_excluded = array('post_format', 'product_type', 'product_visibility', 'product_shipping_class', 'pa_color', 'pa_size','post_translations','language');
                                                            $taxonomies = array_filter($taxonomies, function($taxonomy) use ($taxonomies_to_be_excluded) {
                                                                return !in_array($taxonomy->name, $taxonomies_to_be_excluded);
                                                            });

                                                            $taxonomies = array_values($taxonomies);
                                                            $xmlsbw_selected_taxonomies = get_option('xmlsbw_selected_taxonomies', array());
                                                            if ($taxonomies) {
                                                                echo "<hr/>";
                                                                echo "<h4>". esc_html_e('Select taxonomy type:', 'xml-sitemap-for-google') . "</h4>";
                                                                foreach ($taxonomies as $taxonomy) {
                                                                    echo "<div class='taxonomy-type'>";
                                                                    echo "<div class='taxonomy-type-div'>";
                                                                    if($include_all_post_type){
                                                                        $checked = 'checked';
                                                                        $disabled = 'disabled';
                                                                    } else{
                                                                        $checked = in_array($taxonomy->name, $xmlsbw_selected_taxonomies) ? 'checked' : '';
                                                                        $disabled ='';
                                                                    }
                                                                    
                                                                    echo '<input type="checkbox" class="taxonomy-type-class" name="taxonomies[]" value="' . esc_attr($taxonomy->name) . '" ' . esc_attr($checked) . ' style="margin:10px;" ' . esc_attr($disabled) . '>';
                                                                    echo '<label>' . esc_html($taxonomy->label) . '</label>';
                                                                    echo "</div>";

                                                                    echo "<div class='taxonomy-type-priority-div'>";
                                                                    // Priority Dropdown for taxonomy type
                                                                    echo '<select name="xmlsbw_taxonomy_priority[' . esc_attr($taxonomy->name) . ']" id="priority-' . esc_attr($taxonomy->name) . '">';
                                                                    $priority_options = array("0", "0.1", "0.2", "0.3", "0.4", "0.5", "0.6", "0.7", "0.8", "0.9", "1");
                                                                    foreach ($priority_options as $priority_option) {
                                                                        $selected = (get_option('xmlsbw_taxonomy_priority')[$taxonomy->name] == $priority_option) ? 'selected' : '';
                                                                        echo '<option value="' . esc_attr($priority_option) . '" ' . esc_attr($selected) . '>' . esc_html($priority_option) . '</option>';
                                                                    }
                                                                    echo "</select>";
                                                                    echo "</div>";

                                                                    echo "<div class='taxonomy-type-frequency-div'>";
                                                                    // Frequency Dropdown for taxonomy type
                                                                    echo '<select name="xmlsbw_taxonomy_frequency[' . esc_attr($taxonomy->name) . ']" id="frequency-' . esc_attr($taxonomy->name) . '">';
                                                                    $frequency_options = array("always", "hourly", "daily", "weekly", "monthly", "yearly", "never");
                                                                    foreach ($frequency_options as $frequency_option) {
                                                                        $selected = (get_option('xmlsbw_taxonomy_frequency')[$taxonomy->name] == $frequency_option) ? 'selected' : '';
                                                                        echo '<option value="' . esc_attr($frequency_option) . '" ' . esc_attr($selected) . '>' . esc_html(ucwords($frequency_option)) . '</option>';
                                                                    }
                                                                    echo "</select>";
                                                                    echo "</div>";
                                                                    echo "</div>";
                                                                }
                                                            }
                                                            echo "</div>";
                                                        }
                                                        ?>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr id="select_archive_page_tr">
                                                <th>
                                                    <?php esc_html_e('Configure Archive Pages:', 'xml-sitemap-for-google'); ?>
                                                </th>
                                                <td style="width:923px;">

                                                    <div class="post-type-wrapper">
                                                        <a href='#home' class='archive-type-tab'><?php esc_html_e('Home', 'xml-sitemap-for-google'); ?></a>
                                                        <a href='#author' class='archive-type-tab'> <?php esc_html_e('Author', 'xml-sitemap-for-google'); ?></a>
                                                    </div>

                                                    <div id='home' class='tab-content-archive-page'>

                                                        <div class='archive-page'>

                                                            <div class='archive-page-home-div'>
                                                                <h4>
                                                                    <?php esc_html_e('Archive Page Type', 'xml-sitemap-for-google'); ?>
                                                                </h4>
                                                                <input type="checkbox" id="xmlsbw_include_home_page" name="xmlsbw_include_home_page"
                                                                    value="1" <?php checked(get_option('xmlsbw_include_home_page'), 1); ?>>
                                                                <label>
                                                                    <?php esc_html_e('Include Home Page', 'xml-sitemap-for-google'); ?>
                                                                </label>
                                                            </div>

                                                            <!-- Priority Dropdown for home archive-page -->
                                                            <div class='archive-page-home-priority-div'>
                                                                <h4><?php esc_html_e('Priority', 'xml-sitemap-for-google'); ?></h4>
                                                                <select name='xmlsbw_post_priority[home]' id='priority-home'>
                                                                    <?php
                                                                    $priority_options = array("0", "0.1", "0.2", "0.3", "0.4", "0.5", "0.6", "0.7", "0.8", "0.9", "1");
                                                                    foreach ($priority_options as $priority_option) {
                                                                        $selected = (get_option('xmlsbw_post_priority')['home'] == $priority_option) ? 'selected' : '';
                                                                        echo '<option value="' . esc_attr($priority_option) . '" ' . esc_attr($selected) . '>' . esc_html(ucwords($priority_option)) . '</option>';
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>

                                                            <!-- Frequency Dropdown for home archive-page -->
                                                            <div class='archive-page-home-frequency-div'>
                                                                <h4><?php esc_html_e('Frequency', 'xml-sitemap-for-google'); ?></h4>
                                                                <select name='xmlsbw_post_frequency[home]' id='frequency-home'>
                                                                    <?php
                                                                    $frequency_options = array("always", "hourly", "daily", "weekly", "monthly", "yearly", "never");
                                                                    foreach ($frequency_options as $frequency_option) {
                                                                        $selected = (get_option('xmlsbw_post_frequency')['home'] == $frequency_option) ? 'selected' : '';
                                                                        echo '<option value="' . esc_attr($frequency_option) . '" ' . esc_attr($selected) . '>' . esc_html(ucwords($frequency_option)) . '</option>';

                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div id='author' class='tab-content-archive-page'>

                                                        <div class='archive-page'>

                                                            <div class='archive-page-author-div'>
                                                                <h4>
                                                                    <?php esc_html_e('Archive Page Type', 'xml-sitemap-for-google'); ?>
                                                                </h4>
                                                                <input type="checkbox" id="xmlsbw_include_author_pages"
                                                                    name="xmlsbw_include_author_pages" value="1" <?php checked(get_option('xmlsbw_include_author_pages'), 1); ?>>
                                                                <label>
                                                                    <?php esc_html_e('Include Author Page', 'xml-sitemap-for-google'); ?>
                                                                </label>
                                                            </div>

                                                            <!-- Priority Dropdown for author archive-page -->
                                                            <div class='archive-page-author-priority-div'>
                                                                <h4><?php esc_html_e('Priority', 'xml-sitemap-for-google'); ?></h4>
                                                                <select name='xmlsbw_post_priority[author]' id='priority-author'>
                                                                    <?php
                                                                    $priority_options = array("0", "0.1", "0.2", "0.3", "0.4", "0.5", "0.6", "0.7", "0.8", "0.9", "1");
                                                                    foreach ($priority_options as $priority_option) {
                                                                        $selected = (get_option('xmlsbw_post_priority')['author'] == $priority_option) ? 'selected' : '';
                                                                        echo '<option value="' . esc_attr($priority_option) . '" ' . esc_attr($selected) . '>' . esc_html(ucwords($priority_option)) . '</option>';
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>

                                                            <!-- Frequency Dropdown for author archive-page -->
                                                            <div class='archive-page-author-frequency-div'>
                                                                <h4><?php esc_html_e('Frequency', 'xml-sitemap-for-google'); ?></h4>
                                                                <select name='xmlsbw_post_frequency[author]' id='frequency-author'>
                                                                    <?php
                                                                    $frequency_options = array("always", "hourly", "daily", "weekly", "monthly", "yearly", "never");
                                                                    foreach ($frequency_options as $frequency_option) {
                                                                        $selected = (get_option('xmlsbw_post_frequency')['author'] == $frequency_option) ? 'selected' : '';
                                                                        echo '<option value="' . esc_attr($frequency_option) . '" ' . esc_attr($selected) . '>' . esc_html(ucwords($frequency_option)) . '</option>';
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>

                                                        </div>

                                                    </div>

                                                </td>
                                            </tr>
                                            <tr id="include_last_mod_time_tr">
                                                <th>
                                                    <?php esc_html_e('Include Last Modification Time:', 'xml-sitemap-for-google'); ?>
                                                </th>
                                                <td class="include_last_mod_time_div">
                                                    <div class="toggle-switch">
                                                        <input type="hidden" name="xmlsbw_enable_include_last_mod_time" value="0">
                                                        <input type="checkbox" id="xmlsbw_enable_include_last_mod_time" name="xmlsbw_enable_include_last_mod_time" value="1" <?php checked(get_option('xmlsbw_enable_include_last_mod_time'), 1); ?>>
                                                        <label for="xmlsbw_enable_include_last_mod_time" class="toggle"></label>
                                                    </div>
                                                    <p class="description"><?php esc_html_e('This is highly recommended and helps the search engines to know when your content has changed.', 'xml-sitemap-for-google'); ?></p>
                                                </td>
                                            </tr>
                                            <tr id="post_priority_calculation_tr">
                                                <th>
                                                    <?php esc_html_e('Post Priority Calculation:', 'xml-sitemap-for-google'); ?>
                                                </th>
                                                <td>
                                                    <div style="margin-bottom: 10px;">
                                                    <input type="radio" id="no_calculation" name="xmlsbw_post_priority_calculation" value="no_calculation" <?php if (empty(get_option('xmlsbw_post_priority_calculation')) || get_option('xmlsbw_post_priority_calculation') == 'no_calculation') echo 'checked';?>>
                                                        <label for="no_calculation"><?php esc_html_e('Do not use automatic priority calculation', 'xml-sitemap-for-google'); ?></label>
                                                    </div>
                                                    <div style="margin-bottom: 10px;">
                                                        <input type="radio" id="comment_count" name="xmlsbw_post_priority_calculation" value="comment_count" <?php if (get_option('xmlsbw_post_priority_calculation') == 'comment_count') echo 'checked';?>>
                                                        <label for="comment_count"><?php esc_html_e('Comment Count', 'xml-sitemap-for-google'); ?></label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr id = "enable_advanced_settings_tr">
                                                <th>
                                                    <?php esc_html_e('Advanced Settings:', 'xml-sitemap-for-google'); ?>
                                                </th>
                                                <td>
                                                    <div class="toggle-switch">
                                                        <input type="hidden" name="c" value="0">
                                                        <input type="checkbox" id="xmlsbw_enable_advanced_settings" name="xmlsbw_enable_advanced_settings" value="1" <?php checked(get_option('xmlsbw_enable_advanced_settings'), 1); ?>>
                                                        <label for="xmlsbw_enable_advanced_settings" class="toggle"></label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr id = "exclude_posts_tr">
                                                <th style="text-align:center;">
                                                    <?php esc_html_e('Exclude Posts:', 'xml-sitemap-for-google'); ?>
                                                </th>
                                                <td>
                                                    <?php
                                                    $xmlsbw_excluded_posts = (array) get_option('xmlsbw_excluded_posts', array());
                                                    ?>
                                                    <input style="width: 50%" type="text" id="excluded_posts_input"
                                                        name="excluded_posts_input" placeholder='Begin typing post title to search'
                                                        value="">
                                                    <p class="description">
                                                        <?php esc_html_e('Provide the names of the posts you want to exclude.', 'xml-sitemap-for-google'); ?>
                                                    </p>
                                                    <div class="excluded_posts_container" id="excluded_posts_container">
                                                        <?php foreach ($xmlsbw_excluded_posts as $post_id) { ?>
                                                            <?php $post = get_post($post_id); ?>
                                                            <?php if ($post) { ?>
                                                                <div class="excluded-post">
                                                                    <span>
                                                                        <?php echo esc_html($post->post_title); ?>
                                                                    </span>
                                                                    <input type="hidden" name="xmlsbw_excluded_posts[]" value="<?php echo esc_attr($post_id); ?>">
                                                                    <button type="button" class="remove-excluded-post">X</button>
                                                                </div>
                                                                
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </div> 
                                                </td>
                                            </tr>
                                            <tr id = "exclude_terms_tr">
                                                <th style="text-align:center;">
                                                    <?php esc_html_e('Exclude Terms:', 'xml-sitemap-for-google'); ?>
                                                </th>
                                                <td>
                                                    <?php
                                                    $xmlsbw_excluded_terms = (array) get_option('xmlsbw_excluded_terms', array());
                                                    $taxonomies = get_taxonomies(array(), 'objects');
                                                    ?>
                                                    <input style="width: 50%;" type="text" id="excluded_terms_input"
                                                        name="excluded_terms_input" placeholder='Begin typing term title to search'
                                                        value="">
                                                    <br><br>
                                                    <p class="description">
                                                        <?php esc_html_e('Provide the names of the terms you want to exclude.', 'xml-sitemap-for-google'); ?>

                                                    </p>
                                                    <div class="excluded_terms_container" id="excluded_terms_container">
                                                        <?php foreach ($xmlsbw_excluded_terms as $term_id): ?>
                                                            <?php foreach ($taxonomies as $taxonomy): ?>
                                                                <?php $term = get_term_by('id', $term_id, $taxonomy->name); ?>
                                                                <?php if ($term): ?>
                                                                    <div class="excluded-term">
                                                                        <span>
                                                                            <?php echo esc_html($term->name); ?>
                                                                        </span>
                                                                        <input type="hidden" name="xmlsbw_excluded_terms[]"
                                                                            value="<?php echo esc_html($term_id); ?>">
                                                                        <button type="button" class="remove-excluded-term">X</button>
                                                                    </div>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr id = "enable_additional_pages_tr" class="pro-banner">
                                                <th id="enable_additional_pages_th">
                                                    <?php esc_html_e('Additional Pages:', 'xml-sitemap-for-google'); ?>
                                                </th>
                                                <?php
                                                if(get_option('premium_access_allowed') == 1){
                                                    ?>
                                                    <td>
                                                        <div class="toggle-switch">
                                                            <input type="hidden" name="xmlsbw_enable_additional_pages" value="0">
                                                            <input type="checkbox" id="xmlsbw_enable_additional_pages" name="xmlsbw_enable_additional_pages" value="1" <?php checked(get_option('xmlsbw_enable_additional_pages'), 1); ?>>
                                                            <label for="xmlsbw_enable_additional_pages" class="toggle"></label>
                                                        </div>
                                                    </td>
                                                    <?php
                                                }else{
                                                    ?>
                                                    <td class="hide-show">
                                                        <div class="pro-ribbon-banner">
                                                            <div class="toggle-switch">
                                                                <input type="checkbox">
                                                                <label class="toggle"></label>
                                                            </div>
                                                            <div class="ribbon">
                                                                <svg width="167" height="167" viewBox="0 0 167 167" fill="none">
                                                                    <path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
                                                                    <path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
                                                                    <path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
                                                                    <path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
                                                                    <defs>
                                                                    <linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
                                                                    <stop stop-color="#FDAB00"/>
                                                                    <stop offset="1" stop-color="#CD8F0D"/>
                                                                    </linearGradient>
                                                                    <linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
                                                                    <stop stop-color="#FDAB00"/>
                                                                    <stop offset="1" stop-color="#CD8F0D"/>
                                                                    </linearGradient>
                                                                    </defs>
                                                                </svg>
                                                            </div>
                                                            <div class="learn-more">
                                                                <a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium">Upgrade to Premium</a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                <?php
                                                }
                                                ?>
                                            </tr>
                                            <tr id = "additional_pages_tr">
                                                <td colspan = "2" style="width:100%;">
                                                    <div>
                                                        <?php
                                                            $additional_pages_data = get_option('xmlsbw_additional_pages_data', array());
                                                            if (!empty ($additional_pages_data)) { ?>
                                                                <select class="bulk-actions">
                                                                    <option value="none"><?php esc_html_e('Bulk Action', 'xml-sitemap-for-google'); ?></option>
                                                                    <option value="delete-selected"><?php esc_html_e('Delete', 'xml-sitemap-for-google'); ?></option>
                                                                </select>
                                                                <button type="button" class="button button-secondary apply-btn"><?php esc_html_e('Apply', 'xml-sitemap-for-google'); ?></button>
                                                                <input type="text" class="search-box" placeholder="Search" style="margin-left: -176px;">
                                                        <?php } ?>
                                                        <br><br>
                                                        <div class="additional-pages-div">
                                                            <table id="additional-pages-table"  class="row-border" width="100%">
                                                                <th style="width:10px;"><input type="checkbox" id="check-all"></th>
                                                                <th style="width:600px;"><?php esc_html_e('Page URL', 'xml-sitemap-for-google'); ?></th>
                                                                <th style="width:10px;"><?php esc_html_e('Priority', 'xml-sitemap-for-google'); ?></th>
                                                                <th style="width:10px;"><?php esc_html_e('Frequency', 'xml-sitemap-for-google'); ?></th>
                                                                <th><?php esc_html_e('Last Modified', 'xml-sitemap-for-google'); ?></th>
                                                                <th style="width:10px;"><?php esc_html_e('Action', 'xml-sitemap-for-google'); ?></th>
                                                                <?php
                                                                if (isset($additional_pages_data['url']) && is_array($additional_pages_data['url'])) {
                                                                    $count_id = 0;
                                                                    foreach ($additional_pages_data['url'] as $index => $url) {
                                                                        $priority = isset($additional_pages_data['priority'][$index]) ? $additional_pages_data['priority'][$index] : '';
                                                                        $frequency = isset($additional_pages_data['frequency'][$index]) ? $additional_pages_data['frequency'][$index] : '';
                                                                        $last_modified = isset($additional_pages_data['last_modified'][$index]) ? $additional_pages_data['last_modified'][$index] : '';
                                                                        ?>
                                                                        <tr class="additional-page-row">
                                                                            <td style="text-align: center;">
                                                                                <input type="checkbox" class="delete-checkbox">
                                                                            </td>
                                                                            <td><input style="width:100%; text-align:center;" type="text" name="additional_pages[url][]"
                                                                                    class="additional-url" value="<?php echo esc_attr($url); ?>"></td>
                                                                            <td>
                                                                                <select name="additional_pages[priority][]">
                                                                                    <?php
                                                                                    $priority_options = array("0", "0.1", "0.2", "0.3", "0.4", "0.5", "0.6", "0.7", "0.8", "0.9", "1");
                                                                                    foreach ($priority_options as $priority_option) {
                                                                                        $selected = ($priority == $priority_option) ? 'selected' : '';
                                                                                        echo '<option value="' . esc_attr($priority_option) . '" ' . ($selected ? 'selected' : '') . '>' . esc_html(ucwords($priority_option)) . '</option>';
                                                                                    }
                                                                                    ?>
                                                                                </select>
                                                                            </td>
                                                                            <td>
                                                                                <select name="additional_pages[frequency][]">
                                                                                    <?php
                                                                                    $frequency_options = array("always", "hourly", "daily", "weekly", "monthly", "yearly", "never");
                                                                                    foreach ($frequency_options as $frequency_option) {
                                                                                        $selected = ($frequency == $frequency_option) ? 'selected' : '';
                                                                                        echo '<option value="' . esc_attr($frequency_option) . '" ' . ($selected ? 'selected' : '') . '>' . esc_html(ucwords($frequency_option)) . '</option>';
                                                                                    }
                                                                                    ?>
                                                                                </select>
                                                                            </td>
                                                                            <td>
                                                                                <input type="text" name="additional_pages[last_modified][]" placeholder="" id="utc-time-<?php echo esc_attr($count_id); ?>"
                                                                                    class="additional-last-modified" readonly
                                                                                    value="<?php echo esc_attr($last_modified); ?>">
                                                                            </td>
                                                                            <td>
                                                                                <button type="button" class="button button-secondary delete-row" id='delete-row'>
                                                                                    <img src="<?php echo esc_url(XMLSBW_URL . 'admin/assets/images/delete.png'); ?>" alt="delete" title="Delete">
                                                                                </button>
                                                                            </td>
                                                                        </tr>
                                                                        <?php
                                                                        $count_id++;
                                                                    }
                                                                }
                                                                ?>
                                                            </table>
                                                        </div>
                                                        <button type="button" id="add-new-page" class="button button-secondary add-new"><?php esc_html_e('Add New', 'xml-sitemap-for-google'); ?></button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                                <td>
                                    <div id="html_sitemap" class="tab-content" style="display: none">
                                        <table>
                                            <tr id = "enable_html_sitemap_tr">
                                                <th>
                                                    <?php esc_html_e('Enable HTML Sitemap:', 'xml-sitemap-for-google'); ?>
                                                </th>
                                                <td>
                                                    <div class="toggle-switch">
                                                        <input type="hidden" name="xmlsbw_enable_html_sitemap" value="0">
                                                        <input type="checkbox" id="xmlsbw_enable_html_sitemap" name="xmlsbw_enable_html_sitemap" value="1" <?php checked(get_option('xmlsbw_enable_html_sitemap'), 1); ?>>
                                                        <label for="xmlsbw_enable_html_sitemap" class="toggle"></label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr id="display_html_sitemap_tr">
                                                <th>
                                                    <?php esc_html_e('Display HTML Sitemap:', 'xml-sitemap-for-google'); ?>
                                                </th>
                                                <td style="width:697px;">
                                                    <div class="html-sitemap-wrapper">
                                                        <a href='#shortcode' class='html-sitemap-block-tab'>
                                                            <?php esc_html_e('Shortcode', 'xml-sitemap-for-google'); ?> 
                                                        </a>
                                                        <a href='#block' class='html-sitemap-block-tab'>
                                                            <?php esc_html_e('Gutenberg Block', 'xml-sitemap-for-google'); ?>
                                                        </a>
                                                        <a href='#widget' class='html-sitemap-block-tab'>
                                                            <?php esc_html_e('Widget', 'xml-sitemap-for-google'); ?>
                                                        </a>
                                                        <a href='#php_code' class='html-sitemap-block-tab'>
                                                            <?php esc_html_e('PHP Code', 'xml-sitemap-for-google'); ?>
                                                        </a>
                                                        <a href='#dedicate_url' class='html-sitemap-block-tab'>
                                                            <?php esc_html_e('Dedicated URL', 'xml-sitemap-for-google'); ?>
                                                        </a>
                                                    </div>

                                                    <div id='shortcode' class='tab-content-html-sitemap'>
                                                        <div class='html-sitemap-page'>
                                                            <div class="copy-short">
                                                                <div class="code">
                                                                    <code>[show_html_sitemap]</code>
                                                                </div>
                                                                <div class="copy-shortcode" id="copy-shortcode">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="20" viewBox="0 0 19 20" fill="none">
                                                                    <path d="M3.4375 19H11.5625C12.9066 19 14 18.058 14 16.9V7.1C14 5.94199 12.9066 5 11.5625 5H3.4375C2.09338 5 1 5.94199 1 7.1V16.9C1 18.058 2.09338 19 3.4375 19ZM2.625 7.1C2.625 6.71411 2.9892 6.4 3.4375 6.4H11.5625C12.0108 6.4 12.375 6.71411 12.375 7.1V16.9C12.375 17.2859 12.0108 17.6 11.5625 17.6H3.4375C2.9892 17.6 2.625 17.2859 2.625 16.9V7.1Z" fill="#8D8D8D"/>
                                                                    <path d="M18.2483 12.586V4.02769C18.2483 2.61189 17.0966 1.46021 15.6808 1.46021H7.12249C6.64986 1.46021 6.26666 1.84341 6.26666 2.31603C6.26666 2.78866 6.64986 3.17186 7.12249 3.17186H15.6808C16.153 3.17186 16.5366 3.5559 16.5366 4.02769V12.586C16.5366 13.0586 16.9198 13.4418 17.3924 13.4418C17.8651 13.4418 18.2483 13.0586 18.2483 12.586Z" fill="#8D8D8D"/>
                                                                    </svg>
                                                                </div>  
                                                            </div>
                                                            
                                                        </div>                                                                                                                 
                                                        <p class="description">
                                                                <?php esc_html_e('Use above shortcode to display the HTML Sitemap on required page.', 'xml-sitemap-for-google'); ?>
                                                            </p>
                                                    </div>

                                                    <div id='block' class='tab-content-html-sitemap'>
                                                        <div class='html-sitemap-page'>
                                                            <p><?php esc_html_e('To include this block, edit a page or post and look for the "HTML Sitemap Block".', 'xml-sitemap-for-google'); ?></p>
                                                        </div>
                                                    </div>

                                                    <div id='widget' class='tab-content-html-sitemap'>
                                                        <div class='html-sitemap-page'>
                                                            <p>To add this widget, visit the <a href="<?php home_url() ?>/wp-admin/widgets.php" target ="_blank">widgets page</a> and look for the "HTML Sitemap Widget".</p>
                                                        </div>
                                                    </div>

                                                    <div id='php_code' class='tab-content-html-sitemap'>
                                                        <div class='html-sitemap-page'>
                                                            <div class="copy-php">
                                                                <div class="php-code">
                                                                    <p class="sitemap-php-code">&lt;?php if( function_exists( 'show_html_sitemap' ) ) show_html_sitemap(); ?&gt;</p>
                                                                </div>                                                                
                                                                <div class="copy-phpcode" id="copy-phpcode">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="20" viewBox="0 0 19 20" fill="none">
                                                                    <path d="M3.4375 19H11.5625C12.9066 19 14 18.058 14 16.9V7.1C14 5.94199 12.9066 5 11.5625 5H3.4375C2.09338 5 1 5.94199 1 7.1V16.9C1 18.058 2.09338 19 3.4375 19ZM2.625 7.1C2.625 6.71411 2.9892 6.4 3.4375 6.4H11.5625C12.0108 6.4 12.375 6.71411 12.375 7.1V16.9C12.375 17.2859 12.0108 17.6 11.5625 17.6H3.4375C2.9892 17.6 2.625 17.2859 2.625 16.9V7.1Z" fill="#8D8D8D"/>
                                                                    <path d="M18.2483 12.586V4.02769C18.2483 2.61189 17.0966 1.46021 15.6808 1.46021H7.12249C6.64986 1.46021 6.26666 1.84341 6.26666 2.31603C6.26666 2.78866 6.64986 3.17186 7.12249 3.17186H15.6808C16.153 3.17186 16.5366 3.5559 16.5366 4.02769V12.586C16.5366 13.0586 16.9198 13.4418 17.3924 13.4418C17.8651 13.4418 18.2483 13.0586 18.2483 12.586Z" fill="#8D8D8D"/>
                                                                    </svg>
                                                                </div>
                                                            </div>
                                                            
                                                            <p class="description">
                                                                        <?php esc_html_e('You can use the above PHP code in your theme to display the sitemap.', 'xml-sitemap-for-google'); ?>
                                                                    </p>
                                                        </div>
                                                    </div>

                                                    <div id='dedicate_url' class='tab-content-html-sitemap'>
                                                        <div class='html-sitemap-page'>
                                                            <input type="text" id="xmlsbw_dedicated_url" name="xmlsbw_dedicated_url" style="width:100%;"
                                                            value="<?php echo esc_attr(get_option('xmlsbw_dedicated_url')); ?>">
                                                            <br><br>
                                                            <p class="description">
                                                                <?php esc_html_e('Display the HTML sitemap on a dedicated wordpress page.', 'xml-sitemap-for-google'); ?>
                                                            </p>
                                                        </div>
                                                    </div>

                                                </td>
                                            </tr>
                                            <tr id="sort_order_tr">
                                                <th>
                                                    <?php esc_html_e('Sort Order:', 'xml-sitemap-for-google'); ?>
                                                </th>
                                                <td>
                                                    <?php
                                                        $sort_order_options = array(
                                                            "PublishDate" => "date",
                                                            "LastUpdated" => "modified",
                                                            "Alphabetical" => "title"
                                                        );
                                                        $saved_sort_order = get_option('xmlsbw_sort_order');
                                                        echo "<select name='sort-order'>";
                                                        foreach ($sort_order_options as $text => $value) {
                                                            $selected = ($value === $saved_sort_order) ? "selected" : "";
                                                            echo "<option value='" . esc_attr($value) . "' " . esc_attr($selected) . ">" . esc_html($text) . "</option>";
                                                        }
                                                        echo "</select>";
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr id="sort_direction_tr">
                                                <th>
                                                    <?php esc_html_e('Sort Direction:', 'xml-sitemap-for-google'); ?>
                                                </th>
                                                <td>
                                                    <?php
                                                    $sort_direction_options = array(
                                                        "Ascending" => "ASC",
                                                        "Descending" => "DESC"
                                                    );
                                                    $saved_sort_direction = get_option('xmlsbw_sort_direction');
                                                    echo "<select name='sort-direction'>";
                                                    foreach ($sort_direction_options as $text => $value) {
                                                        $selected_dir = ($value === $saved_sort_direction) ? "selected" : "";
                                                        echo "<option value='" . esc_attr($value) . "' " . esc_attr($selected_dir) . ">" . esc_html($text) . "</option>";
                                                    }
                                                    echo "</select>";
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr id="select_post_type_html_tr">
                                                <th>
                                                    <?php esc_html_e('Configure Post & Taxonomy types:', 'xml-sitemap-for-google'); ?>
                                                </th>
                                                <td style="width:697px;">
                                                    <input type="checkbox" id="xmlsbw_include_all_post_type_html" name="xmlsbw_include_all_post_type_html" 
                                                            value="1" <?php checked(get_option('xmlsbw_include_all_post_type_html'), 1); ?>>
                                                    <label>
                                                        <?php esc_html_e('Include All Post Types & Taxonomy Types', 'xml-sitemap-for-google'); ?>
                                                    </label>
                                                    <div class="select-post-type">
                                                        <div class="post-type-html-wrapper">
                                                            <?php
                                                            $all_post_types = get_post_types(['public' => true], 'objects');
                                                            foreach ($all_post_types as $post_type) {
                                                                if ($post_type->name === 'attachment') {
                                                                    continue;
                                                                }
                                                                $post_type_name = esc_attr($post_type->name);
                                                                $post_type_label = esc_html($post_type->label);
                                                                echo '<a href="#' . esc_attr($post_type_name) . '" class="post-type-tab-html">' . esc_html($post_type_label,'xml-sitemap-for-google') . '</a>';
                                                            }
                                                            ?>
                                                        </div>
                                                        <?php
                                                        $include_all_post_type_html = get_option( 'xmlsbw_include_all_post_type_html' );
                                                        $selected_post_types = get_option('xmlsbw_selected_post_types_html', array());
                                                        foreach ($all_post_types as $post_type) {
                                                            // Post type 
                                                            if($include_all_post_type_html){
                                                                $checked = 'checked';
                                                                $disabled = 'disabled';
                                                            } else{
                                                                $checked = in_array($post_type->name, $selected_post_types) ? 'checked' : '';
                                                                $disabled ='';
                                                            }
                                                            echo '<div id="' . esc_attr($post_type->name) . '-html" class="tab-content-post-type-html">';
                                                                echo "<div class='post-type'>";
                                                                    echo "<div class='post-type-div'>";
                                                                        echo "<h4>". esc_html_e('Select post type:', 'xml-sitemap-for-google') . "</h4>";
                                                                        echo '<input type="checkbox" class="post-type-html-class" name="post_types_html[]" value="' . esc_attr($post_type->name) . '" ' . esc_attr($checked) . ' id="post_type_' . esc_attr($post_type->name) . '" style="margin:10px;" ' . esc_attr($disabled) . '>';
                                                                        echo '<label for="post_type_' . esc_attr($post_type->name) . '">' . esc_html($post_type->label) . '</label>';
                                                                    echo "</div>";
                                                                echo "</div>";

                                                                // Taxonomies
                                                                $taxonomies = get_object_taxonomies($post_type->name, 'objects');
                                                                $taxonomies_to_be_excluded = array('post_format', 'product_type', 'product_visibility', 'product_shipping_class', 'pa_color', 'pa_size','post_translations','language');
                                                                $taxonomies = array_filter($taxonomies, function($taxonomy) use ($taxonomies_to_be_excluded) {
                                                                    return !in_array($taxonomy->name, $taxonomies_to_be_excluded);
                                                                });
                                                                
                                                                $taxonomies = array_values($taxonomies);
                                                                $selected_taxonomies = get_option('xmlsbw_selected_taxonomies_html', array());
                                                                if ($taxonomies) {
                                                                    echo "<h4>". esc_html_e('Select taxonomy type:', 'xml-sitemap-for-google') . "</h4>";
                                                                    foreach ($taxonomies as $taxonomy) {
                                                                        echo "<div class='taxonomy-type'>";
                                                                            echo "<div class='taxonomy-type-div'>";
                                                                                if($include_all_post_type_html){
                                                                                    $checked = 'checked';
                                                                                    $disabled = 'disabled';
                                                                                } else{
                                                                                    $checked = in_array($taxonomy->name, $selected_taxonomies) ? 'checked' : '';
                                                                                    $disabled ='';
                                                                                }
                                                                                echo '<input type="checkbox" class="taxonomy-type-html-class" name="taxonomies_html[]" value="' . esc_attr($taxonomy->name) . '" ' . esc_attr($checked) . ' style="margin:10px;" ' . esc_attr($disabled) . '>';
                                                                                echo '<label>' . esc_html($taxonomy->label,'xml-sitemap-for-google') . '</label>';
                                                                            echo "</div>";
                                                                        echo "</div>";
                                                                    }
                                                                }
                                                            echo "</div>";
                                                        }
                                                        ?>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr id="include_publication_date_tr">
                                                <th>
                                                    <?php esc_html_e('Include Publication Date:', 'xml-sitemap-for-google'); ?>
                                                </th>
                                                <td class="include_publication_date">
                                                    <div class="toggle-switch">
                                                        <input type="hidden" name="xmlsbw_include_publication_date" value="0">
                                                        <input type="checkbox" id="xmlsbw_include_publication_date" name="xmlsbw_include_publication_date" value="1" <?php checked(get_option('xmlsbw_include_publication_date'), 1); ?>>
                                                        <label for="xmlsbw_include_publication_date" class="toggle"></label>
                                                    </div>
                                                    <p class="description"><?php esc_html_e('This setting is applicable only to posts and pages.', 'xml-sitemap-for-google'); ?></p>
                                                </td>
                                            </tr>
                                            <tr id="compact_archives_tr">
                                                <th>
                                                    <?php esc_html_e('Compact Archives:', 'xml-sitemap-for-google'); ?>
                                                </th>
                                                <td class="include_publication_date">
                                                    <div class="toggle-switch">
                                                        <input type="hidden" name="xmlsbw_compact_archives" value="0">
                                                        <input type="checkbox" id="xmlsbw_compact_archives" name="xmlsbw_compact_archives" value="1" <?php checked(get_option('xmlsbw_compact_archives'), 1); ?>>
                                                        <label for="xmlsbw_compact_archives" class="toggle"></label>
                                                    </div>
                                                    <p class="description"><?php esc_html_e('This setting enables you to switch between the standard sitemap and the compact date archive sitemap.', 'xml-sitemap-for-google'); ?></p>
                                                </td>
                                            </tr>
                                            <tr id = "enable_advanced_settings_html_tr">
                                                <th>
                                                    <?php esc_html_e('Advanced Settings:', 'xml-sitemap-for-google'); ?>
                                                </th>
                                                <td>
                                                    <div class="toggle-switch">
                                                        <input type="hidden" name="c" value="0">
                                                        <input type="checkbox" id="xmlsbw_enable_advanced_settings_html" name="xmlsbw_enable_advanced_settings_html" value="1" <?php checked(get_option('xmlsbw_enable_advanced_settings_html'), 1); ?>>
                                                        <label for="xmlsbw_enable_advanced_settings_html" class="toggle"></label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr id = "exclude_posts_html_tr">
                                                <th style="text-align:center;">
                                                    <?php esc_html_e('Exclude Posts:', 'xml-sitemap-for-google'); ?>
                                                </th>
                                                <td>
                                                    <?php
                                                    $excluded_posts = (array) get_option('xmlsbw_excluded_posts_html', array());
                                                    ?>
                                                    <input style="width: 50%" type="text" id="excluded_posts_html_input"
                                                        name="xmlsbw_excluded_posts_html" placeholder='Begin typing post title to search'
                                                        value="">
                                                    
                                                    <p class="description">
                                                        <?php esc_html_e('Provide the names of the posts you want to exclude.', 'xml-sitemap-for-google'); ?>
                                                    </p>
                                                    <div class="excluded_posts_html_container" id="excluded_posts_html_container">
                                                        <?php foreach ($excluded_posts as $post_id) { ?>
                                                            <?php $post = get_post($post_id); ?>
                                                            <?php if ($post) { ?>
                                                                <div class="excluded-post-html">
                                                                    <span>
                                                                    <?php echo esc_html($post->post_title,'xml-sitemap-for-google'); ?>
                                                                    </span>
                                                                    <input type="hidden" name="xmlsbw_excluded_posts_html[]" value="<?php echo esc_attr($post_id); ?>">
                                                                    <button type="button" class="remove-excluded-post-html">X</button>
                                                                </div>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </div> 
                                                </td>
                                            </tr>
                                            <tr id = "exclude_terms_html_tr">
                                                <th style="text-align:center;">
                                                <?php esc_html_e('Exclude Terms:', 'xml-sitemap-for-google'); ?>
                                                </th>
                                                <td>
                                                    <?php
                                                    $excluded_terms = (array) get_option('xmlsbw_excluded_terms_html', array());
                                                    $taxonomies = get_taxonomies(array(), 'objects');
                                                    ?>
                                                    <input style="width: 50%;" type="text" id="excluded_terms_html_input"
                                                        name="excluded_terms_html_input" placeholder='Begin typing term title to search'
                                                        value="">
                                                    <br><br>
                                                    <p class="description">
                                                        <?php esc_html_e('Provide the names of the terms you want to exclude.', 'xml-sitemap-for-google'); ?>

                                                    </p>
                                                    <div class="excluded_terms_html_container" id="excluded_terms_html_container">
                                                        <?php foreach ($excluded_terms as $term_id): ?>
                                                            <?php foreach ($taxonomies as $taxonomy): ?>
                                                                <?php $term = get_term_by('id', $term_id, $taxonomy->name); ?>
                                                                <?php if ($term): ?>
                                                                    <div class="excluded-term-html">
                                                                        <span>
                                                                            <?php echo esc_html($term->name,'xml-sitemap-for-google'); ?>
                                                                        </span>
                                                                        <input type="hidden" name="xmlsbw_excluded_terms_html[]"
                                                                            value="<?php echo esc_html($term_id); ?>">
                                                                        <button type="button" class="remove-excluded-term-html">X</button>
                                                                    </div>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <?php
                                    if(get_option('premium_access_allowed') != 1){
                                    ?>
                                    <div id="pop-up-box" class="pop-up-box text-center">
                                        <table class="pop-up-table">
                                            <tr>
                                                <td style="text-align:center;"><h3>HTML Sitemap is a Premium Feature</h3></td>                                                
                                            </tr>
                                            <tr>
                                                <td class="description">
                                                    Unlock the power of comprehensive site navigation with our premium HTML Sitemap feature. Seamlessly generating a detailed sitemap for all pages on your website,
                                                    it provides users and search engines with an organized roadmap to effortlessly explore your content.
                                                </td>
                                            </tr>
                                            <tr class="features-row">
                                                <td class="features">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none">
                                                        <g clip-path="url(#clip0_1642_43)">
                                                            <path d="M16.5871 7.49821L15.4277 6.33898C15.1525 6.06381 14.927 5.52001 14.927 5.13004V3.4906C14.927 2.71072 14.2899 2.07363 13.5102 2.07321H11.8701C11.4806 2.07321 10.9362 1.8473 10.661 1.57233L9.50173 0.413107C8.95092 -0.137702 8.04907 -0.137702 7.49826 0.413107L6.33903 1.57316C6.06361 1.84834 5.51857 2.07363 5.12988 2.07363H3.49044C2.7116 2.07363 2.07368 2.71072 2.07368 3.4906V5.13008C2.07368 5.51852 1.84822 6.06402 1.573 6.33903L0.413571 7.49825C-0.137653 8.04906 -0.137653 8.95092 0.413571 9.5026L1.573 10.6618C1.84839 10.937 2.07368 11.4823 2.07368 11.8708V13.5103C2.07368 14.2893 2.7116 14.9272 3.49044 14.9272H5.12992C5.51944 14.9272 6.06386 15.1527 6.33907 15.4277L7.4983 16.5873C8.04911 17.1377 8.95097 17.1377 9.50178 16.5873L10.661 15.4277C10.9364 15.1525 11.4806 14.9272 11.8702 14.9272H13.5103C14.2899 14.9272 14.927 14.2893 14.927 13.5103V11.8708C14.927 11.4806 15.1527 10.9368 15.4277 10.6618L16.5871 9.5026C17.1375 8.95092 17.1375 8.04902 16.5871 7.49821ZM7.37545 11.6877L4.24973 8.5616L5.25148 7.56005L7.3757 9.68426L11.7484 5.31261L12.7499 6.31416L7.37545 11.6877Z" fill="#FDBC33"></path>
                                                        </g>
                                                        <defs>
                                                            <clipPath id="clip0_1642_43">
                                                                <rect width="17" height="17" fill="white"></rect>
                                                            </clipPath>
                                                        </defs>
                                                    </svg>
                                                    <p>HTML Sitemap</p>
                                                </td>
                                                <td class="features">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none">
                                                        <g clip-path="url(#clip0_1642_43)">
                                                            <path d="M16.5871 7.49821L15.4277 6.33898C15.1525 6.06381 14.927 5.52001 14.927 5.13004V3.4906C14.927 2.71072 14.2899 2.07363 13.5102 2.07321H11.8701C11.4806 2.07321 10.9362 1.8473 10.661 1.57233L9.50173 0.413107C8.95092 -0.137702 8.04907 -0.137702 7.49826 0.413107L6.33903 1.57316C6.06361 1.84834 5.51857 2.07363 5.12988 2.07363H3.49044C2.7116 2.07363 2.07368 2.71072 2.07368 3.4906V5.13008C2.07368 5.51852 1.84822 6.06402 1.573 6.33903L0.413571 7.49825C-0.137653 8.04906 -0.137653 8.95092 0.413571 9.5026L1.573 10.6618C1.84839 10.937 2.07368 11.4823 2.07368 11.8708V13.5103C2.07368 14.2893 2.7116 14.9272 3.49044 14.9272H5.12992C5.51944 14.9272 6.06386 15.1527 6.33907 15.4277L7.4983 16.5873C8.04911 17.1377 8.95097 17.1377 9.50178 16.5873L10.661 15.4277C10.9364 15.1525 11.4806 14.9272 11.8702 14.9272H13.5103C14.2899 14.9272 14.927 14.2893 14.927 13.5103V11.8708C14.927 11.4806 15.1527 10.9368 15.4277 10.6618L16.5871 9.5026C17.1375 8.95092 17.1375 8.04902 16.5871 7.49821ZM7.37545 11.6877L4.24973 8.5616L5.25148 7.56005L7.3757 9.68426L11.7484 5.31261L12.7499 6.31416L7.37545 11.6877Z" fill="#FDBC33"></path>
                                                        </g>
                                                        <defs>
                                                            <clipPath id="clip0_1642_43">
                                                                <rect width="17" height="17" fill="white"></rect>
                                                            </clipPath>
                                                        </defs>
                                                    </svg>
                                                    <p>Compact Archives</p>
                                                </td>
                                                <td class="features">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none">
                                                        <g clip-path="url(#clip0_1642_43)">
                                                            <path d="M16.5871 7.49821L15.4277 6.33898C15.1525 6.06381 14.927 5.52001 14.927 5.13004V3.4906C14.927 2.71072 14.2899 2.07363 13.5102 2.07321H11.8701C11.4806 2.07321 10.9362 1.8473 10.661 1.57233L9.50173 0.413107C8.95092 -0.137702 8.04907 -0.137702 7.49826 0.413107L6.33903 1.57316C6.06361 1.84834 5.51857 2.07363 5.12988 2.07363H3.49044C2.7116 2.07363 2.07368 2.71072 2.07368 3.4906V5.13008C2.07368 5.51852 1.84822 6.06402 1.573 6.33903L0.413571 7.49825C-0.137653 8.04906 -0.137653 8.95092 0.413571 9.5026L1.573 10.6618C1.84839 10.937 2.07368 11.4823 2.07368 11.8708V13.5103C2.07368 14.2893 2.7116 14.9272 3.49044 14.9272H5.12992C5.51944 14.9272 6.06386 15.1527 6.33907 15.4277L7.4983 16.5873C8.04911 17.1377 8.95097 17.1377 9.50178 16.5873L10.661 15.4277C10.9364 15.1525 11.4806 14.9272 11.8702 14.9272H13.5103C14.2899 14.9272 14.927 14.2893 14.927 13.5103V11.8708C14.927 11.4806 15.1527 10.9368 15.4277 10.6618L16.5871 9.5026C17.1375 8.95092 17.1375 8.04902 16.5871 7.49821ZM7.37545 11.6877L4.24973 8.5616L5.25148 7.56005L7.3757 9.68426L11.7484 5.31261L12.7499 6.31416L7.37545 11.6877Z" fill="#FDBC33"></path>
                                                        </g>
                                                        <defs>
                                                            <clipPath id="clip0_1642_43">
                                                                <rect width="17" height="17" fill="white"></rect>
                                                            </clipPath>
                                                        </defs>
                                                    </svg>
                                                    <p>Exclude Posts/Pages</p>
                                                </td>
                                            </tr>
                                            <tr class="unlock-row">
                                                <td>
                                                    <a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium" class="unlock-featues">Unlock HTML Sitemap</a>
                                                </td>
                                                <td>
                                                    <a href="https://www.weblineindia.com/contact-us.html?utm_source=WP-Plugin&utm_medium=XML%20Sitemap%20For%20Google&utm_campaign=Free%20Support" target="_blank" class="know-all-featues">Learn more about all features</a>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <?php
                                    }
                                    ?>
                                </td>
                            </tr>
                        </table>
                        <div id="submit-button" class="submit" style="display: flex; margin: 0; padding:0;">
                            <input type="submit" name="save_sitemap" class="button button-primary" value="Update Changes" title="Updates your changes on click">
                            <div class="loader">
                                <div class="spinner">
                                </div>
                            </div>
                        </div>
                        <div class="success-message"></div>
                    </form>
                    <br>
                </div>
                <div class="right-box-xmlsbw">
                    <?php
                        xmlsbw_general_section_callback();
                    ?>
                </div>
            </div>
        </div>
    <?php
    add_filter('admin_footer_text', 'xmlsbw_admin_footer');
}

/**
 * Save settings for the XML Sitemap plugin based on the submitted form data.
 *
 * This function retrieves form data submitted via POST method, sanitizes and processes it,
 * and updates the corresponding options in the WordPress database.
 *
 * @since 1.0.0
 */

function xmlsbw_save_sitemap_settings()
{
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'xmlsbw_save_sitemap_settings_nonce' ) ) {

        $formData = isset($_POST['formData']) ? $_POST['formData'] : '';
        $formData = filter_var($formData, FILTER_SANITIZE_STRING);
        parse_str($formData, $parsedData);

        if($parsedData['xmlsbw_include_all_post_type_html'] == 1){
            $all_post_types = get_post_types(array('public' => true), 'names');
            update_option('xmlsbw_selected_post_types_html', $all_post_types);
            $all_taxonomies = get_taxonomies(array('public' => true), 'names');
            update_option('xmlsbw_selected_taxonomies_html', $all_taxonomies);
        }
        elseif(!empty($parsedData['post_types_html']) || !empty($parsedData['taxonomies_html'])){
            update_option('xmlsbw_selected_post_types_html', isset($parsedData['post_types_html']) ? $parsedData['post_types_html'] : array());
            update_option('xmlsbw_selected_taxonomies_html', isset($parsedData['taxonomies_html']) ? $parsedData['taxonomies_html'] : array());
        }else if($parsedData['xmlsbw_enable_html_sitemap']== 1){
            $response = array('success' => false);            
            wp_send_json_error($response);
        }

        update_option('xmlsbw_enable_sitemap_generation', isset($parsedData['xmlsbw_enable_sitemap_generation']) ? $parsedData['xmlsbw_enable_sitemap_generation'] : '');
        update_option('xmlsbw_sitemap_url', isset($parsedData['xmlsbw_sitemap_url']) ? $parsedData['xmlsbw_sitemap_url'] : '');
        update_option('xmlsbw_enable_sitemap_indexes', isset($parsedData['xmlsbw_enable_sitemap_indexes']) ? $parsedData['xmlsbw_enable_sitemap_indexes'] : '');
        // update_option('xmlsbw_links_per_sitemap', isset($parsedData['xmlsbw_links_per_sitemap']) ? $parsedData['xmlsbw_links_per_sitemap'] : '');
        update_option('xmlsbw_links_per_sitemap', (isset($parsedData['xmlsbw_links_per_sitemap']) && !empty($parsedData['xmlsbw_links_per_sitemap'])) ? $parsedData['xmlsbw_links_per_sitemap'] : 1000);
        update_option('xmlsbw_include_home_page', isset($parsedData['xmlsbw_include_home_page']) ? $parsedData['xmlsbw_include_home_page'] : '');
        update_option('xmlsbw_include_author_pages', isset($parsedData['xmlsbw_include_author_pages']) ? $parsedData['xmlsbw_include_author_pages'] : '');
        update_option('xmlsbw_include_all_post_type', isset($parsedData['xmlsbw_include_all_post_type']) ? $parsedData['xmlsbw_include_all_post_type'] : '');
        update_option('xmlsbw_selected_post_types', isset($parsedData['post_types']) ? $parsedData['post_types'] : array());
        update_option('xmlsbw_selected_taxonomies', isset($parsedData['taxonomies']) ? $parsedData['taxonomies'] : array());
        update_option('xmlsbw_enable_include_last_mod_time', isset($parsedData['xmlsbw_enable_include_last_mod_time']) ? $parsedData['xmlsbw_enable_include_last_mod_time'] : '');
        update_option('xmlsbw_post_priority_calculation', isset($parsedData['xmlsbw_post_priority_calculation']) ? $parsedData['xmlsbw_post_priority_calculation'] : '');
        update_option('xmlsbw_excluded_posts', isset($parsedData['xmlsbw_excluded_posts']) ? (array) $parsedData['xmlsbw_excluded_posts'] : array());

        $existing_excluded_terms = array();
        $new_excluded_terms = isset($parsedData['xmlsbw_excluded_terms']) ? (array) $parsedData['xmlsbw_excluded_terms'] : array();
        foreach ($new_excluded_terms as $value) {
            if (!preg_match('/^[a-zA-Z]/', $value)) {
                $existing_excluded_terms[] = $value;
            }
        }
        $term_ids = array();
        foreach ($new_excluded_terms as $term_name) {
            $taxonomies = get_taxonomies(array('public' => true), 'objects');
            foreach ($taxonomies as $taxonomy) {
                $term = get_term_by('name', $term_name, $taxonomy->name);
                if ($term) {
                    $term_ids[] = $term->term_id;
                    break;
                }
            }
        }
        $updated_excluded_terms = array_merge($existing_excluded_terms, $term_ids);
        $updated_excluded_terms = array_unique($updated_excluded_terms);
        update_option('xmlsbw_excluded_terms', $updated_excluded_terms);

        update_option('xmlsbw_post_priority', isset($parsedData['xmlsbw_post_priority']) ? $parsedData['xmlsbw_post_priority'] : array());
        update_option('xmlsbw_post_frequency', isset($parsedData['xmlsbw_post_frequency']) ? $parsedData['xmlsbw_post_frequency'] : array());
        update_option('xmlsbw_taxonomy_priority', isset($parsedData['xmlsbw_taxonomy_priority']) ? $parsedData['xmlsbw_taxonomy_priority'] : array());
        update_option('xmlsbw_taxonomy_frequency', isset($parsedData['xmlsbw_taxonomy_frequency']) ? $parsedData['xmlsbw_taxonomy_frequency'] : array());
        update_option('xmlsbw_enable_advanced_settings', isset($parsedData['xmlsbw_enable_advanced_settings']) ? $parsedData['xmlsbw_enable_advanced_settings'] : '');
        update_option('xmlsbw_enable_additional_pages', isset($parsedData['xmlsbw_enable_additional_pages']) ? $parsedData['xmlsbw_enable_additional_pages'] : '');

        $additional_pages_data = isset($parsedData['additional_pages']) ? $parsedData['additional_pages'] : array();

        // Check if last_modified is empty and update it with current UTC date time
        foreach ($additional_pages_data['last_modified'] as $index => $last_modified) {
            if (empty($last_modified)) {
                $additional_pages_data['last_modified'][$index] = gmdate('Y-M-d H:i');
            }
        }

        update_option('xmlsbw_additional_pages_data', $additional_pages_data);

        // HTML Sitemap
        update_option('xmlsbw_enable_html_sitemap', isset($parsedData['xmlsbw_enable_html_sitemap']) ? $parsedData['xmlsbw_enable_html_sitemap'] : '');
        update_option('xmlsbw_dedicated_url', isset($parsedData['xmlsbw_dedicated_url']) ? $parsedData['xmlsbw_dedicated_url'] : '');
        update_option('xmlsbw_sort_order', isset($parsedData['sort-order']) ? $parsedData['sort-order'] : '');
        update_option('xmlsbw_sort_direction', isset($parsedData['sort-direction']) ? $parsedData['sort-direction'] : '');
        update_option('xmlsbw_include_publication_date', isset($parsedData['xmlsbw_include_publication_date']) ? $parsedData['xmlsbw_include_publication_date'] : '');
        update_option('xmlsbw_compact_archives', isset($parsedData['xmlsbw_compact_archives']) ? $parsedData['xmlsbw_compact_archives'] : '');
        update_option('xmlsbw_enable_advanced_settings_html', isset($parsedData['xmlsbw_enable_advanced_settings_html']) ? $parsedData['xmlsbw_enable_advanced_settings_html'] : '');
        update_option('xmlsbw_include_all_post_type_html', isset($parsedData['xmlsbw_include_all_post_type_html']) ? $parsedData['xmlsbw_include_all_post_type_html'] : '');
        update_option('xmlsbw_excluded_posts_html', isset($parsedData['xmlsbw_excluded_posts_html']) ? (array) $parsedData['xmlsbw_excluded_posts_html'] : array());
        $existing_excluded_terms_html = array();
        $new_excluded_terms_html = isset($parsedData['xmlsbw_excluded_terms_html']) ? (array) $parsedData['xmlsbw_excluded_terms_html'] : array();
        foreach ($new_excluded_terms_html as $value) {
            if (!preg_match('/^[a-zA-Z]/', $value)) {
                $existing_excluded_terms_html[] = $value;
            }
        }
        $term_ids_html = array();
        foreach ($new_excluded_terms_html as $term_name) {
            $taxonomies = get_taxonomies(array('public' => true), 'objects');
            foreach ($taxonomies as $taxonomy) {
                $term = get_term_by('name', $term_name, $taxonomy->name);
                if ($term) {
                    $term_ids_html[] = $term->term_id;
                    break;
                }
            }
        }
        $existing_excluded_terms_html = array_merge($existing_excluded_terms_html, $term_ids_html);
        $existing_excluded_terms_html = array_unique($existing_excluded_terms_html);
        update_option('xmlsbw_excluded_terms_html', $existing_excluded_terms_html);
    }

    $sitemap_url = get_option('xmlsbw_sitemap_url');

    $response_data = array(
        'success' => true,
        'message' => 'Settings Updated',
        'sitemap_url' => $sitemap_url
    );
    wp_send_json_success($response_data);

    die();
}

?>