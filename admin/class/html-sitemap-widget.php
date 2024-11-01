<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) exit;
class HTML_Sitemap_Widget extends WP_Widget
{

    public function __construct()
    {
        $premium_access_allowed = get_option('premium_access_allowed');
        // Check if premium access is allowed
        if ($premium_access_allowed == 1) {
            parent::__construct('html_sitemap_widget',__('HTML Sitemap Widget', 'text_domain'),
                    array('description' => __('A widget to display the HTML sitemap.', 'text_domain')));
        }else{
            parent::__construct("","");
        }
    }

    public function widget($args, $instance)
    {
        if(get_option( 'xmlsbw_enable_html_sitemap') == 1){
            echo ('HTML Sitemap');
            echo do_shortcode('[show_html_sitemap]');
        }
        
    }
}

?>