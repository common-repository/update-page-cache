=== Update Page Cache ===
Contributors: giuse
Donate link:
Tags: update cache, page cache
Requires at least: 4.6
Tested up to: 5.9
Stable tag: 0.0.5
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Update Page Cache flushes and generates again the page cache. Compatible with W3 Total Cache, WP Fastest Cache, WP Super Cache, WP Optimize.

== Description ==

Update Page Cache flushes and generates again the page cache. 

The actual version supports <a href="https://wordpress.org/plugins/w3-total-cache/">W3 Total Cache</a>, <a href="https://wordpress.org/plugins/wp-fastest-cache/" target="_blank" rel="noopener">WP Fastest Cache</a>, <a href="https://wordpress.org/plugins/wp-super-cache/">WP Super Cache</a>, and <a href="https://wordpress.org/plugins/wp-optimize/">WP Optimize</a>.

When you modify a page and save it, usually caching plugins automatically clear the cache of that page, but you need to visit again the page to generate again the cache.

Update Page Cache adds a button on every single page, post and custom post type that you can click to automatically clear and regenerate the cache of that page. Be careful, you will not see the button if you have Fullscreen mode active.

When you edit a single page, if you click on the "Update Page Cache" button, the cache of that page will be automatically updated without the need to visit that page.

In the main settings you can set for each post types if the plugin should automatically clear the page cache when that single post type is saved.

You can also decide which page caches should be updated after saving a specific post type.
In the example shown in the screenshot of the main settings, when a single blog post is saved Update Page Cache updates the cache of the blog page and homepage. When a contact form is saved, Update Page Cache updates the cache of the page Contact.


== Installation ==

1. Upload the entire `update-page-cache` folder to the `/wp-content/plugins/` directory or install it using the usual installation button in the Plugins administration page.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. All done. Good job!



== Changelog ==

= 0.0.5 =
* Changed: pages added on dropdown list changes instead of clicking on the "+" button
* Added: Settings and Support links in the plugins page

= 0.0.4 =
* Added: settings to automatically update the cache of specific pages when specific single post types are saved
* Added: settings to automatically update the cache of a single post if that single post is saved

= 0.0.3 =
* Improved: Cache updated via ajax instead of refreshing the page

= 0.0.2 =
* Fixed: Header already sent

= 0.0.1 =
* Initial release



== Screenshots ==

1. Main settings page
2. "Update page cache" button added to single pages, posts and custom posts in the backend