# WP Email Debugger

**WP Email Debugger** helps you troubleshoot WordPress email delivery issues by sending test emails with full debug logs. Logs include headers, sendmail path, and result. Logs are displayed in the admin and emailed to the site administrator.

---

## Features

- Send test emails from WordPress with **To, CC, BCC** fields.
- Optional **HTML email** content.
- Logs email headers, sendmail path, and send result via `error_log()`.
- Displays the debug log in WordPress admin.
- Emails a debug log to the admin email for review.
- Ideal for diagnosing PHP `mail()` or SMTP delivery issues.

---

## Installation

1. Upload the `wp-email-debugger` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Go to **Email Debugger** in the admin menu.
4. Fill out the **To**, **CC**, **BCC**, **Subject**, and **Message** fields.
5. Check **Send as HTML** if needed.
6. Click **Send Test Email** to send and receive the debug log.

---

## Frequently Asked Questions

**Q:** Can I use this with SMTP plugins?  
**A:** Yes. WP Email Debugger works with the default `wp_mail()`. If you have an SMTP plugin, it respects its configuration.

**Q:** Will the plugin actually send emails?  
**A:** Yes. It sends real emails to the recipients and also emails a copy of the debug log to the WordPress admin.

**Q:** Does it store emails anywhere?  
**A:** No. It only logs the attempt temporarily and emails the debug log to the admin. No database storage.

---

## Screenshots

1. Email Debugger form in WordPress admin.  
2. Debug log preview after sending a test email.

---

## Changelog

### 1.1
- Added email logging and sending debug log to admin.
- Supports **To, CC, BCC** and HTML preview.
- Uses `error_log()` for full debugging.

### 1.0
- Initial release with basic test email form.

---