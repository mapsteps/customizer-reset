=== Customizer Reset - Export & Import ===
Contributors: davidvongries
Tags: customizer reset, customizer export, customizer import, reset, customizer
Requires at least: 4.7
Tested up to: 7.1
Requires PHP: 5.6
Stable tag: 1.5
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Reset, export and import your WordPress Customizer settings with one click. Works with any theme.

== Description ==

**Customizer Reset** adds three simple tools to the bottom of the WordPress Customizer: **reset**, **export** and **import**. Start fresh with one click, or move your Customizer settings to another site or child theme in seconds.

== Features ==

= 🔄 Reset the Customizer =
Made a mess in the Customizer and want to start over without undoing every setting by hand? Reset all Customizer settings of the active theme with one click.

= 📤 Export Customizer settings =
Take a snapshot of your Customizer settings and download it as a JSON file. Great as a backup before you try something new.

= 📥 Import Customizer settings =
Import an export file to move your settings to another WordPress site or child theme. Images used in your settings (e.g. the logo or header image) are imported into the media library too.

= 🎨 Works with any theme =
Customizer Reset works with every theme that uses the WordPress Customizer. It was originally built as a helper plugin for the [Page Builder Framework](https://wp-pagebuilderframework.com/?utm_source=customizer_reset&utm_medium=repository&utm_campaign=wpbf) theme.

== How to use it ==

1. Go to *Appearance → Reset Customizer* (or *Appearance → Customize*).
2. Find the tools at the bottom of the Customizer sidebar:
	* **Reset Customizer** resets all Customizer settings of the active theme.
	* **Export** downloads your settings as a JSON file.
	* **Import** opens a form to upload an export file.

Tip: export your settings before resetting or importing, so you can always go back.

---------

=== Advanced ===

By default, the export contains the theme mods, Customizer options of the active theme and Additional CSS. To add other options to the export, use the filter below:

`function prefix_customizer_export_option_keys( $option_keys ) {
	$option_keys[] = 'my_plugin_option';
	return $option_keys;
}
add_filter( 'customizer_export_option_keys', 'prefix_customizer_export_option_keys' );`

---------

=== What's next? ===

If you like Customizer Reset, make sure to check out our other products:

* **[Page Builder Framework](https://wp-pagebuilderframework.com/?utm_source=customizer_reset&utm_medium=repository&utm_campaign=wpbf)** - A fast & minimalistic WordPress theme designed for the new WordPress era.
* **[Ultimate Dashboard](https://ultimatedashboard.io/?utm_source=customizer_reset&utm_medium=repository&utm_campaign=udb)** - The #1 WordPress plugin to customize your WordPress dashboard and admin area.
* **[Better Admin Bar](https://betteradminbar.com/?utm_source=customizer_reset&utm_medium=repository&utm_campaign=bab)** - The plugin to make your clients enjoy WordPress. It replaces the default admin bar to provide the best possible user experience when editing & navigating a website.
* **[WP Video Popup](https://wp-video-popup.com/?utm_source=customizer_reset&utm_medium=repository&utm_campaign=wp_video_popup)** - Add a YouTube, Vimeo or Rumble video popup to WordPress. Lightweight, privacy-friendly lightbox that works with any page builder.

== Installation ==
1. In your WordPress admin, go to *Plugins → Add New Plugin* and search for "Customizer Reset".
2. Click *Install Now*, then *Activate*.
3. Go to *Appearance → Reset Customizer* to reset, export or import your Customizer settings.

== Frequently Asked Questions ==

= How do I reset the WordPress Customizer? =
Go to *Appearance → Reset Customizer* and click the red **Reset Customizer** button at the bottom of the Customizer sidebar. After you confirm, all Customizer settings of the active theme are reset.

= What does the reset remove? =
It removes the theme mods (settings stored by the active theme) that are registered in the Customizer. Site title, tagline, homepage settings, menus, widgets and Additional CSS are not touched.

= Can I undo a reset? =
No. Export your settings first, then you can import them again at any time.

= What does the export contain? =
Theme mods, Customizer options of the active theme and Additional CSS. Site title, tagline, homepage settings, menus and widgets are not exported.

= Can I import settings from a different theme? =
No. An export can only be imported into the same theme (or a child theme of it), since every theme has its own settings.

= Does it work with my theme? =
Yes, Customizer Reset works with every theme that uses the WordPress Customizer. Block themes that only use the Site Editor don't have Customizer settings to reset.

== Screenshots ==
1. Reset, export & import tools at the bottom of the Customizer

== Changelog ==
= 1.5 November 08, 2026 =
* Fixed: Security improvements (capability checks for reset, export & import)
* Fixed: Imported files are no longer stored in the uploads folder
* Fixed: Images that already exist on the site are no longer imported twice
* Fixed: Reset button stayed disabled and showed no message when a reset failed
* Fixed: Import errors with special characters were displayed incorrectly
* Tweak: Refreshed design of the reset, export & import controls
* Tweak: Export & import buttons and the import form are keyboard accessible
* Tweak: "Reset Customizer" menu label is now translatable
* Tweak: Requires WordPress 4.7
* Tested up to WordPress 7.1
= 1.4.1 November 1, 2025 =
* Tested up to WordPress 6.8
= 1.4 May 10, 2024 =
* Fixed: Deprecated messages
* Tested up to WordPress 6.5
= 1.3 November 30, 2023 =
* Tested up to WordPress 6.4
= 1.2 February 03, 2023 =
* Tweak: Improved wording
= 1.1.5 April 19, 2022 =
* Tweak: Only display reset link in appearance menu if user can manage options
= 1.1.4 March 02, 2022 =
* Tested up to WordPress 5.9
* Updated description
* Fixed: Typo
= 1.1.3 June 20, 2020 =
* Fixed: Reset button is still blue on :focus
= 1.1.2 June 3, 2020 =
* Tweak: Changed reset button color to red
= 1.1.1 December 16, 2019 =
* Fixed: Fatal error on import
* Tweaked wording
= 1.1 December 16, 2019 =
* Added export/import functionality
= 1.0.1 December 05, 2019 =
* Minor tweaks & improvements
= 1.0 December 03, 2019 =
* Initial Release
