=== Lymos Smtp Email – Send Email and Email Logs Lightweight ===
Contributors: lymoswp
Requires at least: 4.6
Tested up to: 6.8
Stable tag: 1.0.2
License: GPLv2 or later
Tags: SMTP, EMAIL, RECORD, LOGS, Order Email

A plugin to send email and record the email logs. Lightweight and easy. You can use Gmail, Outlook, Yahoo and so on.

== Description ==
A plugin to send email and record the email logs. Lightweight and easy. You can use Gmail, Outlook, Yahoo and so on.

1. Smtp or pop send email.
2. Good styles and light styles.
3. Email records. You can record the email sent logs in the systems.
4. Email was opened callback coming soon.

Any other support or bugs or issues, contact us [Lymoswp](https://lymoswp.fly.dev/) please! Thank you!

== Requirements ==

* Wordpress 4.6 or later.

== Lymos Smtp Email Plugin Usage ==

Once you have installed the plugin there are some options that you need to configure in the plugin setttings (go to `Settings->Lymos Smtp Email` from your WordPress Dashboard).

**a)**  Settings

The settings section consists of the following options

* From Email Address: The email address that will be used to send emails to your recipients
* From Name: The name your recipients will see as part of the "from" or "sender" value when they receive your message
* SMTP Host: Your outgoing mail server (example: smtp.gmail.com)
* SSL/TLS On: Open SSL/TLS or none
* Record Logs On: Will record email logs when it was opened
* SMTP Port: The port that will be used to relay outbound mail to your mail server (example: 465)
* SMTP Username: username that you use to login to your mail server
* SMTP Password: password that you use to login to your mail server

**b)** Test your email

This tab allows you to perform the email testing to make sure that your WordPress site is ready to relay all outgoing emails to your configured SMTP server. It consists of the following options:

* To: The email address that will be used to send emails to your recipients
* Subject: The subject of your email
* Message: A textarea to write your test email.

Once you click the "Send" button the plugin will try to send an email to the recipient specified in the "To" field.

**c)** Email Records section

This section will show the email records when option "Record Logs On" was opened.

== Installation ==

1. Upload `lymos-smtp-email` to the `/wp-content/plugins/` directory;
2. Activate the plugin through the 'Plugins > Installed Plugins' menu in WordPress dashboard;
3. Done!

== Screenshots ==

1. SMTP Settings
2. Email Test


== Changelog ==

= 1.0.2 =
- update readme

= 1.0.1 =
- Initial release.

