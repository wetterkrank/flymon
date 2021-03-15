# Flymon

A WordPress plugin to display the lowest flight price for a given route/dates in your blog.  
Powers the cheapest flights monitor here: https://escapefromberl.in

## Installation

Copy the plugin into the plugins directory; activate.

## Usage

To display the price in your post or page, insert the shortcode trip_price in the paragraph.

**Shortcode parameters & examples (+ default values):**
-   from: BER, berlin\_de (required)
-   to: MUC, innsbruck\_at (required)
-   earliest: 2022-01-30, tomorrow (now)
-   latest: 2022-12-31, +2 weeks (now + 3 months)
-   min\_days (7)
-   max\_days (14)
-   max\_stops (0)
-   transport: aircraft, bus, train -- comma-separated (aircraft)
-   currency (EUR)
-   locale (en)

Examples:
```
[trip_price from="BER" to="MUC" earliest="tomorrow" latest="+6 months" currency="EUR"]
[trip_price from="LON" to="IST" earliest="+1 week" latest="+1 month" min_days="6" max_days="8" currency="GBP"]
[trip_price from="AMS" to="PAR" latest="+3 months" min_days="2" max_days="3" transport="train,bus"]
[trip_price from="NYC" to="TYO" transport="bus"]
```

## License

This plugin is licensed under the GPL v2 or later.

> This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License, version 2, as published by the Free Software Foundation.

> This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

> You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA

A copy of the license is included in the root of the plugin’s directory. The file is named `LICENSE`.
