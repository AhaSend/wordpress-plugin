=== AhaSend Email Sender ===
Contributors: yourusername
Tags: email, sender, AhaSend, wp_mail, custom email provider
Requires at least: 5.0
Tested up to: 6.3
Requires PHP: 7.2
Stable tag: 1.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Replaces WordPress default email sender with AhaSend API to manage and send emails.

== Description ==

**AhaSend Email Sender** replaces the default WordPress email system with the AhaSend email provider. This plugin uses AhaSend’s API to send emails directly from your WordPress site, allowing you to leverage AhaSend’s email sending features for better deliverability and tracking.

This plugin is perfect for users who want to:

* Use AhaSend as their email provider within WordPress
* Send WordPress notifications, contact form emails, and other automated emails through AhaSend
* Configure AhaSend API settings directly from the WordPress admin panel

== Features ==

* Seamlessly integrates AhaSend API with WordPress.
* Replaces default `wp_mail` function with AhaSend for improved email deliverability.
* Simple settings page to manage AhaSend API key, sender email, and sender name.
* Fully translatable and localized for multilingual support.

== Installation ==

1. Upload the `ahasend-email` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to **AhaSend Email** in the WordPress admin sidebar menu.
4. Enter your AhaSend API key, sender email, and sender name.
5. Click **Save Settings** to start using AhaSend for all WordPress emails.

== Frequently Asked Questions ==

= What is AhaSend, and why should I use it? =

AhaSend is an email sending platform that provides better deliverability and email tracking options. Using AhaSend allows you to bypass typical email issues associated with WordPress's default mailer, enhancing the chances of your emails reaching the inbox.

= How do I get my AhaSend API key? =

You can obtain your API key by logging into your AhaSend account and navigating to the API section. Once you have the key, enter it in the plugin settings page under **AhaSend Email** in WordPress.

= Does this plugin support multilingual sites? =

Yes, the plugin is fully translatable. The translation files can be found in the `/languages` folder, and it is compatible with plugins like Loco Translate for easy localization.

== Screenshots ==

1. **Settings Page** - Simple form to configure API key and sender information.

== Changelog ==


= 1.0 =
* Initial release with core email sending functionality.

== Upgrade Notice ==

= 1.0 =
??

== License ==

This plugin is licensed under the GPLv2 or later. 
For details, see [GNU General Public License](https://www.gnu.org/licenses/gpl-2.0.html).
