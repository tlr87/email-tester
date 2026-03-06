<?php
/**
 * Plugin Name: Email Tester Advanced
 * Description: Test outgoing emails with CC/BCC, HTML preview, and logging.
 * Version: 1.1
 * Author: Your Name
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Add admin menu
add_action('admin_menu', 'email_tester_advanced_menu');
function email_tester_advanced_menu() {
    add_menu_page(
        'Email Tester Advanced',
        'Email Tester',
        'manage_options',
        'email-tester-advanced',
        'email_tester_advanced_page',
        'dashicons-email',
        90
    );
}

// Admin page content
function email_tester_advanced_page() {
    ?>
    <div class="wrap">
        <h1>Email Tester Advanced</h1>
        <?php
        if ( isset($_POST['email_tester_send']) && !empty($_POST['email_address']) ) {
            $to = sanitize_email($_POST['email_address']);
            $subject = sanitize_text_field($_POST['subject'] ?? 'WordPress Email Test');
            $message = wp_kses_post($_POST['message'] ?? 'This is a test email from your WordPress site.');
            $cc = sanitize_text_field($_POST['cc'] ?? '');
            $bcc = sanitize_text_field($_POST['bcc'] ?? '');
            $is_html = isset($_POST['is_html']) ? true : false;

            $headers = [];
            if ($is_html) $headers[] = 'Content-Type: text/html; charset=UTF-8';
            if (!empty($cc)) $headers[] = 'Cc: ' . $cc;
            if (!empty($bcc)) $headers[] = 'Bcc: ' . $bcc;

            // Attempt to send email
            $success = wp_mail($to, $subject, $message, $headers);

            // Log for debugging
            error_log("Email Tester Advanced: To=$to, CC=$cc, BCC=$bcc, Subject=$subject, HTML=" . ($is_html ? 'Yes' : 'No') . ", Success=" . ($success ? 'Yes' : 'No'));

            // Show result
            if ($success) {
                echo '<div style="padding:10px; background-color:#d4edda; color:#155724;">Email successfully sent to ' . esc_html($to) . '</div>';
                if ($is_html) {
                    echo '<h2>Email Preview:</h2>';
                    echo '<div style="padding:10px; border:1px solid #ccc;">' . $message . '</div>';
                }
            } else {
                echo '<div style="padding:10px; background-color:#f8d7da; color:#721c24;">Failed to send email. Check your server configuration.</div>';
            }
        }
        ?>
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th><label for="email_address">To (Email Address)</label></th>
                    <td><input name="email_address" type="email" id="email_address" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="subject">Subject</label></th>
                    <td><input name="subject" type="text" id="subject" class="regular-text" value="WordPress Email Test"></td>
                </tr>
                <tr>
                    <th><label for="message">Message</label></th>
                    <td><textarea name="message" id="message" rows="8" class="large-text">This is a test email from your WordPress site.</textarea></td>
                </tr>
                <tr>
                    <th><label for="cc">CC</label></th>
                    <td><input name="cc" type="text" id="cc" class="regular-text" placeholder="Optional, comma separated"></td>
                </tr>
                <tr>
                    <th><label for="bcc">BCC</label></th>
                    <td><input name="bcc" type="text" id="bcc" class="regular-text" placeholder="Optional, comma separated"></td>
                </tr>
                <tr>
                    <th>HTML Email</th>
                    <td><label><input type="checkbox" name="is_html" value="1"> Send as HTML</label></td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="email_tester_send" id="email_tester_send" class="button button-primary" value="Send Test Email">
            </p>
        </form>
    </div>
    <?php
}