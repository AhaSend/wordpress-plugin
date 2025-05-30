<?php
/**
 * Plugin Name:       AhaSend Email API
 * Description:       Connect your WordPress site to AhaSend for reliable, fast transactional email delivery with easy SMTP integration and real-time tracking.
 * Version:           1.3
 * Author:            ahasend
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author URI:        https://ahasend.com
 * Developer:         AhaSend
 * Developer URI:     https://ahasend.com
 * License:           GPLv3 or later
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       ahasend-email-api
 * Domain Path:       /languages
 */

// Prevent direct access to the plugin
if (!defined('ABSPATH')) exit;

function ahasend_create_mu_plugin() {
    $mu_plugin_dir = WP_CONTENT_DIR . '/mu-plugins/';
    $mu_plugin_file = $mu_plugin_dir . 'ahasend-mu-mailer.php';

    // Ensure the mu-plugins directory exists
    if ( ! file_exists( $mu_plugin_dir ) ) {
        mkdir( $mu_plugin_dir, 0755, true );
    }

    // The MU plugin PHP code
    $mu_plugin_code = <<<EOD
<?php
/*
Plugin Name: AhaSend MU Mailer
Description: Overrides wp_mail to use AhaSend API.
Author: Your Name
*/

if (!function_exists('wp_mail')) {
    function wp_mail(\$to, \$subject, \$message, \$headers = '', \$attachments = array()) {
        \$api_key    = get_option('ahasend_api_key');
        \$from_email = get_option('ahasend_from_email');
        \$from_name  = get_option('ahasend_from_name');

        if (!\$api_key || !\$from_email || !\$from_name) {
            return false;
        }

        \$is_html = false;
        if (is_array(\$headers)) {
            foreach (\$headers as \$header) {
                if (stripos(\$header, 'Content-Type: text/html') !== false) {
                    \$is_html = true;
                    break;
                }
            }
        } elseif (stripos(\$headers, 'Content-Type: text/html') !== false) {
            \$is_html = true;
        }

        // Format recipients
        \$recipients = is_array(\$to) ? array_map(function(\$email) {
            return ['email' => \$email, 'name' => ''];
        }, \$to) : [['email' => \$to, 'name' => '']];

        \$payload = json_encode([
            'from' => [
                'email' => \$from_email,
                'name'  => \$from_name
            ],
            'recipients' => \$recipients,
            'content'    => [
                'subject'   => \$subject,
                'html_body' => \$is_html ? \$message : nl2br(esc_html(\$message))
            ]
        ]);

        \$response = wp_remote_post('https://api.ahasend.com/v1/email/send', array(
            'body'    => \$payload,
            'headers' => array(
                'accept'       => 'application/json',
                'X-Api-Key'    => \$api_key,
                'Content-Type' => 'application/json'
            ),
        ));

        if (is_wp_error(\$response)) {
            return false;
        }

        \$code = wp_remote_retrieve_response_code(\$response);
        return (\$code >= 200 && \$code < 300);
    }
}
EOD;

    // Write the MU plugin file
    file_put_contents( $mu_plugin_file, $mu_plugin_code );
}
add_action('admin_init', 'ahasend_create_mu_plugin');


// Remove the MU plugin when this plugin is deactivated
register_deactivation_hook(__FILE__, function() {
    $mu_plugin_file = WP_CONTENT_DIR . '/mu-plugins/ahasend-mu-mailer.php';
    if (file_exists($mu_plugin_file)) {
        unlink($mu_plugin_file);
    }
});

/**
 * Overrides wp_mail to use AhaSend API.
 */
