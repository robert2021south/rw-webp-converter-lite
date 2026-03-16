=== RW WebP Converter Lite ===
Contributors: robert2021south
Tags: webp, image optimization, image converter, performance, media library
Donate link: http://ko-fi.com/robertsouth
Requires at least: 6.6
Tested up to: 6.9
Requires PHP: 8.2
Stable tag: 1.2.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Language: en_US
Languages Folder: /languages
Text Domain: rw-webp-converter-lite
Update URI: https://wordpress.org/rw-webp-converter-lite/

A lightweight WordPress plugin that converts JPG and PNG images to WebP format in bulk and automatically converts newly uploaded images.

== Description ==
**RW WebP Converter Lite** is a lightweight and easy-to-use WordPress plugin that helps you optimize images by converting JPG and PNG files into the modern WebP format.

The plugin allows you to bulk convert existing images in the media library and automatically convert newly uploaded images to WebP. By reducing image file size while maintaining quality, RW WebP Converter Lite helps improve website loading speed and overall performance.

All features are optional and can be configured from the plugin settings page.

**Features**:
* Bulk convert existing JPG and PNG images in the media library to WebP
* Automatically convert newly uploaded images to WebP
* Enable or disable automatic conversion via settings
* Lightweight and fast, with no unnecessary features
* Compatible with the WordPress media library

**Documentation**:
[Plugin Documentation](https://docs.robertwp.com/rw-webp-converter-lite/)
Includes setup and usage instructions for this plugin.

== Installation ==

1. Upload the `rw-webp-converter-lite` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the **Plugins** menu in WordPress
3. Go to **Tools → RW WebP Converter Lite** to configure settings

== Source Code ==

This plugin includes minified JavaScript and CSS files for performance
and distribution purposes.

The original, human-readable source code for these files is publicly
available and maintained on GitHub:

https://github.com/robert2021south/rw-webp-converter-lite

The following files are minified in the plugin package:
- assets/js/rwwcl-admin-bulk.min.js
- assets/js/rwwcl-admin-deactivate.min.js
- assets/js/rwwcl-admin-feedback.min.js
- assets/css/rwwcl-admin-style.min.css
- assets/css/rwwcl-admin-deactivate-modal.min.css


== Frequently Asked Questions ==

= Does this plugin replace my original images? =
The plugin converts images to WebP format. Depending on your settings, original images may be kept alongside WebP versions.

= Can I disable automatic conversion on upload? =
Yes. Automatic image conversion can be enabled or disabled from the plugin settings page.

= Does this plugin support existing images? =
Yes. You can bulk convert existing JPG and PNG images in the media library.

= Is this plugin lightweight? =
Yes. The plugin is designed to be lightweight and focuses only on WebP image conversion without unnecessary features.

== Screenshots ==

1. Plugin settings page
2. Bulk image conversion interface
3. Media library WebP conversion status

== Changelog ==

= 1.2.0 =
* Added feedback form in settings page
* Implemented star rating system with half-star support
* Added option to leave feedback with email (optional)
* Enhanced user experience with toast notifications

= 1.1.0 =
* Added a feedback survey modal when users deactivate the plugin.
  - Collects reasons for deactivation and optional details.
  - Asynchronously sends feedback to remote server without blocking user action.
  - Includes improved UI/UX with polite prompts, responsive modal, and privacy notice.

= 1.0.3 =
* Updated documentation to include public source code reference.

= 1.0.2 =
* Improved input validation and output handling.
* Updated plugin documentation for consistency.

= 1.0.1 =
* Removed Pro-related texts, logos, and references.

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.2.0 =
* Added feedback form in settings page
* Implemented star rating system with half-star support
* Added option to leave feedback with email (optional)
* Enhanced user experience with toast notifications

== Privacy ==
This plugin respects your privacy. Any data collected (such as optional feedback or deactivation reasons) is handled transparently and securely.

**Feedback Data:** When you use the feedback form in the About tab, we collect your rating, feedback message, and optionally your email address. This helps us understand user needs and improve the plugin.
**Deactivation Data:** When you uninstall the plugin, you may optionally provide a reason for leaving. This helps us identify areas for improvement.

All data, including any optional email address, is transmitted securely (via TLS/HTTPS) and retained only as long as necessary. For example, feedback data is retained for a maximum of 12 months to help us improve the plugin.

We do not collect any personal data, site URLs, or user-identifiable information without your explicit consent. Email addresses are only collected when voluntarily provided and are never used for marketing.

For complete details on what data is collected, how it is stored (including hashing and encryption for email addresses), the legal basis under GDPR/CCPA, and your rights, please read our full privacy policy:
https://robertwp.com/privacy-policy
