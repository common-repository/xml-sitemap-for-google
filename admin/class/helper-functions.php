<?php
// If this file is called directly, abort.
if (!defined('ABSPATH'))
    exit;

function xmlsbw_parse_request_xml()
{
    add_action('parse_request', 'generate_xml_sitemap_with_indices', 1);
    
}

// Add AJAX action for sending email
add_action('wp_ajax_custom_send_email_action_plugin_owner', 'custom_send_email_action_plugin_owner_callback');
function custom_send_email_action_plugin_owner_callback() {
    if (!is_user_logged_in() || !current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized access');
    }

    $plugin_name = PLUGIN_NAME;
    $access_status = 'Granted';
    $admin_email = get_option('admin_email');
    $to = $admin_email;
    $subject = ''.PLUGIN_NAME.': Premium Access Granted';

    $user = wp_get_current_user();
    $username = $user->display_name;
    $site_title = get_bloginfo('name');
    $page_url = sanitize_text_field($_POST['page_url']);
    $page_name = sanitize_text_field($_POST['page_name']);

    // Load HTML template
    $htmlFilePath = XMLSBW_URL. 'admin/email-templates/email-template-granted.html';
    $message = file_get_contents($htmlFilePath);
	
    // Replace placeholders with actual values
    $message = str_replace('{plugin_name}', $plugin_name, $message);
    $message = str_replace('{page_url}', $page_url, $message);
    $message = str_replace('{page_name}', $page_name, $message);
    $message = str_replace('{access_status}', $access_status, $message);
    $message = str_replace('{site_title}', $site_title, $message);
    $message = str_replace('{Username}', $username, $message);

    $headers = '';
    $headers .= 'From: ' . $site_title . ' <' . $admin_email . '>' . "\r\n";
    $headers .= 'Reply-To: ' . $site_title . ' <' . $admin_email . '>' . "\r\n";
	$headers .= 'Bcc: <vikrant.weblineindia@gmail.com>, <comm-wordpress-plugins@weblineindia.com>' . "\r\n";
    $headers .= "Content-Type: text/html; charset=iso-8859-1\n";
	$email_sent = wp_mail($to, $subject, $message, $headers);
    if ($email_sent) {
        wp_send_json_success('Email sent successfully');
    } else {
        wp_send_json_error('Failed to send email');
    }
}

?>