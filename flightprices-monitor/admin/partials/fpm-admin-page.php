<!-- Provides the admin area view for the plugin -->
<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap" id="flymon-admin">
    <h1>Flymon Settings</h1>
    <?php if (!empty($_GET['updated'])) : ?>
        <div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
            <p><strong><?php _e('Settings saved.') ?></strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
        </div>
    <?php endif; ?>

    <div style="display: flex; flex-wrap: wrap; gap: 5%;">
        <div>
            <form action="options.php" method="POST">
                <?php settings_fields($this->get_slug()); ?>
                <?php do_settings_sections($this->get_slug()); ?>
                <?php submit_button(__('Save Changes'), 'primary'); ?>
            </form>
        </div>

        <div>
            <h2>Shortcodes cheatsheet</h2>
            <p>
                To display the price in your post, insert the shortcode <code>trip_price</code> in the paragraph.<br/>
                Keep an eye on the quotation marks -- they can be messed up when pasting.
            </p>
            <p>
                <b>Shortcode parameters & examples (+ default values):</b>
            </p>
            <ul style="list-style: disc; padding: 0 20px;">
                <li>from: BER, berlin_de (required)</li>
                <li>to: MUC, innsbruck_at (required)</li>
                <li>earliest: 2023-01-30, tomorrow (now)</li>
                <li>latest: 2023-12-31, +2 weeks (now + 3 months)</li>
                <li>min_days (none)</li>
                <li>max_days (none)</li>
                <li>max_stops (0)</li>
                <li>transport: aircraft, bus, train -- comma-separated (aircraft)</li>
                <li>currency (EUR)</li>
                <li>locale (en)</li>
                <li>deeplink_type: search|booking (search)</li>
            </ul>

            <p>
              For locations, use IATA codes or check out <a href="https://tequila.kiwi.com/portal/docs/tequila_api/locations_api">Kiwi's locations API</a>.<br />
              For dates, you can use a wide range of expressions -- whatever can be parsed by PHP's <a href="https://www.php.net/manual/en/function.strtotime.php">`strtotime()`</a>.<br />
              Either both max and min days at destination must be specified, or none of them. If none are set, Kiwi searches for one way.<br />
            </p>
            <p>
                <b>Examples:</b><br/>
            </p>
<textarea name="" id="" cols="80" rows="15">
[trip_price from="BER" to="MUC" earliest="tomorrow" latest="+6 months" currency="EUR"]

[trip_price from="LON" to="IST" earliest="+1 week" latest="+1 month" min_days="6" max_days="8" currency="GBP"]

[trip_price from="AMS" to="PAR" latest="+3 months" min_days="2" max_days="3" transport="train,bus"]

[trip_price from="NYC" to="TYO" transport="bus"]
</textarea>
        </div>
    </div>

</div>
