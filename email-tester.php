<?php
/**
 * Plugin Name: Email Tester
 * Description: Simple plugin to test outgoing emails in WordPress.
 * Version: 1.0
 * Author: Your Name
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Add admin menu
add_action('admin_menu', 'email_tester_menu');
function email_tester_menu() {
    add_menu_page(
        'Email Tester',         // Page title
        'Email Tester',         // Menu title
        'manage_options',       // Capability
        'email-tester',         // Menu slug
        'email_tester_page',    // Callback function
        'dashicons-email',      // Icon
        90                      // Position
    );
}

// Admin page content
function email_tester_page() {
    ?>
    <div class="wrap">
        <h1>Email Tester</h1>
        <?php
        if ( isset($_POST['email_tester_send']) && !empty($_POST['email_address']) ) {
            $to = sanitize_email($_POST['email_address']);
            $subject = 'WordPress Email Test';
            $message = 'This is a test email sent from your WordPress site.';
            $headers = ['Content-Type: text/html; charset=UTF-8'];

            if ( wp_mail($to, $subject, $message, $headers) ) {
                echo '<div style="padding:10px; background-color:#d4edda; color:#155724;">Email successfully sent to ' . esc_html($to) . '</div>';
            } else {
                echo '<div style="padding:10px; background-color:#f8d7da; color:#721c24;">Failed to send email. Check your server configuration.</div>';
            }
        }
        ?>
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th><label for="email_address">Email Address</label></th>
                    <td><input name="email_address" type="email" id="email_address" class="regular-text" required></td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="email_tester_send" id="email_tester_send" class="button button-primary" value="Send Test Email">
            </p>
        </form>
    </div>
    <?php
}