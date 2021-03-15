=== Plugin Name ===
Contributors: wetterkrank
Donate link: http://escapefromberl.in
Tags: travel, flights, prices
Requires at least: 3.0.1
Tested up to: 5.3
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A WordPress plugin to display the lowest flight price for a given route/dates in your blog.

== Description ==

A WordPress plugin to display the lowest flight price for a given route/dates in your blog. 
The prices are provided by Kiwi.com; accuracy is pretty high.

== Installation ==

Copy the plugin into the plugins directory; activate.

To insert the price in your post or page, insert the shortcode *fpm_price* in the text.
Shortcode parameters and examples:

- type (micro, mini, full)
- from (BER, berlin_de)
- to (MUC, innsbruck_at)
- earliest (2018-01-30, tomorrow)
- latest (2018-12-31, +3 months)
- min_days (2)
- max_days (4)
- direct_only (true, false)
- currency (EUR)
- locale (en)

Examples:
[fpm_price from="BER" to="MUC" earliest="tomorrow" latest="+3 months" currency="EUR"]

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png`
(or jpg, jpeg, gif).
2. This is the second screen shot

== Changelog ==

= 0.1 =
* A working beta.
