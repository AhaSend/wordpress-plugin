<?php
/**
 * Plugin Name: AhaSend SMTP Integration
 * Description: Connect your WordPress site to AhaSend for reliable, fast transactional email delivery with easy SMTP integration and real-time tracking.
 * Version: 1.0
 * Author: ahasend
 * Text Domain: ahasend-email
 * Domain Path: /languages
 */

// Prevent direct access to the plugin
if (!defined('ABSPATH')) exit;

/**
 * Load plugin text domain for translations.
 */
function ahasend_load_textdomain() {
    load_plugin_textdomain('ahasend-email', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'ahasend_load_textdomain');

/**
 * Overrides wp_mail to use AhaSend API.
 */
/**
 * Overrides wp_mail to use AhaSend API.
 */
function ahasend_wp_mail($args) {
    $to          = $args['to'];
    $subject     = $args['subject'];
    $message     = $args['message'];
    $headers     = isset($args['headers']) ? $args['headers'] : '';
    $attachments = isset($args['attachments']) ? $args['attachments'] : [];
    $api_key     = get_option('ahasend_api_key');
    $from_email  = get_option('ahasend_from_email');
    $from_name   = get_option('ahasend_from_name');

    if (!$api_key || !$from_email || !$from_name) {
        return false; // Exit if settings are not set
    }

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

    // Prepare AhaSend payload
    $payload = json_encode([
        'from' => [
            'email' => $from_email,
            'name' => $from_name
        ],
        'recipients' => $recipients,
        'content' => [
            'subject' => $subject,
            'html_body' => $is_html ? $message : nl2br(esc_html($message))
        ]
    ]);

    // Initialize cURL request
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://api.ahasend.com/v1/email/send',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => [
            'accept: application/json',
            'X-Api-Key: ' . $api_key,
            'Content-Type: application/json'
        ],
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    $response_data = json_decode($response, true);
    return isset($response_data['success']) && $response_data['success'] === true;
}

// Hook into wp_mail with the correct number of accepted arguments
add_filter('wp_mail', 'ahasend_wp_mail', 10, 1);


/**
 * Adds a custom settings page to the WordPress admin menu.
 */
function ahasend_add_settings_page() {
    add_menu_page(
        __('AhaSend Settings', 'ahasend-email'),
        __('AhaSend Email', 'ahasend-email'),
        'manage_options',
        'ahasend-email',
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
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ahasend_save_settings'])) {
        check_admin_referer('ahasend_save_settings'); // Verify nonce for security

        // Save each setting
        update_option('ahasend_api_key', sanitize_text_field($_POST['ahasend_api_key']));
        update_option('ahasend_from_email', sanitize_email($_POST['ahasend_from_email']));
        update_option('ahasend_from_name', sanitize_text_field($_POST['ahasend_from_name']));

        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Settings saved successfully.', 'ahasend-email') . '</p></div>';
    }

    // Retrieve current option values
    $api_key    = get_option('ahasend_api_key');
    $from_email = get_option('ahasend_from_email');
    $from_name  = get_option('ahasend_from_name');

    ?>
    <div class="wrap">
        <h1><?php esc_html_e('AhaSend Email Settings', 'ahasend-email'); ?></h1>
        <form method="post">
            <?php wp_nonce_field('ahasend_save_settings'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="ahasend_api_key"><?php esc_html_e('API Key', 'ahasend-email'); ?></label></th>
                    <td><input type="text" id="ahasend_api_key" name="ahasend_api_key" value="<?php echo esc_attr($api_key); ?>" class="regular-text" required /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="ahasend_from_email"><?php esc_html_e('From Email', 'ahasend-email'); ?></label></th>
                    <td><input type="email" id="ahasend_from_email" name="ahasend_from_email" value="<?php echo esc_attr($from_email); ?>" class="regular-text" required /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="ahasend_from_name"><?php esc_html_e('From Name', 'ahasend-email'); ?></label></th>
                    <td><input type="text" id="ahasend_from_name" name="ahasend_from_name" value="<?php echo esc_attr($from_name); ?>" class="regular-text" required /></td>
                </tr>
            </table>
            <?php submit_button(__('Save Settings', 'ahasend-email'), 'primary', 'ahasend_save_settings'); ?>
        </form>
    </div>
    <?php
}
