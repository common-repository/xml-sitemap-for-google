<?php 
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Callback section on Admin Footer
 * @since 1.0.0
 */
function xmlsbw_admin_footer($footer_text)
{
    $url = 'https://wordpress.org';
    $wpdev_url = 'https://www.weblineindia.com/wordpress-development.html?utm_source=WP-Plugin&utm_medium=XML%20Sitemap%20By%20Webline&utm_campaign=Footer%20CTA';
    $text = sprintf(
        wp_kses(
            'Please rate our plugin %1$s <a href="https://wordpress.org/plugins/xml-sitemap-for-google/#reviews" target="_blank" rel="noopener noreferrer">&#9733;&#9733;&#9733;&#9733;&#9733;</a> on <a href="https://wordpress.org/plugins/xml-sitemap-for-google/#reviews" target="_blank" rel="noopener">WordPress.org</a> to help us spread the word. Thank you from the <a href="%4$s" target="_blank" rel="noopener noreferrer">WordPress development</a> team at WeblineIndia.',
            array(
                'a' => array(
                    'href' => array(),
                    'target' => array(),
                    'rel' => array(),
                ),
            )
        ),
        '<strong>"XML Sitemap for Google"</strong>',
        $url,
        $url,
        $wpdev_url
    );
    return $text;
}

/**
 * General section callback function.
 * @since 1.0.0
 */
function xmlsbw_general_section_callback()
{
    ?>
    <div class="xmlsbw-plugin-cta">
        <h2 class="xmlsbw-heading">Thank you for downloading our plugin - XML Sitemap for Google.</h2>
        <h2 class="xmlsbw-heading">We're here to help !</h2>
        <p>Our plugin comes with free, basic support for all users. We also provide plugin customization in case you want to
            customize our plugin to suit your needs.</p>
            <div class="action-btns">
                <a href="https://www.weblineindia.com/contact-us.html?utm_source=WP-Plugin&utm_medium=XML%20Sitemap%20For%20Google&utm_campaign=Free%20Support"
                    target="_blank" class="button">Need help?</a>
                <a href="https://www.weblineindia.com/contact-us.html?utm_source=WP-Plugin&utm_medium=XML%20Sitemap%20For%20Google&utm_campaign=Plugin%20Customization"
                    target="_blank" class="button">Want to customize plugin?</a>
            </div>
        
    </div>
    <div class="xmlsbw-plugin-cta upgrade">
        <p class="note">Want to hire Wordpress Developer to finish your wordpress website quicker or need any help in
            maintenance and upgrades?</p>
        <a href="https://www.weblineindia.com/contact-us.html?utm_source=WP-Plugin&utm_medium=XML%20Sitemap%20For%20Google&utm_campaign=Hire%20WP%20Developer"
            target="_blank" class="button button-primary">Hire now</a>
    </div>
    <?php
}

?>