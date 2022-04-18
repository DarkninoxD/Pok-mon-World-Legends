=== LiteSpeed Cache ===
Contributors: LiteSpeedTech
Tags: caching, optimize, performance, pagespeed, seo, speed, image optimize, compress, object cache, redis, memcached, database cleaner
Requires at least: 4.0
Tested up to: 5.2.1
Stable tag: 2.9.8.2
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html

All-in-one unbeatable acceleration & PageSpeed improvement: caching, image/CSS/JS optimization...

== Description ==

LiteSpeed Cache for WordPress (LSCWP) is an all-in-one site acceleration plugin, featuring an exclusive server-level cache and a collection of optimization features.

LSCWP supports WordPress Multisite and is compatible with most popular plugins, including WooCommerce, bbPress, and Yoast SEO.

== Requirements ==
**General Features** may be used by anyone with any web server (LiteSpeed, Apache, NGiNX, etc.).

**LiteSpeed Exclusive Features** require OpenLiteSpeed, commercial LiteSpeed products, LiteSpeed-powered hosting, or [the new QUIC.cloud CDN](https://quic.cloud), now in beta. [Why?](https://www.litespeedtech.com/support/wiki/doku.php/litespeed_wiki:cache:lscwp:faq#why_do_the_cache_features_require_litespeed_server)

== Plugin Features ==

= General Features =

* Free QUIC.cloud CDN Cache
* Object Cache (Memcached/LSMCD/Redis)
* Image Optimization (Lossless/Lossy)
* Minify CSS, JavaScript, and HTML
* Minify inline CSS/JS
* Combine CSS/JS
* Automatically generate Critical CSS
* Lazyload images/iframes
* Multiple CDN support
* Load CSS/JS Asynchronously
* Browser Cache
* Database Cleaner and Optimizer
* PageSpeed score optimization
* OPcode Cache
* HTTP/2 Push for CSS/JS (on web servers that support it)
* DNS Prefetch
* Cloudflare API
* Single Site and Multi Site (Network) support
* Import/Export settings
* Basic/Advanced setting view
* Attractive, easy-to-understand interface
* WebP image format support
* Heartbeat control

= LiteSpeed Exclusive Features =

* Automatic page caching to greatly improve site performance
* Automatic purge of related pages based on certain events
* Private cache for logged-in users
* Caching of WordPress REST API calls
* Separate caching of desktop and mobile views
* Ability to schedule purge for specified URLs
* WooCommerce and bbPress support
* [WordPress CLI](https://www.litespeedtech.com/support/wiki/doku.php/litespeed_wiki:cache:lscwp#wordpress_cli) commands
* API system for easy cache integration
* Exclude from cache by URI, Category, Tag, Cookie, User Agent
* Smart preload crawler with support for SEO-friendly sitemap
* Multiple crawlers for cache varies
* HTTP/2 & [QUIC](https://blog.litespeedtech.com/2017/07/11/litespeed-announces-quic-support/) support<sup>*</sup>
* ESI (Edge Side Includes) support<sup>*</sup>
* Widgets and [Shortcodes](https://www.litespeedtech.com/support/wiki/doku.php/litespeed_wiki:cache:lscwp:configuration:esi:shortcode) as ESI blocks<sup>*</sup>

<sup>*</sup> Feature not available in OpenLiteSpeed

== Screenshots ==

1. Plugin Benchmarks
2. Admin Settings - Cache
3. Admin Settings - Purge
4. Admin Settings - Excludes
5. Admin Settings - Optimize
6. Admin Settings - Tuning
7. Admin Settings - Media
8. Admin Settings - CDN
9. Admin Settings - ESI
10. Admin Settings - Crawler
11. Admin Settings - Thirdparty WooCommerce
12. Admin Management - Purge
13. Admin Management - DB Optimizer
14. Image Optimization
15. Admin Crawler Status Page
16. Cache Miss Example
17. Cache Hit Example
18. Frontend Adminbar Shortcut

== LSCWP Resources ==
* [Join our Slack community](https://goo.gl/FG9S4N) to connect with other LiteSpeed users.
* [Ask a question on our support forum](https://wordpress.org/support/plugin/litespeed-cache/).
* [View detailed documentation on our wiki](https://www.litespeedtech.com/support/wiki/doku.php/litespeed_wiki:cache:lscwp).
* [Read WordPress Wednesday tutorials on our blog](http://blog.litespeedtech.com/tag/wordpress-wednesday).
* [Help translate LSCWP](https://translate.wordpress.org/projects/wp-plugins/litespeed-cache).
* [LSCWP GitHub repo](https://github.com/litespeedtech/lscache_wp).

== Installation ==

[View detailed documentation on our wiki](https://www.litespeedtech.com/support/wiki/doku.php/litespeed_wiki:cache:lscwp).

= For Optimization Without a LiteSpeed Web Server =
1. Install the LiteSpeed Cache for WordPress plugin and activate it.
1. From the WordPress Dashboard, navigate to **LiteSpeed Cache -> Settings**. Click **Advanced View**, and enable the available optimization features in the various tabs.

= For Caching and Optimization With a LiteSpeed Web Server =
1. Install [LiteSpeed Web Server Enterprise](https://www.litespeedtech.com/products/litespeed-web-server) with LSCache Module, [LiteSpeed Web ADC](https://www.litespeedtech.com/products/litespeed-web-adc), or [OpenLiteSpeed](https://www.litespeedtech.com/open-source/openlitespeed) with cache module [Free].
1. Install the LiteSpeed Cache for WordPress plugin and activate it.
1. From the WordPress Dashboard, navigate to **LiteSpeed Cache -> Settings**, make sure the option **Enable LiteSpeed Cache** is set to `Enable`.
1. Click **Advanced View** to enable any desired optimization features in the various tabs.

= Notes for LiteSpeed Web Server Enterprise =

* Make sure that your license includes the LSCache module. A [2-CPU trial license with LSCache module](https://www.litespeedtech.com/products/litespeed-web-server/download/get-a-trial-license "trial license") is available for free for 15 days.
* The server must be configured to have caching enabled. If you are the server admin, [click here](https://www.litespeedtech.com/support/wiki/doku.php/litespeed_wiki:cache:common_installation#web_server_configuration) for instructions. Otherwise, please request that the server admin configure the cache root for the server.

= Notes for OpenLiteSpeed =

* This integration utilizes OpenLiteSpeed's cache module.
* If it is a fresh OLS installation, the easiest way to integrate is to use [ols1clk](http://open.litespeedtech.com/mediawiki/index.php/Help:1-Click_Install). If using an existing WordPress installation, use the `--wordpresspath` parameter.
* If OLS and WordPress are both already installed, please follow the instructions in [How To Set Up LSCache For WordPress](http://open.litespeedtech.com/mediawiki/index.php/Help:How_To_Set_Up_LSCache_For_WordPress).

== Third Party Compatibility ==

The vast majority of plugins and themes are compatible with LSCache. [Our API](https://www.litespeedtech.com/support/wiki/doku.php/litespeed_wiki:cache:lscwp:api) is available for those that are not. Use the API to customize smart purging, customize cache rules, create cache varies, and make WP nonce cacheable, among other things.

== Privacy ==

This plugin includes a Privacy blurb that you can add to your site's Privacy Policy via the WordPress Privacy settings.

**For your own information:** LiteSpeed Cache for WordPress potentially stores a duplicate copy of every web page on display on your site. The pages are stored locally on the system where LiteSpeed server software is installed and are not transferred to or accessed by LiteSpeed employees in any way, except as necessary in providing routine technical support if you request it. All cache files are temporary, and may easily be purged before their natural expiration, if necessary, via a Purge All command. It is up to individual site administrators to come up with their own cache expiration rules.

In addition to caching, our WordPress plugin has an Image Optimization feature. When optimization is requested, images are transmitted to a remote LiteSpeed server, processed, and then transmitted back for use on your site. LiteSpeed keeps copies of optimized images for 7 days (in case of network stability issues) and then permanently deletes them. Similarly, the WordPress plugin has a Reporting feature whereby a site owner can transmit an environment report to our server so that we may better provide technical support. Neither of these features collects any visitor data. Only server and site data is involved.

Please see [LiteSpeed’s Privacy Policy](https://www.litespeedtech.com/company/privacy-policy) for our complete Privacy/GDPR statement.

== Frequently Asked Questions ==

= Why do the cache features require LiteSpeed Server? =
This plugin communicates with your LiteSpeed Web Server and its built-in page cache (LSCache) to deliver superior performance to your WordPress site. The plugin's cache features indicate to the server that a page is cacheable and for how long, or they invalidate particular cached pages using tags.

LSCache is a server-level cache, so it's faster than PHP-level caches. [Compare with other PHP-based caches](https://www.litespeedtech.com/benchmarks/wordpress).

A page cache allows the server to bypass PHP and database queries altogether. LSCache, in particular, because of its close relationship with the server, can remember things about the cache entries that other plugins cannot, and it can analyze dependencies. It can utilize tags to manage the smart purging of the cache, and it can use vary cookies to serve multiple versions of cached content based on things like mobile vs. desktop, geographic location, and currencies. [See our Caching 101 blog series](https://blog.litespeedtech.com/tag/caching-101/).

If all of that sounds complicated, no need to worry. LSCWP works right out of the box with default settings that are appropriate for most sites. [See the Beginner's Guide](https://www.litespeedtech.com/support/wiki/doku.php/litespeed_wiki:cache:lscwp:beginner).

**Don't have a LiteSpeed server?** We're beta testing our new QUIC.cloud CDN service, and it allows those on *any server* (nginx and Apache included) to experience the power of LiteSpeed caching! [Click here](https://quic.cloud) to learn more or to give QUIC.cloud a try!

= What about the optimization features of LSCache? =

LSCWP includes additional optimization features, such as Database Optimization, Minification and Combination of CSS and JS files, HTTP/2 Push, CDN Support, Browser Cache, Object Cache, Lazy Load for Images, and Image Optimization! And now, many of these features do not require the use of a LiteSpeed web server.

= Is the LiteSpeed Cache Plugin for WordPress free? =

Yes, LSCWP will always be free and open source. That said, a LiteSpeed server is required for the **LiteSpeed Exclusive Features** (see the list above), and there are fees associated with some LiteSpeed server editions (see question 2).

= What server software is required for this plugin? =

A LiteSpeed web server is required in order to use the LiteSpeed Exclusive Features of this plugin. See **Plugin Features** above for details.

* LiteSpeed Web Server Enterprise with LSCache Module (v5.0.10+)
* OpenLiteSpeed (v1.4.17+) - Free and open source!
* LiteSpeed WebADC (v2.0+)

Any single server or cluster including a LiteSpeed server will work.

The General Features may be used with any web server. LiteSpeed is not required.

= Does this plugin work in a clustered environment? =

The cache entries are stored at the LiteSpeed server level. The simplest solution is to use LiteSpeed WebADC, as the cache entries will be stored at that level.

If using another load balancer, the cache entries will only be stored at the backend nodes, not at the load balancer.

The purges will also not be synchronized across the nodes, so this is not recommended.

If a customized solution is required, please contact LiteSpeed Technologies at `info@litespeedtech.com`

NOTICE: The rewrite rules created by this plugin must be copied to the Load Balancer.

= Where are the cached files stored? =

The actual cached pages are stored and managed by LiteSpeed Servers.

Nothing is stored within the WordPress file structure.

= Does LiteSpeed Cache for WordPress work with OpenLiteSpeed? =

Yes it can work well with OpenLiteSpeed, although some features may not be supported. See **Plugin Features** above for details. Any setting changes that require modifying the `.htaccess` file will require a server restart.

= Is WooCommerce supported? =

In short, yes. However, for some WooCommerce themes, the cart may not be updated correctly. Please [visit our blog](https://blog.litespeedtech.com/2017/05/31/wpw-fixing-lscachewoocommerce-conflicts/) for a quick tutorial on how to detect this problem and fix it if necessary.

= My plugin has some pages that are not cacheable. How do I instruct the LiteSpeed Cache Plugin to not cache the page? =

As of version 1.0.10, you may simply add `define('LSCACHE_NO_CACHE', true);` sometime before the shutdown hook, and it should be recognized by the cache.

Alternatively, you may use the function xxx`LiteSpeed_Cache_Tags::set_noncacheable();` for earlier versions (1.0.7+).

If using the function, make sure to check that the class exists prior to using the function.

Please see [our API wiki](https://www.litespeedtech.com/support/wiki/doku.php/litespeed_wiki:cache:lscwp:api) for more information and to learn what else you can do to integrate your plugin with LSCWP.

= Are my images optimized? =

Not automatically. LSCWP v1.6+ can optimize your images by request. Navigate to **LiteSpeed Cache > Image Optimization**.

= How do I make a WP nonce cacheable in my third-party plugin? =

Our API includes a function that uses ESI to "punch a hole" in a cached page for a nonce. This allows the nonce to be cached for 12 hours, regardless of the TTL of the page it is on.

Quick start: replace `wp_create_nonce( 'example' )` with `method_exists( 'LiteSpeed_Cache_API', 'nonce' ) ? LiteSpeed_Cache_API::nonce( 'example' ) : wp_create_nonce( 'example' )`.

Learn more on [our API wiki](https://www.litespeedtech.com/support/wiki/doku.php/litespeed_wiki:cache:lscwp:api#nonce_issues).

= How do I get WP-PostViews to display an updating view count? =

1. Use: `<div id="postviews_lscwp"></div>`
    to replace
    `<?php if(function_exists('the_views')) { the_views(); } ?>`
    * NOTE: The id can be changed, but the div id and the ajax function must match.
1. Replace the ajax query in `wp-content/plugins/wp-postviews/postviews-cache.js` with
    `
    jQuery.ajax({
        type:"GET",
        url:viewsCacheL10n.admin_ajax_url,
        data:"postviews_id="+viewsCacheL10n.post_id+"&action=postviews",
        cache:!1,
        success:function(data) {
            if(data) {
                jQuery('#postviews_lscwp').html(data+' views');
            }
       }
    });
    `
1. Purge the cache to use the updated pages.

= How do I enable the crawler? =

The crawler is disabled by default, and must be enabled by the server admin first.

Once the crawler is enabled on the server side, navigate to **LiteSpeed Cache > Crawler** and set *Activation* to `Enable`.

For more detailed information about crawler setup, please see [our wiki](https://www.litespeedtech.com/support/wiki/doku.php/litespeed_wiki:cache:lscwp:configuration:enabling_the_crawler).

= Why are some settings tabs missing? =

LSCWP has a "Basic View" and an "Advanced View." While in Basic View, you will see the following settings tabs: **General**, **Cache**, **Purge**, **Excludes**, and (optionally) **WooCommerce**. These are all that is necessary to manage the LiteSpeed Cache.

Click on the `Advanced View` link at the top of the page, and several more tabs will be revealed: **Optimize**, **Tuning**, **Media**, **CDN**, **ESI**, **Advanced**, **Debug**, **Crawler**. These tabs contain more expert-level cache options as well as non-cache-related optimization functionality.

= What are the known compatible plugins and themes? =

* [WPML](https://wpml.org/)
* [bbPress](https://wordpress.org/plugins/bbpress/)
* [WooCommerce](https://wordpress.org/plugins/woocommerce/)
* [Contact Form 7](https://wordpress.org/plugins/contact-form-7/)
* [Google XML Sitemaps](https://wordpress.org/plugins/google-sitemap-generator/)
* [Yoast SEO](https://wordpress.org/plugins/wordpress-seo/)
* [Wordfence Security](https://wordpress.org/plugins/wordfence/)
* [NextGen Gallery](https://wordpress.org/plugins/nextgen-gallery/)
* [ShortPixel](https://shortpixel.com/h/af/CXNO4OI28044/)
* Aelia CurrencySwitcher
* [Fast Velocity Minify](https://wordpress.org/plugins/fast-velocity-minify/) - Thanks Raul Peixoto!
* Autoptimize
* [Better WP Minify](https://wordpress.org/plugins/bwp-minify/)
* [WP Touch](https://wordpress.org/plugins/wptouch/)
* [Theme My Login](https://wordpress.org/plugins/theme-my-login/)
* [WPLister](https://www.wplab.com/plugins/wp-lister/)
* [WP-PostRatings](https://wordpress.org/plugins/wp-postratings/)
* [Avada 5.1 RC1+](https://avada.theme-fusion.com/)
* [Elegant Themes Divi 3.0.67+](https://www.elegantthemes.com/gallery/divi/)
* [Elegant Divi Builder] (https://www.elegantthemes.com/plugins/divi-builder/)
* [Caldera Forms](https://wordpress.org/plugins/caldera-forms/) 1.5.6.2+
* Login With Ajax
* [Ninja Forms](https://wordpress.org/plugins/ninja-forms/)
* [Post Types Order 1.9.3.6+](https://wordpress.org/plugins/post-types-order/)
* [BoomBox — Viral Magazine WordPress Theme](https://themeforest.net/item/boombox-viral-buzz-wordpress-theme/16596434?ref=PX-lab)
* Beaver Builder
* FacetWP (LSWS 5.3.6+)
* WpDiscuz
* WP-Stateless
* Elementor


== Changelog ==

= 2.9.8.2 - Jun 17 2019 =
* 🔥🐞 <strong>3rd</strong>: Fixed PHP 5.3 compatibility issue with Facetwp.

= 2.9.8.1 - Jun 17 2019 =
* <strong>3rd</strong>: Set ESI template hook priority to highest number to prevent ESI conflict with Enfold theme. (#289354)
* <strong>3rd</strong>: Improved Facetwp reset button compatibility with ESI. (@emilyel)
* <strong>3rd</strong>: Enabled user role change to fix duplicate login issue for plugins that use alternative login processes. (#114165 #717223 @sergiom87)
* <strong>GUI</strong>: Wrapped static text with translate function. (@halilemreozen)

= 2.9.8 - May 22 2019 =
* <strong>Core</strong>: Refactored loading priority so user related functions & optimization features are set after user initialization. (#717223 #114165 #413338)
* <strong>Media</strong>: Improved backup file calculation query to prevent out-of-memory issue.
* <strong>Conf</strong>: Feed cache now defaults to ON.
* <strong>API</strong>: Fully remote attachment compatibility API of image optimization now supported.
* 🕷️: Bypassed vary change for crawler; crawler can now simulate default vary cookie.
* <strong>ESI</strong>: Refactored ESI widget. Removed `widget_load_get_options()` function.
* <strong>ESI</strong>: Changed the input name of widget fields in form.
* <strong>3rd</strong>: Elementor can now save ESI widget settings in frontend builder.
* <strong>3rd</strong>: WP-Stateless compatibility.
* <strong>IAPI</strong>: Image optimization can now successfully finish the destroy process with large volume images with automatic continual mode.
* 🐞<strong>CDN</strong>: Fixed issue with Load JQuery Remotely setting where WP 5.2.1 provided an unexpected jQuery version.
* 🐞<strong>3rd</strong>: Login process now gets the correct role; fixed double login issue.

= 2.9.7.2 - May 2 2019 =
* <strong>Conf</strong>: Enhanced compatibility when an option is not properly initialized.
* <strong>Conf</strong>: Prevent non-array instance in widget from causing 500 error. (#210407)
* <strong>CCSS</strong>: Increase CCSS generation timeout to 60s.
* <strong>Media</strong>: Renamed lazyload CSS class to avoid conflicts with other plugins. (@DynamoProd)
* <strong>JS</strong>: Improved W3 validator. (@istanbulantik)
* <strong>QUIC</strong>: Synced cache tag prefix for static files cache.
* <strong>ESI</strong>: Restored query strings to ESI admin bar for accurate rendering. (#977284)
* <strong>ESI</strong>: Tweaked ESI init priority to honor LITESPEED_DISABLE_ALL const. ESI will now init after plugin loaded.
* 🐞<strong>ESI</strong>: No longer initialize ESI if ESI option is OFF.
* <strong>API</strong>: New "Disable All" API function.
* <strong>API</strong>: New "Force public cache" API function.
* 🐞<strong>Vary</strong>: Fixed an issue with saving vary groups.
* 🐞<strong>IAPI</strong>: Fixed an issue where image md5 validation failed due to whitespace in the image path.
* 🐞<strong>3rd</strong>: Bypass all optimization/ESI/Cache features when entering Divi Theme Builder frontend editor.
* 🐞<strong>3rd</strong>: Fixed an issue where DIVI admin bar exit button didn't work when ESI was ON.

= 2.9.7.1 - Apr 9 2019 =
* <strong>Purge</script>: Purge All no longer includes Purge CCSS/Placeholder.
* <strong>3rd</strong>: Divi Theme Builder no longer experiences nonce expiration issues in the contact form widget. (#475461)

= 2.9.7 - Apr 1 2019 =
* 🌱🌱🌱 QUIC.cloud CDN feature. Now Apache/Nginx can use LiteSpeed cache freely.

= 2.9.6 - Mar 27 2019 =
* 🌱<strong>IAPI</strong>: Appended XMP to `Preserve EXIF data` setting. WebP will now honor this setting. (#902219)
* <strong>Object</script>: Fixed SASL connection with LSMCD.
* <strong>ESI</strong>: Converted ESI URI parameters to JSON; Added ESI validation.
* <strong>Import</strong>: Import/Export will now use JSON format. <strong>Please re-export any backed up settings. Previous backup format is no longer recognized.</strong>
* <strong>Media</strong>: WebP replacement will honor `Role Excludes` setting now. (@mfazio26)
* <strong>Data</strong>: Forbid direct visit to const.default.ini.
* <strong>Utility</strong>: Can handle WHM passed in `LITESPEED_ERR` constant now.
* <strong>IAPI</strong>: Communicate via JSON encoding.
* <strong>IAPI</strong>: IAPI v2.9.6.

= 2.9.5 - Mar 14 2019 =
* 🌱 Auto convert default WordPress nonce to ESI to avoid expiration.
* 🌱 <strong>API</strong>: Ability to easily convert custom nonce to ESI by registering `LiteSpeed_Cache_API::nonce_action`.
* <strong>OPTM</strong>: Tweaked redundant attr `data-no-optimize` in func `_analyse_links` to `data-ignore-optimize` to offer the API to bypass optimization but still move src to top of source code.
* <strong>API</strong>: Renamed default nonce ESI ID from `lscwp_nonce_esi` to `nonce`.
* <strong>API</strong>: Added WebP generation & validation hook API. (@alim #wp-stateless)
* <strong>API</strong>: Added hook to bypass vary commenter check. (#wpdiscuz)
* <strong>Doc</strong>: Clarified Cache Mobile description. (@JohnnyNguyen)
* <strong>Doc</strong>: Replaced incorrect link in description. (@JohnnyNguyen)
* <strong>3rd</strong>: Improved wpDiscuz compatibility.
* 🐞<strong>3rd</strong>: Fixed Divi Theme Builder comment compatibility on non-builder pages. (#410919)
* <strong>3rd</strong>: Added YITH ESI adjustment.

= 2.9.4.1 - Feb 28 2019 =
* 🔥🐞<strong>Tag</strong>: Fixed issue where unnecessary warning potentially displayed after upgrade process when object cache is enabled.

= 2.9.4 - Feb 27 2019 =
* 🐞<strong>REST</strong>: New REST class with better WP5 Gutenberg and internal REST call support when ESI is embedded.
* <strong>ESI</strong>: ESI block ID is now in plain text in ESI URL parameters.
* 🐞<strong>ESI</strong>: Fixed a redundant ESI 301 redirect when comma is in ESI URL.
* <strong>ESI</strong>: REST call can now parse shortcodes in ESI.
* <strong>API</strong>: Changed ESI `parse_esi_param()` function to private and `load_esi_block` function to non-static.
* <strong>API</strong>: Added `litespeed_is_json` hook for buffer JSON conversion.
* <strong>GUI</strong>: Prepended plugin name to new version notification banner.
* <strong>3rd</strong>: WPML multi domains can now be handled in optimization without CDN tricks.

= 2.9.3 - Feb 20 2019 =
* <strong>ESI</strong>: ESI shortcodes can now be saved in Gutenberg editor.
* <strong>ESI</strong>: ESI now honors the parent page JSON data type to avoid breaking REST calls (LSWS 5.3.6+).
* <strong>ESI</strong>: Added is_json parameter support for admin_bar.
* <strong>ESI</strong>: Simplified comment form code.
* <strong>3rd</strong>: Better page builder plugin compatibility within AJAX calls.
* <strong>3rd</strong>: Compatibility with FacetWP (LSWS 5.3.6+).
* <strong>3rd</strong>: Compatibility with Beaver Builder.
* <strong>Debug</strong>: Added ESI buffer content to log.
* <strong>Tag</strong>: Only append blog ID to cache tags when site is part of a network.
* <strong>IAPI</strong>: Optimized database query for pulling images.
* <strong>GUI</strong>: Added more plugin version checking for better feature compatibility.
* <strong>GUI</strong>: Ability to bypass non-critical banners with the file .litespeed_no_banner.
* <strong>Media</strong>: Background image WebP replacement now supports quotes around src.

= 2.9.2 - Feb 5 2019 =
* <strong>API</strong>: Add a hook `litespeed_esi_shortcode-*` for ESI shortcodes.
* <strong>3rd</strong>: WooCommerce can purge products now when variation stock is changed.
* 🐞🕷️: Forced HTTP1.1 for crawler due to a CURL HTTP2 bug.

= 2.9.1 - Jan 25 2019 =
* <strong>Compatibility</strong>: Fixed fatal error for PHP 5.3.
* <strong>Compatibility</strong>: Fixed PHP warning in htmlspecialchars when building URLs. (@souljahn2)
* <strong>Media</strong>: Excluded invalid image src from lazyload. (@andrew55)
* <strong>Optm</strong>: Improved URL compatibility when detecting closest cloud server.
* <strong>ESI</strong>: Supported JSON format comment format in ESI with `is_json` parameter.
* <strong>API</strong>: Added filters to CCSS/CSS/JS content. (@lhoucine)
* <strong>3rd</strong>: Improved comment compatibility with Elegant Divi Builder.
* <strong>IAPI</strong>: New Europe Image Optimization server (EU5). <strong>Please whitelist the new [IAPI IP List](https://wp.api.litespeedtech.com/ips).</strong>
* <strong>GUI</strong>: No longer show banners when `Disable All` in `Debug` is ON. (@rabbitwordpress)
* <strong>GUI</strong>: Fixed button style for RTL languages.
* <strong>GUI</strong>: Removed unnecessary translation in report.
* <strong>GUI</strong>: Updated readme wiki links.
* <strong>GUI</strong>: Fixed pie styles in image optimization page.

= 2.9 - Dec 31 2018 =
* 🌱<strong>Media</strong>: Lazy Load Image Classname Excludes. (@thinkmedia)
* 🌱: New EU/AS cloud servers for faster image optimization handling.
* 🌱: New EU/AS cloud servers for faster CCSS generation.
* 🌱: New EU/AS cloud servers for faster responsive placeholder generation.
* 🌱<strong>Conf</strong>: Ability to set single options via link.
* 🌱<strong>Cache</strong>: Ability to add custom TTLs to Force Cache URIs.
* <strong>Purge</strong>: Added post type to Purge tags.
* <strong>Purge</strong>: Redefined CCSS page types.
* <strong>Core</strong>: Using Exception for .htaccess R/W.
* <strong>IAPI</strong>: <strong>New cloud servers added. Please whitelist the new [IAPI IP List](https://wp.api.litespeedtech.com/ips).</strong>
* <strong>Optm</strong>: Trim BOM when detecting if the page is HTML.
* <strong>GUI</strong>: Added PageSpeed Score comparison into promotion banner.
* <strong>GUI</strong>: Refactored promotion banner logic.
* <strong>GUI</strong>: Removed page optimized comment when ESI Silence is requested.
* <strong>GUI</strong>: WHM transient changed to option instead of transient when storing.
* <strong>GUI</strong>: Appending more descriptions to CDN filetype setting.
* <strong>IAPI</strong>: Removed duplicate messages.
* <strong>IAPI</strong>: Removed taken_failed/client_pull(duplicated) status.
* <strong>Debug</strong>: Environment report no longer generates hash for validation.
* <strong>3rd</strong>: Non-cacheable pages no longer punch ESI holes for Divi compatibility.
* 🐞<strong>Network</strong>: Added slashes for mobile rules when activating plugin.
* 🐞<strong>CCSS</strong>: Eliminated a PHP notice when appending CCSS.

= 2.8.1 - Dec 5 2018 =
* 🐞🕷️: Fixed an activation warning related to cookie crawler. (@kacper3355 @rastel72)
* 🐞<strong>Media</strong>: Replace safely by checking if pulled images is empty or not first. (@Monarobase)
* <strong>3rd</strong>: Shortcode ESI compatibility with Elementor.

= 2.8 - Nov 30 2018 =
* 🌱: ESI shortcodes.
* 🌱: Mobile crawler.
* 🌱: Cookie crawler.
* <strong>API</strong>: Can now add `_litespeed_rm_qs=0` to bypass Remove Query Strings.
* <strong>Optm</strong>: Removed error log when minify JS failed.
* 🐞<strong>Core</strong>: Fixed a bug that caused network activation PHP warning.
* <strong>Media</strong>: Removed canvas checking for WebP to support TOR. (@odeskumair)
* <strong>Media</strong>: Eliminated potential image placeholder PHP warning.
* <strong>3rd</strong>: Bypassed Google recaptcha from Remove Query Strings for better compatibility.
* <strong>IAPI</strong>: Showed destroy timeout details.
* <strong>Debug</strong>: Moved Google Fonts log to advanced level.
* <strong>GUI</strong>: Replaced all Learn More links for functions.
* <strong>GUI</strong>: Cosmetic updates including Emoji.
* 🕷️: Removed duplicated data in sitemap and blacklist.

= 2.7.3 - Nov 26 2018 =
* <strong>Optm</strong>: Improved page render speed with Web Font Loader JS library for Load Google Fonts Asynchronously.
* <strong>Optm</strong>: Directly used JS library files in plugin folder instead of short links `/min/`.
* <strong>Optm</strong>: Handled exceptions in JS optimization when meeting badly formatted JS.
* <strong>3rd</strong>: Added Adobe Lightroom support for NextGen Gallery.
* <strong>3rd</strong>: Improved Postman app support for POST JSON requests.
* <strong>IAPI</strong>: <strong>US3 server IP changed to 68.183.60.185</strong>.

= 2.7.2 - Nov 19 2018 =
* 🌱: Auto Upgrade feature.
* <strong>CDN</strong>: Bypass CDN for cron to avoid WP jQuery deregister warning.

= 2.7.1 - Nov 15 2018 =
* 🌱<strong>CLI</strong>: Ability to set CDN mapping by `set_option litespeed-cache-cdn_mapping[url][0] https://url`.
* 🌱<strong>CDN</strong>: Ability to customize default CDN mapping data in default.ini.
* 🌱<strong>API</strong>: Default.ini now supports both text-area items and on/off options.
* <strong>Vary</strong>: Refactored Vary and related API.
* <strong>Vary</strong>: New hook to manipulate vary cookies value.
* <strong>Core</strong>: Activation now can generate Object Cache file.
* <strong>Core</strong>: Unified Object Cache/rewrite rules generation process across activation/import/reset/CLI.
* <strong>Core</strong>: Always hook activation to make activation available through the front end.
* 🐞<strong>IAPI</strong>: Fixed a bug where environment report gave incorrect image optimization data.
* 🐞<strong>OLS</strong>: Fixed a bug where login cookie kept showing a warning on OpenLiteSpeed.
* 🐞<strong>Core</strong>: Fixed a bug where Import/Activation/CLI was missing CDN mapping settings.
* <strong>API</strong>: <strong>Filters `litespeed_cache_media_lazy_img_excludes/litespeed_optm_js_defer_exc` passed-in parameter is changed from string to array.</strong>

= 2.7 - Nov 2 2018 =
* 🌱: Separate Purge log for better debugging.
* <strong>3rd</strong>: Now fully compatible with WPML.
* <strong>IAPI</strong>: Sped up Image Optimization workflow.
* <strong>GUI</strong>: Current IP now shows in Debug settings.
* <strong>GUI</strong>: Space separated placeholder queue list for better look.
* <strong>IAPI</strong>: <strong>EU3 server IP changed to 165.227.131.98</strong>.

= 2.6.4.1 - Oct 25 2018 =
* 🔥🐞<strong>Media</strong>: Fixed a bug where the wrong table was used in the Image Optimization process.
* <strong>IAPI</strong>: IAPI v2.6.4.1.

= 2.6.4 - Oct 24 2018 =
* 🌱: Ability to create custom default config options per hosting company.
* 🌱: Ability to generate mobile Critical CSS.
* 🐞<strong>Media</strong>: Fixed a bug where Network sites could incorrectly override optimized images.
* 🐞<strong>CDN</strong>: Fixed a bug where image URLs containing backslashes were matched.
* <strong>Cache</strong>: Added default Mobile UA config setting.
* <strong>GUI</strong>: Fixed unknown shortcut characters for non-English languages Setting tabs.

= 2.6.3 - Oct 18 2018 =
* 🌱: Ability to Reset All Options.
* 🌱<strong>CLI</strong>: Added new `lscache-admin reset_options` command.
* <strong>GUI</strong>: Added shortcuts for more of the Settings tabs.
* <strong>Media</strong>: Updated Lazy Load JS library to the most recent version.
* There is no longer any need to explicitly Save Settings upon Import.
* Remove Query String now will remove *all* query strings in JS/CSS static files.
* <strong>IAPI</strong>: Added summary info to debug log.

= 2.6.2 - Oct 11 2018 =
* <strong>Setting</strong>: Automatically correct invalid numeric values in configuration settings upon submit.
* 🐞<strong>Media</strong>: Fixed the issue where iframe lazy load was broken by latest Chrome release. (@ofmarconi)
* 🐞: Fixed an issue with Multisite where subsites failed to purge when only primary site has WooCommerce . (@kierancalv)

= 2.6.1 - Oct 4 2018 =
* 🌱: Ability to generate separate Critical CSS Cache for Post Types & URIs.
* <strong>API</strong>: Filter `litespeed_frontend_htaccess` for frontend htaccess path.
* <strong>Media</strong>: Removed responsive placeholder generation history to save space.

= 2.6.0.1 - Sep 24 2018 =
* 🔥🐞: Fixed an issue in responsive placeholder generation where redundant history data was being saved and using a lot of space.

= 2.6 - Sep 22 2018 =
* <strong>Vary</strong>: Moved `litespeed_cache_api_vary` hook outside of OLS condition for .htaccess generation.
* <strong>CDN</strong>: Trim spaces in original URL of CDN setting.
* <strong>API</strong>: New filter `litespeed_option_` to change all options dynamically.
* <strong>API</strong>: New `LiteSpeed_Cache_API::force_option()` to change all options dynamically.
* <strong>API</strong>: New `LiteSpeed_Cache_API::vary()` to set default vary directly for easier compaitiblity with WPML WooCommerce Multilingual.
* <strong>API</strong>: New `LiteSpeed_Cache_API::nonce()` to safely and easily allow caching of wp-nonce.
* <strong>API</strong>: New `LiteSpeed_Cache_API::hook_vary_add()` to add new vary.
* <strong>Optm</strong>: Changed HTML/JS/CSS optimization options assignment position from constructor to `finalize()`.
* <strong>Doc</strong>: Added nonce to FAQ and mentioned nonce in 3rd Party Compatibility section.
* <strong>GUI</strong>: Moved inline minify to under html minify due to the dependency.
* <strong>3rd</strong>: Cached Aelia CurrencySwitcher by default.
* 🐞: Fixed issue where enabling remote JQuery caused missing jquery-migrate library error.

= 2.5.1 - Sep 11 2018 =
* 🌱 Responsive placeholder. (@szmigieldesign)
* Changed CSS::ccss_realpath function scope to private.
* 🐞 Detected JS filetype before optimizing to avoid PHP source conflict. (@closte #50)

= 2.5 - Sep 6 2018 =
* [IMPROVEMENT] <strong>CLI</strong> can now execute Remove Original Image Backups. (@Shon)
* [UPDATE] Fixed issue where WP-PostViews documentation contained extra slashes. (#545638)
* [UPDATE] Check LITESPEED_SERVER_TYPE for more accurate LSCache Disabled messaging.
* [IAPI] Fixed a bug where optimize/fetch error notification was not being received. (@LucasRolff)

= 2.4.4 - Aug 31 2018 =
* [NEW] <strong>CLI</strong> can now support image optimization. (@Shon)
* [IMPROVEMENT] <strong>GUI</strong> Cron/CLI will not create admin message anymore.
* [UPDATE] <strong>Media</strong> Fixed a PHP notice that appeared when pulling optimized images.
* [UPDATE] Fixed a PHP notice when detecting origin of ajax call. (@iosoft)
* [DEBUG] Debug log can now log referer URL.
* [DEBUG] Changes to options will now be logged.

= 2.4.3 - Aug 27 2018 =
* [NEW] <strong>Media</strong> Ability to inline image lazyload JS library. (@Music47ell)
* [IMPROVEMENT] <strong>Media</strong> Deleting images will now clear related optimization file & info too.
* [IMPROVEMENT] <strong>Media</strong> Non-image postfix data will now be bypassed before sending image optimization request.
* [BUGFIX] <strong>CDN</strong> CDN URL will no longer be replaced during admin ajax call. (@pankaj)
* [BUGFIX] <strong>CLI</strong> WPCLI can now save options without incorrectly clearing textarea items. (@Shon)
* [GUI] Moved Settings above Manage on the main menu.

= 2.4.2 - Aug 21 2018 =
* [IMPROVEMENT] <strong>Media</strong> Sped up Image Optimization process by replacing IAPI server pull communication.
* [IMPROVEMENT] <strong>Media</strong> Ability to delete optimized WebP/original image by item in Media Library. (@redgoodapple)
* [IMPROVEMENT] <strong>CSS Optimize</strong> Generate new optimized CSS name based on purge timestamp. Allows CSS cache to be cleared for visitors. (@bradbrownmagic)
* [IMPROVEMENT] <strong>API</strong> added litespeed_img_optm_options_per_image. (@gintsg)
* [UPDATE] Stopped showing "No Image Found" message when all images have finished optimization. (@knutsp)
* [UPDATE] Improved a PHP warning when saving settings. (@sergialarconrecio)
* [UPDATE] Changed backend adminbar icon default behavior from Purge All to Purge LSCache.
* [UPDATE] Clearing CCSS cache will clear unfinished queue too.
* [UPDATE] Added "$" exact match when adding URL by frontend adminbar dropdown menu, to avoid affecting any sub-URLs.
* [UPDATE] Fixed IAPI error message showing array bug. (@thiomas)
* [UPDATE] Debug Disable All will do a Purge All.
* [UPDATE] <strong>Critical CSS server IP changed to 142.93.3.57</strong>.
* [GUI] Showed plugin update link for IAPI version message.
* [GUI] Bypassed null IAPI response message.
* [GUI] Grouped related settings with indent.
* [IAPI] Added 503 handler for IAPI response.
* [IAPI] IAPI v2.4.2.
* [IAPI] <strong>Center Server IP Changed from 34.198.229.186 to 142.93.112.87</strong>.

= 2.4.1 - Jul 19 2018 =
* [NEW FEATURE] <strong>Media</strong> Auto Level Up. Auto refill credit.
* [NEW FEATURE] <strong>Media</strong> Auto delete original backups after pulled. (@borisov87 @JMCA2)
* [NEW FEATURE] <strong>Media</strong> Auto request image optimization. (@ericsondr)
* [IMPROVEMENT] <strong>Media</strong> Fetch 404 error will notify client as other errors.
* [IMPROVEMENT] <strong>Media</strong> Support WebP for PageSpeed Insights. (@LucasRolff)
* [BUGFIX] <strong>CLI</strong> Fixed the issue where CLI import/export caused certain textarea settings to be lost. (#767519)
* [BUGFIX] <strong>CSS Optimize</strong> Fixed the issue that duplicated optimized CSS and caused rapid expansion of CSS cache folder.
* [GUI] <strong>Media</strong> Refactored operation workflow and interface.
* [UPDATE] <strong>Media</strong> Set timeout seconds to avoid pulling timeout. (@Jose)
* [UPDATE] <strong>CDN</strong>Fixed the notice when no path is in URL. (@sabitkamera)
* [UPDATE] <strong>Media</strong> Auto correct credits when pulling.
* [UPDATE] <strong>GUI</strong> Removed redundant double quote in gui.cls. (@DaveyJake)
* [IAPI] IAPI v2.4.1.
* [IAPI] Allow new error status notification and success message from IAPI.

= 2.4 - Jul 2 2018 =
* [NEW FEATURE] <strong>Media</strong> Added lossless optimization.
* [NEW FEATURE] <strong>Media</strong> Added Request Orignal Images ON/OFF.
* [NEW FEATURE] <strong>Media</strong> Added Request WebP ON/OFF. (@JMCA2)
* [IMPROVEMENT] <strong>Media</strong> Improved optimization tools to archive maximum compression and score.
* [IMPROVEMENT] <strong>Media</strong> Improved speed of image pull.
* [IMPROVEMENT] <strong>Media</strong> Automatically recover credit after pulled.
* [REFACTOR] <strong>Config</strong> Separated configure const class.
* [BUGFIX] <strong>Report</strong> Report can be sent successfully with emoji now. (@music47ell)
* [IAPI] New Europe Image Optimization server (EU3/EU4).
* [IAPI] New America Image Optimization server (US3/US4/US5/US6).
* [IAPI] New Asian Image Optimization server (AS3).
* [IAPI] Refactored optimization process.
* [IAPI] Increased credit limit.
* [IAPI] Removed request interval limit.
* [IAPI] IAPI v2.4.
* <strong>We strongly recommended that you re-optimize your image library to get a better compression result</strong>.

= 2.3.1 - Jun 18 2018 =
* [IMPROVEMENT] New setting to disable Generate Critical CSS. (@cybmeta)
* [IMPROVEMENT] Added filter to can_cdn/can_optm check. (@Jacob)
* [UPDATE] *Critical CSS* Added 404 css. Limit cron interval.
* [UPDATE] AJAX will not bypass CDN anymore by default. (@Jacob)
* [GUI] Show Disable All Features warning if it is on in Debug tab.

= 2.3 - Jun 13 2018 =
* [NEW FEATURE] Automatically generate critical CSS. (@joeee @ivan_ivanov @3dseo)
* [BUGFIX] "Mark this page as..." from dropdown menu will not reset settings anymore. (@cbratschi)

= 2.2.7 - Jun 4 2018 =
* [IMPROVEMENT] Improved redirection for manual image pull to avoid too many redirections warning.
* [IAPI] Increased credit limit.
* [BUGFIX] Fixed 503 error when enabling log filters in Debug tab. (#525206)
* [UPDATE] Improve compatibility when using sitemap url on servers with allow_url_open off.
* [UPDATE] Removed Crawler HTTP2 option due to causing no-cache blacklist issue for certain environments.
* [UPDATE] Privacy policy can be now translated. (@Josemi)
* [UPDATE] IAPI Increased default img request max to 3000.

= 2.2.6 - May 24 2018 =
* [NEW FEATURE] Original image backups can be removed now. (@borisov87 @JMCA2)
* [BUGFIX] Role Excludes in Tuning tab can save now. (@pako69)
* [UPDATE] Added privacy policy support.

= 2.2.5 - May 14 2018 =
* [IAPI] <strong>Image Optimization</strong> New Asian Image Optimization server (AS2).
* [INTEGRATION] Removed wpForo 3rd party file. (@massimod)

= 2.2.4 - May 7 2018 =
* [IMPROVEMENT] Improved compatibility with themes using the same js_min library. (#129093 @Darren)
* [BUGFIX] Fixed a bug when checking image path for dynamic files. (@miladk)
* [INTEGRATION] Compatibility with Universal Star Rating. (@miladk)

= 2.2.3 - Apr 27 2018 =
* [NEW FEATURE] WebP For Extra srcset setting in Media tab. (@vengen)
* [REFACTOR] Removed redundant LS consts.
* [REFACTOR] Refactored adv_cache generation flow.
* [BUGFIX] Fixed issue where inline JS minify exception caused a blank page. (@oomskaap @kenb1978)
* [UPDATE] Changed HTTP/2 Crawl default value to OFF.
* [UPDATE] Added img.data-src to default WebP replacement value for WooCommerce WebP support.
* [UPDATE] Detached crawler from LSCache LITESPEED_ON status.
* [API] Improved ESI API to honor the cache control in ESI wrapper.
* [API] Added LITESPEED_PURGE_SILENT const to bypass the notification when purging
* [INTEGRATION] Fixed issue with nonce expiration when using ESI API. (#923505 @Dan)
* [INTEGRATION] Improved compatibility with Ninja Forms by bypassing non-javascript JS from inline JS minify.
* [INTEGRATION] Added a hook for plugins that change the CSS/JS path e.g. Hide My WordPress.

= 2.2.2 - Apr 16 2018 =
* [NEW FEATURE] WebP Attribute To Replace setting in Media tab. (@vengen)
* [IMPROVEMENT] Generate adv_cache file automatically when it is lost.
* [IMPROVEMENT] Improved compatibility with ajax login. (@veganostomy)
* [UPDATE] Added object cache lib check in case user downgrades LSCWP to non-object-cache versions.
* [UPDATE] Avoided infinite loop when users enter invalid hook values in Purge All Hooks settings.
* [UPDATE] Updated log format in media&cdn class.
* [UPDATE] Added more items to Report.

= 2.2.1 - Apr 10 2018 =
* [NEW FEATURE] Included Directories setting in CDN tab. (@Dave)
* [NEW FEATURE] Purge All Hooks setting in Advanced tab.
* [UPDATE] Added background-image WebP replacement support. (@vengen)
* [UPDATE] Show recommended values for textarea items in settings.
* [UPDATE] Moved CSS/JS optimizer log to Advanced level.
* [INTEGRATION] Added WebP support for Avada Fusion Sliders. (@vengen)

= 2.2.0.2 - Apr 3 2018 =
* [HOTFIX] <strong>Object Cache</strong> Fixed the PHP warning caused by previous improvement to Object Cache.

= 2.2.0.1 - Apr 3 2018 =
* [HOTFIX] Object parameter will no longer cause warnings to be logged for Purge and Cache classes. (@kelltech @khrifat)
* [UPDATE] Removed duplicated del_file func from Object Cache class.
* [BUGFIX] `CLI` no longer shows 400 error upon successful result.

= 2.2 - Apr 2 2018 =
* [NEW FEATURE] <strong>Debug</strong> Disable All Features setting in Debug tab. (@monarobase)
* [NEW FEATURE] <strong>Cache</strong> Force Cacheable URIs setting in Excludes tab.
* [NEW FEATURE] <strong>Purge</strong> Purge all LSCache and other caches in one link.
* [REFACTOR] <strong>Purge</strong> Refactored Purge class.
* [BUGFIX] Query strings in DoNotCacheURI setting now works.
* [BUGFIX] <strong>Cache</strong> Mobile cache compatibility with WebP vary. (@Shivam #987121)
* [UPDATE] <strong>Purge</strong> Moved purge_all to Purge class from core class.
* [API] Set cacheable/Set force cacheable. (@Jacob)

= 2.1.2 - Mar 28 2018 =
* [NEW FEATURE] <strong>Image Optimization</strong> Clean Up Unfinished Data feature.
* [IAPI] IAPI v2.1.2.
* [IMPROVEMENT] <strong>CSS/JS Minify</strong> Reduced loading time significantly by improving CSS/JS minify loading process. (@kokers)
* [IMPROVEMENT] <strong>CSS/JS Minify</strong> Cache empty JS Minify content. (@kokers)
* [IMPROVEMENT] <strong>Cache</strong> Cache 301 redirect when scheme/host are same.
* [BUGFIX] <strong>Media</strong> Lazy load now can support WebP. (@relle)
* [UPDATE] <strong>CSS/JS Optimize</strong> Serve static files for CSS async & lazy load JS library.
* [UPDATE] <strong>Report</strong> Appended Basic/Advanced View setting to Report.
* [UPDATE] <strong>CSS/JS Minify</strong> Removed zero-width space from CSS/JS content.
* [GUI] Added Purge CSS/JS Cache link in Admin.

= 2.1.1.1 - Mar 21 2018 =
* [BUGFIX] Fixed issue where activation failed to add rules to .htaccess.
* [BUGFIX] Fixed issue where 304 header was blank on feed page refresh.

= 2.1.1 - Mar 20 2018 =
* [NEW FEATURE] <strong>Browser Cache</strong> Unlocked for non-LiteSpeed users.
* [IMPROVEMENT] <strong>Image Optimization</strong> Fixed issue where images with bad postmeta value continued to show in not-yet-requested queue.

= 2.1 - Mar 15 2018 =
* [NEW FEATURE] <strong>Image Optimization</strong> Unlocked for non-LiteSpeed users.
* [NEW FEATURE] <strong>Object Cache</strong> Unlocked for non-LiteSpeed users.
* [NEW FEATURE] <strong>Crawler</strong> Unlocked for non-LiteSpeed users.
* [NEW FEATURE] <strong>Database Cleaner and Optimizer</strong> Unlocked for non-LiteSpeed users.
* [NEW FEATURE] <strong>Lazy Load Images</strong> Unlocked for non-LiteSpeed users.
* [NEW FEATURE] <strong>CSS/JS/HTML Minify/Combine Optimize</strong> Unlocked for non-LiteSpeed users.
* [IAPI] IAPI v2.0.
* [IAPI] Increased max rows prefetch when client has additional credit.
* [IMPROVEMENT] <strong>CDN</strong> Multiple domains may now be used.
* [IMPROVEMENT] <strong>Report</strong> Added WP environment constants for better debugging.
* [REFACTOR] Separated Cloudflare CDN class.
* [BUGFIX] <strong>Image Optimization</strong> Fixed issue where certain MySQL version failed to create img_optm table. (@philippwidmer)
* [BUGFIX] <strong>Image Optimization</strong> Fixed issue where callback validation failed when pulling and sending request simultaneously.
* [GUI] Added Slack community banner.
* [INTEGRATION] CDN compatibility with WPML multiple domains. (@egemensarica)

= 2.0 - Mar 7 2018 =
* [NEW FEATURE] <strong>Image Optimization</strong> Added level up guidance.
* [REFACTOR] <strong>Image Optimization</strong> Refactored Image Optimization class.
* [IAPI] <strong>Image Optimization</strong> New European Image Optimization server (EU2).
* [IMPROVEMENT] <strong>Image Optimization</strong> Manual pull action continues pulling until complete.
* [IMPROVEMENT] <strong>CDN</strong> Multiple CDNs can now be randomized for a single resource.
* [IMPROVEMENT] <strong>Image Optimization</strong> Improved compatibility of long src images.
* [IMPROVEMENT] <strong>Image Optimization</strong> Reduced runtime load.
* [IMPROVEMENT] <strong>Image Optimization</strong> Avoid potential loss/reset of notified images status when pulling.
* [IMPROVEMENT] <strong>Image Optimization</strong> Avoid duplicated optimization for multiple records in Media that have the same image source.
* [IMPROVEMENT] <strong>Image Optimization</strong> Fixed issue where phantom images continued to show in not-yet-requested queue.
* [BUGFIX] <strong>Core</strong> Improved compatibility when upgrading outside of WP Admin. (@jikatal @TylorB)
* [BUGFIX] <strong>Crawler</strong> Improved HTTP/2 compatibility to avoid erroneous blacklisting.
* [BUGFIX] <strong>Crawler</strong> Changing Delay setting will use server variable for min value validation if set.
* [UPDATE] <strong>Crawler</strong> Added HTTP/2 protocol switch in the Crawler settings.
* [UPDATE] Removed unnecessary translation strings.
* [GUI] Display translated role group name string instead of English values. (@Richard Hordern)
* [GUI] Added Join LiteSpeed Slack link.
* [GUI] <strong>Import / Export</strong> Cosmetic changes to Import Settings file field.
* [INTEGRATION] Improved compatibility with WPML Media for Image Optimization. (@szmigieldesign)

= 1.9.1.1 - February 20 2018 =
* [Hotfix] Removed empty crawler when no role simulation is set.

= 1.9.1 - February 20 2018 =
* [NEW FEATURE] Role Simulation crawler.
* [NEW FEATURE] WebP multiple crawler.
* [NEW FEATURE] HTTP/2 support for crawler.
* [BUGFIX] Fixed a js bug with the auto complete mobile user agents field when cache mobile is turned on.
* [BUGFIX] Fixed a constant undefined warning after activation.
* [GUI] Sitemap generation settings are no longer hidden when using a custom sitemap.

= 1.9 - February 12 2018 =
* [NEW FEATURE] Inline CSS/JS Minify.
* [IMPROVEMENT] Removed Composer vendor to thin the plugin folder.
* [UPDATE] Tweaked H2 to H1 in Admin headings for accessibility. (@steverep)
* [GUI] Added Mobile User Agents to basic view.
* [GUI] Moved Object Cache & Browser Cache from Cache tab to Advanced tab.
* [GUI] Moved LSCache Purge All from Adminbar to dropdown menu.

= 1.8.3 - February 2 2018 =
* [NEW FEATURE] Crawler server variable limitation support.
* [IMPROVEMENT] Added Store Transients option to fix transients missing issue when Cache Wp-Admin setting is OFF.
* [IMPROVEMENT] Tweaked ARIA support. (@steverep)
* [IMPROVEMENT] Used strpos instead of strncmp for performance. (@Zach E)
* [BUGFIX] Transient cache can now be removed when the Cache Wp-Admin setting is ON in Object Cache.
* [BUGFIX] Network sites can now save Advanced settings.
* [BUGFIX] Media list now shows in network sites.
* [BUGFIX] Show Crawler Status button is working again.
* [UPDATE] Fixed a couple of potential PHP notices in the Network cache tab and when no vary group is set.
* [GUI] Added Learn More link to all setting pages.

= 1.8.2 - January 29 2018 =
* [NEW FEATURE] Instant Click in the Advanced tab.
* [NEW FEATURE] Import/Export settings.
* [NEW FEATURE] Opcode Cache support.
* [NEW FEATURE] Basic/Advanced setting view.
* [IMPROVEMENT] Added ARIA support in widget settings.
* [BUGFIX] Multiple WordPress instances with same Object Cache address will no longer see shared data.
* [BUGFIX] WebP Replacement may now be set at the Network level.
* [BUGFIX] Object Cache file can now be removed at the Network level uninstall.

= 1.8.1 - January 22 2018 =
* [NEW FEATURE] Object Cache now supports Redis.
* [IMPROVEMENT] Memcached Object Cache now supports authorization.
* [IMPROVEMENT] A 500 error will no longer be encountered when turning on Object Cache without the proper PHP extension installed.
* [BUGFIX] Object Cache settings can now be saved at the Network level.
* [BUGFIX] Mu-plugin now supports Network setting.
* [BUGFIX] Fixed admin bar showing inaccurate Edit Page link.
* [UPDATE] Removed warning information when no Memcached server is available.

= 1.8 - January 17 2018 =
* [NEW FEATURE] Object Cache.
* [REFACTOR] Refactored Log class.
* [REFACTOR] Refactored LSCWP basic const initialization.
* [BUGFIX] Fixed Cloudflare domain search breaking when saving more than 50 domains under a single account.
* [UPDATE] Log filter settings are now their own item in the wp-option table.

= 1.7.2 - January 5 2018 =
* [NEW FEATURE] Cloudflare API support.
* [IMPROVEMENT] IAPI key can now be reset to avoid issues when domain is changed.
* [BUGFIX] Fixed JS optimizer breaking certain plugins JS.
* [UPDATE] Added cdn settings to environment report.
* [GUI] Added more shortcuts to backend adminbar.
* [INTEGRATION] WooCommerce visitors are now served from public cache when cart is empty.

= 1.7.1.1 - December 29 2017 =
* [BUGFIX] Fixed an extra trailing underscore issue when saving multiple lines with DNS Prefetch.
* [UPDATE] Cleaned up unused dependency vendor files.

= 1.7.1 - December 28 2017 =
* [NEW FEATURE] Added DNS Prefetch setting on the Optimize page.
* [NEW FEATURE] Added Combined File Max Size setting on the Tuning page.
* [IMPROVEMENT] Improved JS/CSS minify to achieve higher page scores.
* [IMPROVEMENT] Optimized JS/CSS files will not be served from private cache for OLS or with ESI off.
* [UPDATE] Fixed a potential warning for new installations on the Settings page.
* [UPDATE] Fixed an issue with guest users occasionally receiving PHP warnings.
* [BUGFIX] Fixed a bug with the Improve HTTPS Compatibility setting failing to save.
* Thanks to all of our users for your encouragement and support! Happy New Year!
* PS: Lookout 2018, we're back!

= 1.7 - December 22 2017 =
* [NEW FEATURE] Drop Query Strings setting in the Cache tab.
* [NEW FEATURE] Multiple CDN Mapping in the CDN tab.
* [IMPROVEMENT] Improve HTTP/HTTPS Compatibility setting in the Advanced tab.
* [IMPROVEMENT] Keep JS/CSS original position in HTML when excluded in setting.
* [IAPI] Reset client level credit after Image Optimization data is destroyed.
* [REFACTOR] Refactored build_input/textarea functions in admin_display class.
* [REFACTOR] Refactored CDN class.
* [GUI] Added a notice to Image Optimization and Crawler to warn when cache is disabled.
* [GUI] Improved image optimization indicator styles in Media Library List.

= 1.6.7 - December 15 2017 =
* [IAPI] Added ability to scan for new image thumbnail sizes and auto-resend image optimization requests.
* [IAPI] Added ability to destroy all optimization data.
* [IAPI] Updated IAPI to v1.6.7.
* [INTEGRATION] Fixed certain 3rd party plugins calling REST without user nonce causing logged in users to be served as guest.

= 1.6.6.1 - December 8 2017 =
* [IAPI] Limit first-time submission to one image group for test-run purposes.
* [BUGFIX] Fixed vary group generation issue associated with custom user role plugins.
* [BUGFIX] Fixed WooCommerce issue where logged-in users were erroneously purged when ESI is off.
* [BUGFIX] Fixed WooCommerce cache miss issue when ESI is off.

= 1.6.6 - December 6 2017 =
* [NEW FEATURE] Preserve EXIF in Media setting.
* [NEW FEATURE] Clear log button in Debug Log Viewer.
* [IAPI] Fixed notified images resetting to previous status when pulling.
* [IAPI] Fixed HTTPS compatibility for image optimization initialization.
* [IAPI] An error message is now displayed when image optimization request submission is bypassed due to a lack of credit.
* [IAPI] IAPI v1.6.6.
* [IMPROVEMENT] Support JS data-no-optimize attribute to bypass optimization.
* [GUI] Added image group wiki link.
* [INTEGRATION] Improved compatibility with Login With Ajax.
* [INTEGRATION] Added function_exists check for WooCommerce to avoid 500 errors.

= 1.6.5.1 - December 1 2017 =
* [HOTFIX] Fixed warning message on Edit .htaccess page.

= 1.6.5 - November 30 2017 =
* [IAPI] Manually pull image optimization action button.
* [IAPI] Automatic credit system for image optimization to bypass unfinished image optimization error.
* [IAPI] Notify failed images from LiteSpeed's Image Server.
* [IAPI] Reset/Clear failed images feature.
* [IAPI] Redesigned report page.
* [REFACTOR] Moved pull_img logic from admin_api to media.
* [BUGFIX] Fixed a compatibility issue for clients who have allow_url_open setting off.
* [BUGFIX] Fixed logged in users sometimes being served from guest cache.
* [UPDATE] Environment report is no longer saved to a file.
* [UPDATE] Removed crawler reset notification.
* [GUI] Added more details on image optimization.
* [GUI] Removed info page from admin menu.
* [GUI] Moved environment report from network level to single site level.
* [GUI] Crawler time added in a user friendly format.
* [INTEGRATION] Improved compatibility with FacetWP json call.

= 1.6.4 - November 22 2017 =
* [NEW FEATURE] Send env reports privately with a new built-in report number referral system.
* [IAPI] Increased request timeout to fix a cUrl 28 timeout issue.
* [BUGFIX] Fixed a TTL max value validation bug.
* [INTEGRATION] Improved Contact Form 7 REST call compatibility for logged in users.
* Thanks for all your ratings. That encouraged us to be more diligent. Happy Thanksgiving.

= 1.6.3 - November 17 2017 =
* [NEW FEATURE] Only async Google Fonts setting.
* [NEW FEATURE] Only create WebP images when optimizing setting.
* [NEW FEATURE] Batch switch images to original/optimized versions in Image Optimization.
* [NEW FEATURE] Browser Cache TTL setting.
* [NEW FEATURE] Cache WooCommerce Cart setting.
* [IMPROVEMENT] Moved optimized JS/CSS snippet in header html to after meta charset.
* [IMPROVEMENT] Added a constant for better JS/CSS optimization compatibility for different dir WordPress installation.
* [IAPI] Take over failed callback check instead of bypassing it.
* [IAPI] Image optimization requests are now limited to 500 images per request.
* [BUGFIX] Fixed a parsing failure bug not using attributes in html elements with dash.
* [BUGFIX] Fixed a bug causing non-script code to move to the top of a page when not using combination.
* [UPDATE] Added detailed logs for external link detection.
* [UPDATE] Added new lines in footer comment to avoid Firefox crash when enabled HTML minify.
* [API] `Purge private` / `Purge private all` / `Add private tag` functions.
* [GUI] Redesigned image optimization operation links in Media Lib list.
* [GUI] Tweaked wp-admin form save button position.
* [GUI] Added "learn more" link for image optimization.

= 1.6.2.1 - November 6 2017 =
* [INTEGRATION] Improved compatibility with old WooCommerce versions to avoid unknown 500 errors.
* [BUGFIX] Fixed WebP images sometimes being used in non-supported browsers.
* [BUGFIX] Kept query strings for HTTP/2 push to avoid re-fetching pushed sources.
* [BUGFIX] Excluded JS/CSS from HTTP/2 push when using CDN.
* [GUI] Fixed a typo in Media list.
* [GUI] Made more image optimization strings translatable.
* [GUI] Updated Tuning description to include API documentation.

= 1.6.2 - November 3 2017 =
* [NEW FEATURE] Do Not Cache Roles.
* [NEW FEATURE] Use WebP Images for supported browsers.
* [NEW FEATURE] Disable Optimization Poll ON/OFF Switch in Media tab.
* [NEW FEATURE] Revert image optimization per image in Media list.
* [NEW FEATURE] Disable/Enable image WebP per image in Media list.
* [IAPI] Limit optimized images fetching cron to a single process.
* [IAPI] Updated IAPI to v1.6.2.
* [IAPI] Fixed repeating image request issue by adding a failure status to local images.
* [REFACTOR] Refactored login vary logic.

= 1.6.1 - October 29 2017 =
* [IAPI] Updated LiteSpeed Image Optimization Server API to v1.6.1.

= 1.6 - October 27 2017 =
* [NEW FEATURE] Image Optimization.
* [NEW FEATURE] Role Excludes for Optimization.
* [NEW FEATURE] Combined CSS/JS Priority.
* [IMPROVEMENT] Bypass CDN for login/register page.
* [UPDATE] Expanded ExpiresByType rules to include new font types. ( Thanks to JMCA2 )
* [UPDATE] Removed duplicated type param in admin action link.
* [BUGFIX] Fixed CDN wrongly replacing img base64 and "fake" src in JS.
* [BUGFIX] Fixed image lazy load replacing base64 src.
* [BUGFIX] Fixed a typo in Optimize class exception.
* [GUI] New Tuning tab in admin settings panel.
* [REFACTOR] Simplified router by reducing actions and adding types.
* [REFACTOR] Renamed `run()` to `finalize()` in buffer process.

= 1.5 - October 17 2017 =
* [NEW FEATURE] Exclude JQuery (to fix inline JS error when using JS Combine).
* [NEW FEATURE] Load JQuery Remotely.
* [NEW FEATURE] JS Deferred Excludes.
* [NEW FEATURE] Lazy Load Images Excludes.
* [NEW FEATURE] Lazy Load Image Placeholder.
* [IMPROVEMENT] Improved Lazy Load size attribute for w3c validator.
* [UPDATE] Added basic caching info and LSCWP version to HTML comment.
* [UPDATE] Added debug log to HTML detection.
* [BUGFIX] Fixed potential font CORS issue when using CDN.
* [GUI] Added API docs to setting description.
* [REFACTOR] Relocated all classes under includes with backwards compatibility.
* [REFACTOR] Relocated admin templates.

= 1.4 - October 11 2017 =
* [NEW FEATURE] Lazy load images/iframes.
* [NEW FEATURE] Clean CSS/JS optimizer data functionality in DB Optimizer panel.
* [NEW FEATURE] Exclude certain URIs from optimizer.
* [IMPROVEMENT] Improved optimizer HTML check compatibility to avoid conflicts with ESI functions.
* [IMPROVEMENT] Added support for using ^ when matching the start of a path in matching settings.
* [IMPROVEMENT] Added wildcard support in CDN original URL.
* [IMPROVEMENT] Moved optimizer table initialization to admin setting panel with failure warning.
* [UPDATE] Added a one-time welcome banner.
* [UPDATE] Partly relocated class: 'api'.
* [API] Added API wrapper for removing wrapped HTML output.
* [INTEGRATION] Fixed WooCommerce conflict with optimizer.
* [INTEGRATION] Private cache support for WooCommerce v3.2.0+.
* [GUI] Added No Optimization menu to frontend.

= 1.3.1.1 - October 6 2017 =
* [BUGFIX] Improved optimizer table creating process in certain database charset to avoid css/js minify/combination failure.

= 1.3.1 - October 5 2017 =
* [NEW FEATURE] Remove WP Emoji Option.
* [IMPROVEMENT] Separated optimizer data from wp_options to improve compatibility with backup plugins.
* [IMPROVEMENT] Enhanced crawler cron hook to prevent de-scheduling in some cases.
* [IMPROVEMENT] Enhanced Remove Query Strings to also remove Emoji query strings.
* [IMPROVEMENT] Enhanced HTML detection when extra spaces are present at the beginning.
* [UPDATE] Added private cache support for OLS.
* [BUGFIX] Self-redirects are no longer cached.
* [BUGFIX] Fixed css async lib warning when loading in HTTP/2 push.

= 1.3 - October 1 2017 =
* [NEW FEATURE] Added Browser Cache support.
* [NEW FEATURE] Added Remove Query Strings support.
* [NEW FEATURE] Added Remove Google Fonts support.
* [NEW FEATURE] Added Load CSS Asynchronously support.
* [NEW FEATURE] Added Load JS Deferred support.
* [NEW FEATURE] Added Critical CSS Rules support.
* [NEW FEATURE] Added Private Cached URIs support.
* [NEW FEATURE] Added Do Not Cache Query Strings support.
* [NEW FEATURE] Added frontend adminbar shortcuts ( Purge this page/Do Not Cache/Private cache ).
* [IMPROVEMENT] Do Not Cache URIs now supports full URLs.
* [IMPROVEMENT] Improved performance of Do Not Cache settings.
* [IMPROVEMENT] Encrypted vary cookie.
* [IMPROVEMENT] Enhanced HTML optimizer.
* [IMPROVEMENT] Limited combined file size to avoid heavy memory usage.
* [IMPROVEMENT] CDN supports custom upload folder for media files.
* [API] Added purge single post API.
* [API] Added version compare API.
* [API] Enhanced ESI API for third party plugins.
* [INTEGRATION] Compatibility with NextGEN Gallery v2.2.14.
* [INTEGRATION] Compatibility with Caldera Forms v1.5.6.2+.
* [BUGFIX] Fixed CDN&Minify compatibility with css url links.
* [BUGFIX] Fixed .htaccess being regenerated despite there being no changes.
* [BUGFIX] Fixed CDN path bug for subfolder WP instance.
* [BUGFIX] Fixed crawler path bug for subfolder WP instance with different site url and home url.
* [BUGFIX] Fixed a potential Optimizer generating redundant duplicated JS in HTML bug.
* [GUI] Added a more easily accessed submit button in admin settings.
* [GUI] Admin settings page cosmetic changes.
* [GUI] Reorganized GUI css/img folder structure.
* [REFACTOR] Refactored configuration init.
* [REFACTOR] Refactored admin setting save.
* [REFACTOR] Refactored .htaccess operator and rewrite rule generation.

= 1.2.3.1 - September 20 2017 =
* [UPDATE] Improved PHP5.3 compatibility.

= 1.2.3 - September 20 2017 =
* [NEW FEATURE] Added CDN support.
* [IMPROVEMENT] Improved compatibility when upgrading by fixing a possible fatal error.
* [IMPROVEMENT] Added support for custom wp-content paths.
* [BUGFIX] Fixed non-primary network blogs not being able to minify.
* [BUGFIX] Fixed HTML Minify preventing Facebook from being able to parse og tags.
* [BUGFIX] Preview page is no longer cacheable.
* [BUGFIX] Corrected log and crawler timezone to match set WP timezone.
* [GUI] Revamp of plugin GUI.

= 1.2.2 - September 15 2017 =
* [NEW FEATURE] Added CSS/JS minification.
* [NEW FEATURE] Added CSS/JS combining.
* [NEW FEATURE] Added CSS/JS HTTP/2 server push.
* [NEW FEATURE] Added HTML minification.
* [NEW FEATURE] Added CSS/JS cache purge button in management.
* [UPDATE] Improved debug log formatting.
* [UPDATE] Fixed some description typos.

= 1.2.1 - September 7 2017 =
* [NEW FEATURE] Added Database Optimizer.
* [NEW FEATURE] Added Tab switch shortcut.
* [IMPROVEMENT] Added cache disabled check for management pages.
* [IMPROVEMENT] Renamed .htaccess backup for security.
* [BUGFIX] Fixed woocommerce default ESI setting bug.
* [REFACTOR] Show ESI page for OLS with notice.
* [REFACTOR] Management Purge GUI updated.

= 1.2.0.1 - September 1 2017 =
* [BUGFIX] Fixed a naming bug for network constant ON2.

= 1.2.0 - September 1 2017 =
* [NEW FEATURE] Added ESI support.
* [NEW FEATURE] Added a private cache TTL setting.
* [NEW FEATURE] Debug level can now be set to either 'Basic' or 'Advanced'.
* [REFACTOR] Renamed const 'NOTSET' to 'ON2' in class config.

= 1.1.6 - August 23 2017 =
* [NEW FEATURE] Added option to privately cache logged-in users.
* [NEW FEATURE] Added option to privately cache commenters.
* [NEW FEATURE] Added option to cache requests made through WordPress REST API.
* [BUGFIX] Fixed network 3rd-party full-page cache detection bug.
* [GUI] New Cache and Purge menus in Settings.

= 1.1.5.1 - August 16 2017 =
* [IMPROVEMENT] Improved compatibility of frontend&backend .htaccess path detection when site url is different than installation path.
* [UPDATE] Removed unused format string from header tags.
* [BUGFIX] 'showheader' Admin Query String now works.
* [REFACTOR] Cache tags will no longer output if not needed.

= 1.1.5 - August 10 2017 =
* [NEW FEATURE] Scheduled Purge URLs feature.
* [NEW FEATURE] Added buffer callback to improve compatibility with some plugins that force buffer cleaning.
* [NEW FEATURE] Hide purge_all admin bar quick link if cache is disabled.
* [NEW FEATURE] Required htaccess rules are now displayed when .htaccess is not writable.
* [NEW FEATURE] Debug log features: filter log support; heartbeat control; log file size limit; log viewer.
* [IMPROVEMENT] Separate crawler access log.
* [IMPROVEMENT] Lazy PURGE requests made after output are now queued and working.
* [IMPROVEMENT] Improved readme.txt with keywords relating to our compatible plugins list.
* [UPDATE] 'ExpiresDefault' conflict msg is now closeable and only appears in the .htaccess edit screen.
* [UPDATE] Improved debug log formatting.
* [INTEGRATION] Compatibility with MainWP plugin.
* [BUGFIX] Fixed Woocommerce order not purging product stock quantity.
* [BUGFIX] Fixed Woocommerce scheduled sale price not updating issue.
* [REFACTOR] Combined cache_enable functions into a single function.

= 1.1.4 - August 1 2017 =
* [IMPROVEMENT] Unexpected rewrite rules will now show an error message.
* [IMPROVEMENT] Added Cache Tag Prefix setting info in the Env Report and Info page.
* [IMPROVEMENT] LSCWP setting link is now displayed in the plugin list.
* [IMPROVEMENT] Improved performance when setting cache control.
* [UPDATE] Added backward compatibility for v1.1.2.2 API calls. (used by 3rd-party plugins)
* [BUGFIX] Fixed WPCLI purge tag/category never succeeding.

= 1.1.3 - July 31 2017 =
* [NEW FEATURE] New LiteSpeed_Cache_API class and documentation for 3rd party integration.
* [NEW FEATURE] New API function litespeed_purge_single_post($post_id).
* [NEW FEATURE] PHP CLI support for crawler.
* [IMPROVEMENT] Set 'no cache' for same location 301 redirects.
* [IMPROVEMENT] Improved LiteSpeed footer comment compatibility.
* [UPDATE] Removed 'cache tag prefix' setting.
* [BUGFIX] Fixed a bug involving CLI purge all.
* [BUGFIX] Crawler now honors X-LiteSpeed-Cache-Control for the 'no-cache' header.
* [BUGFIX] Cache/rewrite rules are now cleared when the plugin is uninstalled.
* [BUGFIX] Prevent incorrect removal of the advanced-cache.php on deactivation if it was added by another plugin.
* [BUGFIX] Fixed subfolder WP installations being unable to Purge By URL using a full URL path.
* [REFACTOR] Reorganized existing code for an upcoming ESI release.

= 1.1.2.2 - July 13 2017 =
* [BUGFIX] Fixed blank page in Hebrew language post editor by removing unused font-awesome and jquery-ui css libraries.

= 1.1.2.1 - July 5 2017 =
* [UPDATE] Improved compatibility with WooCommerce v3.1.0.

= 1.1.2 - June 20 2017 =
* [BUGFIX] Fixed missing form close tag.
* [UPDATE] Added a wiki link for enabling the crawler.
* [UPDATE] Improved Site IP description.
* [UPDATE] Added an introduction to the crawler on the Information page.
* [REFACTOR] Added more detailed error messages for Site IP and Custom Sitemap settings.

= 1.1.1.1 - June 15 2017 =
* [BUGFIX] Hotfix for insufficient validation of site IP value in crawler settings.

= 1.1.1 - June 15 2017 =
* [NEW] As of LiteSpeed Web Server v.5.1.16, the crawler can now be enabled/disabled at the server level.
* [NEW] Added the ability to provide a custom sitemap for crawling.
* [NEW] Added ability to use site IP address directly in crawler settings.
* [NEW] Crawler performance improved with the use of new custom user agent 'lsrunner'.
* [NEW] "Purge By URLs" now supports full URL paths.
* [NEW] Added thirdparty WP-PostRatings compatibility.
* [BUGFIX] Cache is now cleared when changing post status from published to draft.
* [BUGFIX] WHM activation message no longer continues to reappear after being dismissed.
* [COSMETIC] Display recommended values for settings.

= 1.1.0.1 - June 8 2017 =
* [UPDATE] Improved default crawler interval setting.
* [UPDATE] Tested up to WP 4.8.
* [BUGFIX] Fixed compatibility with plugins that output json data.
* [BUGFIX] Fixed tab switching bug.
* [BUGFIX] Removed occasional duplicated messages on save.
* [COSMETIC] Improved crawler tooltips and descriptions.

= 1.1.0 - June 6 2017 =
* [NEW] Added a crawler - this includes configuration options and a dedicated admin page. Uses wp-cron
* [NEW] Added integration for WPLister
* [NEW] Added integration for Avada
* [UPDATE] General structure of the plugin revamped
* [UPDATE] Improved look of admin pages
* [BUGFIX] Fix any/all wp-content path retrieval issues
* [BUGFIX] Use realpath to clear symbolic link when determining .htaccess paths
* [BUGFIX] Fixed a bug where upgrading multiple plugins did not trigger a purge all
* [BUGFIX] Fixed a bug where cli import_options did not actually update the options.
* [REFACTOR] Most of the files in the code were split into more, smaller files

= 1.0.15 - April 20 2017 =
* [NEW] Added Purge Pages and Purge Recent Posts Widget pages options.
* [NEW] Added wp-cli command for setting and getting options.
* [NEW] Added an import/export options cli command.
* [NEW] Added wpForo integration.
* [NEW] Added Theme My Login integration.
* [UPDATE] Purge adjacent posts when publish a new post.
* [UPDATE] Change environment report file to .php and increase security.
* [UPDATE] Added new purgeby option to wp-cli.
* [UPDATE] Remove nag for multiple sites.
* [UPDATE] Only inject LiteSpeed javascripts in LiteSpeed pages.
* [REFACTOR] Properly check for zero in ttl settings.
* [BUGFIX] Fixed the 404 issue that can be caused by some certain plugins when save the settings.
* [BUGFIX] Fixed mu-plugin compatibility.
* [BUGFIX] Fixed problem with creating zip backup.
* [BUGFIX] Fixed conflict with jetpack.

= 1.0.14.1 - January 31 2017 =
* [UPDATE] Removed Freemius integration due to feedback.

= 1.0.14 - January 30 2017 =
* [NEW] Added error page caching. Currently supports 403, 404, 500s.
* [NEW] Added a purge errors action.
* [NEW] Added wp-cli integration.
* [UPDATE] Added support for multiple varies.
* [UPDATE] Reorganize the admin interface to be less cluttered.
* [UPDATE] Add support for LiteSpeed Web ADC.
* [UPDATE] Add Freemius integration.
* [REFACTOR] Made some changes so that the rewrite rules are a little more consistent.
* [BUGFIX] Check member type before adding purge all button.
* [BUGFIX] Fixed a bug where activating/deactivating the plugin quickly caused the WP_CACHE error to show up.
* [BUGFIX] Handle more characters in the rewrite parser.
* [BUGFIX] Correctly purge posts when they are made public/private.

= 1.0.13.1 - November 30 2016 =
* [BUGFIX] Fixed a bug where a global was being used without checking existence first, causing unnecessary log entries.

= 1.0.13 - November 28 2016 =
* [NEW] Add an Empty Entire Cache button.
* [NEW] Add stale logic to certain purge actions.
* [NEW] Add option to use primary site settings for all subsites in a multisite environment.
* [NEW] Add support for Aelia CurrencySwitcher
* [UPDATE] Add logic to allow third party vary headers
* [UPDATE] Handle password protected pages differently.
* [BUGFIX] Fixed bug caused by saving settings.
* [BUGFIX] FIxed bug when searching for advanced-cache.php

= 1.0.12 - November 14 2016 =
* [NEW] Added logic to generate environment reports.
* [NEW] Created a notice that will be triggered when the WHM Plugin installs this plugin. This will notify users when the plugin is installed by their server admin.
* [NEW] Added the option to cache 404 pages via 404 Page TTL setting.
* [NEW] Reworked log system to be based on selection of yes or no instead of log level.
* [NEW] Added support for Autoptimize.
* [NEW] Added Better WP Minify integration.
* [UPDATE] On plugin disable, clear .htaccess.
* [UPDATE] Introduced URL tag. Changed Purge by URL to use this new tag.
* [BUGFIX] Fixed a bug triggered when .htaccess files were empty.
* [BUGFIX] Correctly determine when to clear files in multisite environments (wp-config, advanced-cache, etc.).
* [BUGFIX] When disabling the cache, settings changed in the same save will now be saved.
* [BUGFIX] Various bugs from setting changes and multisite fixed.
* [BUGFIX] Fixed two bugs with the .htaccess path search.
* [BUGFIX] Do not alter $_GET in add_quick_purge. This may cause issues for functionality occurring later in the same request.
* [BUGFIX] Right to left radio settings were incorrectly displayed. The radio buttons themselves were the opposite direction of the associated text.

= 1.0.11 - October 11 2016 =
* [NEW] The plugin will now set cachelookup public on.
* [NEW] New option - check advanced-cache.php. This enables users to have two caching plugins enabled at the same time as long as the other plugin is not used for caching purposes. For example, using another cache plugin for css/js minification.
* [UPDATE] Rules added by the plugin will now be inserted into an LSCACHE START/END PLUGIN comment block.
* [UPDATE] For woocommerce pages, if a user visits a non-cached page with a non-empty cart, do not cache the page.
* [UPDATE] If woocommerce needs to display any notice, do not cache the page.
* [UPDATE] Single site settings are now in both the litespeed cache submenu and the settings submenu.
* [BUGFIX] Multisite network options were not updated on upgrade. This is now corrected.

= 1.0.10 - September 16 2016 =
* Added a check for LSCACHE_NO_CACHE definition.
* Added a Purge All button to the admin bar.
* Added logic to purge the cache when upgrading a plugin or theme. By default this is enabled on single site installations and disabled on multisite installations.
* Added support for WooCommerce Versions < 2.5.0.
* Added .htaccess backup rotation. Every 10 backups, an .htaccess archive will be created. If one already exists, it will be overwritten.
* Moved some settings to the new Specific Pages tab to reduce clutter in the General tab.
* The .htaccess editor is now disabled if DISALLOW_FILE_EDIT is set.
* After saving the Cache Tag Prefix setting, all cache will be purged.

= 1.0.9.1 - August 26 2016 =
* Fixed a bug where an error displayed on the configuration screen despite not being an error.
* Change logic to check .htaccess file less often.

= 1.0.9 - August 25 2016 =
* [NEW] Added functionality to cache and purge feeds.
* [NEW] Added cache tag prefix setting to avoid conflicts when using LiteSpeed Cache for WordPress with LiteSpeed Cache for XenForo and LiteMage.
* [NEW] Added hooks to allow third party plugins to create config options.
* [NEW] Added WooCommerce config options.
* The plugin now also checks for wp-config in the parent directory.
* Improved WooCommerce support.
* Changed .htaccess backup process. Will create a .htaccess_lscachebak_orig file if one does not exist. If it does already exist, creates a backup using the date and timestamp.
* Fixed a bug where get_home_path() sometimes returned an invalid path.
* Fixed a bug where if the .htaccess was removed from a WordPress subdirectory, it was not handled properly.

= 1.0.8.1 - July 28 2016 =
* Fixed a bug where check cacheable was sometimes not hit.
* Fixed a bug where extra slashes in clear rules were stripped.

= 1.0.8 - July 25 2016 =
* Added purge all on update check to purge by post id logic.
* Added uninstall logic.
* Added configuration for caching favicons.
* Added configuration for caching the login page.
* Added configuration for caching php resources (scripts/stylesheets accessed as .php).
* Set login cookie if user is logged in and it isn’t set.
* Improved NextGenGallery support to include new actions.
* Now displays a notice on the network admin if WP_CACHE is not set.
* Fixed a few php syntax issues.
* Fixed a bug where purge by pid didn’t work.
* Fixed a bug where the Network Admin settings were shown when the plugin was active in a subsite, but not network active.
* Fixed a bug where the Advanced Cache check would sometimes not work.

= 1.0.7.1 - May 26 2016 =
* Fixed a bug where enabling purge all in the auto purge on update settings page did not purge the correct blogs.
* Fixed a bug reported by user wpc on our forums where enabling purge all in the auto purge on update settings page caused nothing to be cached.

= 1.0.7 - May 24 2016 =
* Added login cookie configuration to the Advanced Settings page.
* Added support for WPTouch plugin.
* Added support for WP-Polls plugin.
* Added Like Dislike Counter third party integration.
* Added support for Admin IP Query String Actions.
* Added confirmation pop up for purge all.
* Refactor: LiteSpeed_Cache_Admin is now split into LiteSpeed_Cache_Admin, LiteSpeed_Cache_Admin_Display, and LiteSpeed_Cache_Admin_Rules
* Refactor: Rename functions to accurately represent their functionality
* Fixed a bug that sometimes caused a “no valid header” error message.

= 1.0.6 - May 5 2016 =
* Fixed a bug reported by Knut Sparhell that prevented dashboard widgets from being opened or closed.
* Fixed a bug reported by Knut Sparhell that caused problems with https support for admin pages.

= 1.0.5 - April 26 2016 =
* [BETA] Added NextGen Gallery plugin support.
* Added third party plugin integration.
* Improved cache tag system.
* Improved formatting for admin settings pages.
* Converted bbPress to use the new third party integration system.
* Converted WooCommerce to use the new third party integration system.
* If .htaccess is not writable, disable separate mobile view and do not cache cookies/user agents.
* Cache is now automatically purged when disabled.
* Fixed a bug where .htaccess was not checked properly when adding common rules.
* Fixed a bug where multisite setups would be completely purged when one site requested a purge all.

= 1.0.4 - April 7 2016 =
* Added logic to cache commenters.
* Added htaccess backup to the install script.
* Added an htaccess editor in the wp-admin dashboard.
* Added do not cache user agents.
* Added do not cache cookies.
* Created new LiteSpeed Cache Settings submenu entries.
* Implemented Separate Mobile View.
* Modified WP_CACHE not defined message to only show up for users who can manage options.
* Moved enabled all/disable all from network management to network settings.
* Fixed a bug where WP_CACHE was not defined on activation if it was commented out.

= 1.0.3 - March 23 2016 =
* Added a Purge Front Page button to the LiteSpeed Cache Management page.
* Added a Default Front Page TTL option to the general settings.
* Added ability to define web application specific cookie names through rewrite rules to handle logged-in cookie conflicts when using multiple web applications. <strong>[Requires LSWS 5.0.15+]</strong>
* Improved WooCommerce handling.
* Fixed a bug where activating lscwp sets the “enable cache” radio button to enabled, but the cache was not enabled by default.
* Refactored code to make it cleaner.
* Updated readme.txt.

= 1.0.2 - March 11 2016 =
* Added a "Use Network Admin Setting" option for "Enable LiteSpeed Cache". For single sites, this choice will default to enabled.
* Added enable/disable all buttons for network admin. This controls the setting of all managed sites with "Use Network Admin Setting" selected for "Enable LiteSpeed Cache".
* Exclude by Category/Tag are now text areas to avoid slow load times on the LiteSpeed Cache Settings page for sites with a large number of categories/tags.
* Added a new line to advanced-cache.php to allow identification as a LiteSpeed Cache file.
* Activation/Deactivation are now better handled in multi-site environments.
* Enable LiteSpeed Cache setting is now a radio button selection instead of a single checkbox.
* Can now add '$' to the end of a URL in Exclude URI to perform an exact match.
* The _lscache_vary cookie will now be deleted upon logout.
* Fixed a bug in multi-site setups that would cause a "function already defined" error.

= 1.0.1 - March 8 2016 =
* Added Do Not Cache by URI, by Category, and by Tag.  URI is a prefix/string equals match.
* Added a help tab for plugin compatibilities.
* Created logic for other plugins to purge a single post if updated.
* Fixed a bug where woocommerce pages that display the cart were cached.
* Fixed a bug where admin menus in multi-site setups were not correctly displayed.
* Fixed a bug where logged in users were served public cached pages.
* Fixed a compatibility bug with bbPress.  If there is a new forum/topic/reply, the parent pages will now be purged as well.
* Fixed a bug that didn't allow cron job to update scheduled posts.

= 1.0.0 - January 20 2016 =
* Initial Release.