/*add_filter('wp_mail', function($args) {

    $api_key    = get_option('ahasend_api_key');
    $from_email = get_option('ahasend_from_email');
    $from_name  = get_option('ahasend_from_name');

    // If not configured, let default mailer handle it
    if (!$api_key || !$from_email || !$from_name) {
        return $args;
    }

    $to      = $args['to'];
    $subject = $args['subject'];
    $message = $args['message'];
    $headers = $args['headers'];

    // Check if HTML
    $is_html = false;
    if (is_array($headers)) {
        foreach ($headers as $header) {
            if (stripos($header, 'Content-Type: text/html') !== false) {
                $is_html = true;
                break;
            }
        }
    } elseif (stripos($headers, 'Content-Type: text/html') !== false) {
        $is_html = true;
    }

    // Format recipients
    $recipients = is_array($to) ? array_map(function($email) {
        return ['email' => $email, 'name' => ''];
    }, $to) : [['email' => $to, 'name' => '']];

    // Create payload
    $payload = json_encode([
        'from' => [
            'email' => $from_email,
            'name'  => $from_name
        ],
        'recipients' => $recipients,
        'content'    => [
            'subject'   => $subject,
            'html_body' => $is_html ? $message : nl2br(esc_html($message))
        ]
    ]);

    // Send via AhaSend
    $response = wp_remote_post('https://api.ahasend.com/v1/email/send', array(
        'body'    => $payload,
        'headers' => array(
            'accept'       => 'application/json',
            'X-Api-Key'    => $api_key,
            'Content-Type' => 'application/json'
        ),
    ));

    // If successful, prevent default mail sending
    if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) == 200) {
        // Abort further sending by returning false as the PHPMailer object
        add_filter('wp_mail_phpmailer_init', function($phpmailer) {
            // Overwrite send() so no mails go out
            $phpmailer->preSend = function() { return false; };
        });
    }

    // Always return $args to keep compatibility
    return $args;
}, 1);*/


/**
 * Adds a custom settings page to the WordPress admin menu.
 */
function ahasend_add_settings_page() {
    add_menu_page(
        __('AhaSend Settings', 'ahasend-email-api'),
        __('AhaSend Email', 'ahasend-email-api'),
        'manage_options',
        'ahasend-email-api',
        'ahasend_render_settings_page',
        'dashicons-email',
        100
    );
}
add_action('admin_menu', 'ahasend_add_settings_page');

/**
 * Renders the custom settings page.
 */
function ahasend_render_settings_page() {
    // Save settings if form is submitted
	if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ahasend_save_settings'])) {
        check_admin_referer('ahasend_save_settings'); // Verify nonce for security

        // Save each setting
		if (isset($_POST['ahasend_api_key'])) {
			$ahasend_api_key = sanitize_text_field(wp_unslash($_POST['ahasend_api_key']));
			update_option('ahasend_api_key', $ahasend_api_key);
		}
		if (isset($_POST['ahasend_from_email'])) {
			$ahasend_api_key = sanitize_text_field(wp_unslash($_POST['ahasend_from_email']));
			update_option('ahasend_from_email', $ahasend_api_key);
		}
		if (isset($_POST['ahasend_from_name'])) {
			$ahasend_api_key = sanitize_text_field(wp_unslash($_POST['ahasend_from_name']));
			update_option('ahasend_from_name', $ahasend_api_key);
		}

        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Settings saved successfully.', 'ahasend-email-api') . '</p></div>';
    }

    // Retrieve current option values
    $api_key    = get_option('ahasend_api_key');
    $from_email = get_option('ahasend_from_email');
    $from_name  = get_option('ahasend_from_name');

    ?>
    <div class="wrap">
        <h1><?php esc_html_e('AhaSend Email Settings', 'ahasend-email-api'); ?></h1>
        <form method="post">
            <?php wp_nonce_field('ahasend_save_settings'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="ahasend_api_key"><?php esc_html_e('API Key', 'ahasend-email-api'); ?></label></th>
                    <td><input type="text" id="ahasend_api_key" name="ahasend_api_key" value="<?php echo esc_attr($api_key); ?>" class="regular-text" required /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="ahasend_from_email"><?php esc_html_e('From Email', 'ahasend-email-api'); ?></label></th>
                    <td><input type="email" id="ahasend_from_email" name="ahasend_from_email" value="<?php echo esc_attr($from_email); ?>" class="regular-text" required /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="ahasend_from_name"><?php esc_html_e('From Name', 'ahasend-email-api'); ?></label></th>
                    <td><input type="text" id="ahasend_from_name" name="ahasend_from_name" value="<?php echo esc_attr($from_name); ?>" class="regular-text" required /></td>
                </tr>
            </table>
            <?php submit_button(__('Save Settings', 'ahasend-email-api'), 'primary', 'ahasend_save_settings'); ?>
        </form>
    </div>
    <?php
}
