=== AhaSend Email API ===
Contributors: ahasend
Tags: email,mailersend,phpmailer,smtp,wp_mail
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.3
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Connect your WordPress site to AhaSend for reliable, fast transactional email delivery with easy SMTP integration and real-time tracking.

== Description ==
Most hosting providers aren\'t equipped to handle high-volume email sending or guarantee fast, reliable delivery, leading to delayed emails and poor inbox placement.
The AhaSend WordPress plugin seamlessly connects your WordPress site with AhaSend’s [reliable email delivery platform](https://ahasend.com) via an HTTP API, improving email sending performance compared to sending with SMTP and bypassing issues such as blocked SMTP ports by hosting providers. Optimize your transactional emails with easy integration and advanced features designed for speed and enhanced inbox placement. With AhaSend, you benefit from real-time tracking, customizable data retention, and secure email handling to ensure efficient and accurate delivery. Perfect for e-commerce, membership sites, and more, AhaSend’s plugin provides robust, fast email solutions without the hassle.

== External services ==
This plugin sends email content to the AhaSend API everytime WordPress needs to send an email, and AhaSend - as an Email Service Provider - delivers the email to the recipients. 
Please review AhaSends [Terms of Use](https://ahasend.com/terms) and [Privacy Policy](https://ahasend.com/privacy) before using this plugin.

== Installation ==
* Install and activate the AhaSend plugin.
* Open your [AhaSend dashboard](https://dash.ahasend.com/accounts).
* Go to the Credentials tab.
* Create an API Key.
* Copy the key.
* Paste the key into the AhaSend plugin configuration form.
* Start sending emails!

== Frequently Asked Questions ==
= Need help? =
* Our support team is available 24/7 to assist with any issues you may encounter while sending your transactional emails. Contact us anytime at support@ahasend.com.

== Screenshots ==
1. Setting page

== Changelog ==

= 1.3 =
* Creating a plugin in the mu-plugins directory instead of using the wp_mail hook to override it.

= 1.2.1 =
* Fix issue with status code checking.

= 1.2.0 =
* Update Stable tag

= 1.0.0 =
* Initial Release
