=== Plugin Name ===
Contributors: wetterkrank
Donate link: http://escapefromberl.in
Tags: travel, flight prices, rail prices, bus prices
Requires at least: 3.0.1
Tested up to: 5.7
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A WordPress plugin that displays the lowest travel prices for given routes/dates.

== Description ==

A WordPress plugin that displays the trip price for given route/dates.
The prices are provided by Kiwi.com API and cached (cache time can be configured).
You can set your Kiwi Tequila affiliate id to be added to deeplinks.

== Installation ==

Copy the plugin into the plugins directory; activate.
In WP Settings menu, open Flymon page and add your Kiwi.com API key.

To display the price in your post or page, insert the shortcode *trip_price* in the paragraph.  

For locations, use IATA codes or check out Kiwi's locations API.
For dates, you can use a wide range of expressions -- whatever can be parsed by PHP's *strtotime* function.

**Shortcode parameters & examples (+ default values):**

- from: BER, berlin\_de (required)
- to: MUC, innsbruck\_at (required)
- earliest: 2022-01-30, tomorrow (now)
- latest: 2022-12-31, +2 weeks (now + 3 months)
- min\_days (7)
- max\_days (14)
- max\_stops (0)
- transport: aircraft, bus, train -- comma-separated (aircraft)
- currency (EUR)
- locale (en)

Shortcode examples:

[trip_price from="LON" to="IST" earliest="tomorrow" latest="+1 month" min_days="6" max_days="8" currency="GBP"]
[trip_price from="AMS" to="PAR" latest="+3 months" min_days="2" max_days="3" transport="train,bus"]
[trip_price from="NYC" to="TYO" transport="bus"] :)

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png`
(or jpg, jpeg, gif).
2. This is the second screen shot

== Changelog ==

= 0.1 =
* A working beta.

= 0.2 =
* Moved the API request and result caching to Wordpress itself, instead of an external proxy.
* Added the vehicle type support, tooltips on hover, some error handling.