<?php
/*
Plugin Name: WP Email Debugger
Description: Send WordPress test emails and receive detailed debug logs.
Version: 1.1
Author: RD3
*/

if (!defined('ABSPATH')) exit;

add_action('admin_menu', function () {
    add_menu_page(
        'Email Debugger',
        'Email Debugger',
        'manage_options',
        'wp-email-debugger',
        'wp_email_debugger_page'
    );
});

function wp_email_debugger_page() {

    $log = '';

    if (isset($_POST['send_test'])) {

        $to = sanitize_text_field($_POST['to']);
        $cc = sanitize_text_field($_POST['cc']);
        $bcc = sanitize_text_field($_POST['bcc']);
        $subject = sanitize_text_field($_POST['subject']);
        $message = wp_kses_post($_POST['message']);
        $html = isset($_POST['html']);

        $headers = [];

        $headers[] = 'From: WordPress <wordpress@' . $_SERVER['SERVER_NAME'] . '>';

        if ($cc) $headers[] = 'Cc: ' . $cc;
        if ($bcc) $headers[] = 'Bcc: ' . $bcc;

        if ($html) {
            $headers[] = 'Content-Type: text/html; charset=UTF-8';
        }

        $log .= "Sending with mail()\n";
        $log .= "Sendmail path: " . ini_get('sendmail_path') . "\n";
        $log .= "Envelope sender: " . ini_get('sendmail_from') . "\n";
        $log .= "To: $to\n";
        $log .= "CC: $cc\n";
        $log .= "BCC: $bcc\n";
        $log .= "Subject: $subject\n";
        $log .= "Headers:\n" . implode("\n", $headers) . "\n";

        error_log("WP Email Debugger Attempt:\n" . $log);

        $result = wp_mail($to, $subject, $message, $headers);

        $log .= "\nResult: " . ($result ? "true" : "false") . "\n";

        error_log("WP Email Debugger Result: " . ($result ? "true" : "false"));

        // Send the log to admin email
        $admin_email = get_option('admin_email');

        wp_mail(
            $admin_email,
            "WordPress Email Debug Log",
            nl2br($log),
            ['Content-Type: text/html; charset=UTF-8']
        );
    }

    ?>

    <div class="wrap">
        <h1>Email Debugger</h1>

        <form method="post">

            <table class="form-table">

                <tr>
                    <th>To</th>
                    <td><input type="email" name="to" class="regular-text" required></td>
                </tr>

                <tr>
                    <th>CC</th>
                    <td><input type="text" name="cc" class="regular-text"></td>
                </tr>

                <tr>
                    <th>BCC</th>
                    <td><input type="text" name="bcc" class="regular-text"></td>
                </tr>

                <tr>
                    <th>Subject</th>
                    <td><input type="text" name="subject" class="regular-text" value="WordPress Email Test"></td>
                </tr>

                <tr>
                    <th>Message</th>
                    <td>
                        <textarea name="message" rows="6" class="large-text">
This is a WordPress test email.
                        </textarea>
                    </td>
                </tr>

                <tr>
                    <th>HTML Email</th>
                    <td>
                        <label>
                            <input type="checkbox" name="html">
                            Send as HTML
                        </label>
                    </td>
                </tr>

            </table>

            <p>
                <input type="submit" name="send_test" class="button button-primary" value="Send Test Email">
            </p>

        </form>

        <?php if (!empty($log)) : ?>

            <h2>Email Log</h2>

            <pre style="background:#000;color:#0f0;padding:20px;">
<?php echo esc_html($log); ?>
            </pre>

        <?php endif; ?>

    </div>

    <?php
}