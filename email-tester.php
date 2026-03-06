<?php
/**
 * Plugin Name: Email Tester Advanced
 * Description: Test outgoing emails with CC/BCC, HTML preview, and step-by-step PHPMailer debug output.
 * Version: 1.2
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

            // Clear previous debug logs
            delete_transient('email_tester_debug');

            // Hook into PHPMailer to capture debug
            add_action('phpmailer_init', function($phpmailer){
                $phpmailer->SMTPDebug = 2; // 1 = commands, 2 = commands + responses
                $phpmailer->Debugoutput = function($str, $level) {
                    $logs = get_transient('email_tester_debug') ?: [];
                    $logs[] = $str;
                    set_transient('email_tester_debug', $logs, 60);
                    error_log("WP Mail Debug: " . $str);
                };
            });

            // Send email
            $success = wp_mail($to, $subject, $message, $headers);

            // Retrieve debug logs
            $logs = get_transient('email_tester_debug') ?: [];
            delete_transient('email_tester_debug');

            // Show results
            if ($success) {
                echo '<div style="padding:10px; background-color:#d4edda; color:#155724;">Email successfully sent to ' . esc_html($to) . '</div>';
            } else {
                echo '<div style="padding:10px; background-color:#f8d7da; color:#721c24;">Email failed. See debug output below.</div>';
            }

            // Show HTML preview if applicable
            if ($is_html) {
                echo '<h2>Email Preview:</h2>';
                echo '<div style="padding:10px; border:1px solid #ccc; background:#f9f9f9;">' . $message . '</div>';
            }

            // Show debug output
            if (!empty($logs)) {
                echo '<h2>Debug Output</h2>';
                echo '<div style="padding:10px; border:1px solid #ccc; max-height:300px; overflow:auto; background:#f9f9f9;">';
                foreach($logs as $line){
                    echo esc_html($line) . "<br>";
                }
                echo '</div>';
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