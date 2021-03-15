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
                Keep an eye on the quotes -- they can be messed up when pasting.
            </p>
            <p>
                <b>Shortcode parameters & examples (+ default values):</b>
            </p>
            <ul style="list-style: disc; padding: 0 20px;">
                <li>from: BER, berlin_de (required)</li>
                <li>to: MUC, innsbruck_at (required)</li>
                <li>earliest: 2022-01-30, tomorrow (now)</li>
                <li>latest: 2022-12-31, +2 weeks (now + 3 months)</li>
                <li>min_days (7)</li>
                <li>max_days (14)</li>
                <li>max_stops (0)</li>
                <li>transport: aircraft, bus, train -- comma-separated (aircraft)</li>
                <li>currency (EUR)</li>
                <li>locale (en)</li>
            </ul>
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
