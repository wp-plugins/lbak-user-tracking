<div id="top">
    <a href="#general_settings" class="button-primary">General Settings</a>
    <a href="#widget_settings" class="button-primary">Widget Settings</a>
    <a href="#tracker_settings" class="button-primary">Tracker Settings</a>
    <a href="#search_settings" class="button-primary">Search Settings</a>
    <a href="#stats_settings" class="button-primary">Stats Settings</a>
</div>
<?php

//If the form is submitted, process it with the following.
if ($_POST['widget_submit'] == 'Submit') {
    check_admin_referer('lbakut_nonce');
    unset($_POST['widget_submit']);
    $options['widget_ignore_admin'] = $wpdb->escape($_POST['widget_ignore_admin']);
    $options['widget_no_to_display'] = $_POST['widget_no_to_display'] ? intval($_POST['widget_no_to_display']) : 10;

    if ($_POST['widget_ignore_ip'] != 'Add ignored IP addresses in here.') {
        $options['widget_ignored_ips'] = explode("\n", $wpdb->escape($_POST['widget_ignore_ip']));
        if (!is_array($options['widget_ignored_ips'])) {
            $options['widget_ignored_ips'] = array(0 => '');
        }
    }

    if ($_POST['widget_ignore_user'] != 'Add ignored user IDs in here.') {
        $options['widget_ignored_users'] = explode("\n", $wpdb->escape($_POST['widget_ignore_user']));
        if (!is_array($options['widget_ignored_users'])) {
            $options['widget_ignored_users'] = array(0 => '');
        }
    }

    foreach ($_POST as $k => $v) {
        if (preg_match('/widget_show/',$k)) {
            $options[$k] = $wpdb->escape($_POST[$k]);
        }
    }

    lbakut_update_options($options);

    $updated = '<div class="updated">Widget Options Updated!</div>';
}
else if ($_POST['track_submit'] == 'Submit') {
    check_admin_referer('lbakut_nonce');
    unset($_POST['track_submit']);
    foreach ($_POST as $k => $v) {
        if (preg_match('/track_/',$k)) {
            $options[$k] = $wpdb->escape($_POST[$k]);
        }
    }
    lbakut_update_options($options);
    $updated = '<div class="updated">Tracker Options Updated!</div>';
}
else if ($_POST['search_submit'] == 'Submit') {
    check_admin_referer('lbakut_nonce');
    unset($_POST['search_submit']);
    foreach ($_POST as $k => $v) {
        if (preg_match('/search_/',$k)) {
            $options[$k] = $wpdb->escape($_POST[$k]);
        }
    }
    lbakut_update_options($options);
    $updated = '<div class="updated">Search Options Updated!</div>';
}
else if ($_POST['general_submit'] == 'Submit') {
    check_admin_referer('lbakut_nonce');
    $options['delete_on_uninstall'] = $wpdb->escape($_POST['delete_on_uninstall']);
    $options['track_ignore_admin'] = $wpdb->escape($_POST['track_ignore_admin']);
    $options['use_time_ago'] = $wpdb->escape($_POST['use_time_ago']);
    $options['time_format'] = $wpdb->escape($_POST['time_format']);
    lbakut_update_options($options);
    $updated = '<div class="updated">General Options Updated!</div>';
}
//Stats POST updtaing.
else if (isset($_POST['stats_submit'])) {
    check_admin_referer('lbakut_nonce');
    $options['stats_enable_shortcodes'] = $wpdb->escape($_POST['stats_enable_shortcodes']);
    $options['stats_update_frequency'] = $wpdb->escape($_POST['stats_update_frequency']);
    $options['stats_browser_widget'] = $wpdb->escape($_POST['stats_browser_widget']);
    $options['stats_os_widget'] = $wpdb->escape($_POST['stats_os_widget']);
    $options['stats_pageviews_widget'] = $wpdb->escape($_POST['stats_pageviews_widget']);
    lbakut_update_options($options);
    lbakut_cron_jobs('reset');
    $updated = '<div class="updated">Stats settings updated!</div>';
}
else {
    $updated = '';
}

//Get the checkbox setting for the ignore_admin box.
if ($options['widget_ignore_admin']) {
    $widget_ignore_admin_checked = 'checked';
}
else {
    $widget_ignore_admin_checked = '';
}

if ($options['delete_on_uninstall'] == true) {
    $delete_on_uninstall_checked = 'checked';
}
else {
    $delete_on_uninstall_checked = '';
}

//This is to keep the implode() function happy.
if ($options['widget_ignored_ips'] == '') {
    $options['widget_ignored_ips'] = array(0 => 'Add ignored IP addresses in here.');
}
if ($options['widget_ignored_users'] == '') {
    $options['widget_ignored_users'] = array(0 => 'Add ignored user IDs in here.');
}

?>
<br />
<?php echo $updated; ?>

<div id="poststuff" class="ui-sortable meta-box-sortable">
    <div class="postbox" id="general_settings">
        <h3><?php _e('General Settings', 'lbakut'); ?></h3>
        <div class="inside">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>?page=lbakut"
                  method="post" name="lbakut_general_form">
                <?php wp_nonce_field('lbakut_nonce'); ?>
                <p>
                    <?php _e('', 'lbakut'); ?>
                </p>
                <table class="widefat">
                    <thead>
                    <th><?php _e('Setting', 'lbakut'); ?></th>
                    <th></th>
                    <th><?php _e('Description', 'lbakut'); ?></th>
                    </thead>
                    <tr>
                        <td>
                            <b><?php _e('Delete data on uninstall?', 'lbakut'); ?></b>
                        </td>
                        <td>
                            <input type="checkbox" id="delete_on_uninstall"
                                   name="delete_on_uninstall" value="1" <?php echo $delete_on_uninstall_checked; ?> />
                        </td>
                        <td>
                            <p>
                                <?php _e('If this is checked, the lbakut_activity_log table
                                will be deleted from your database when you uninstall
                                this plugin. This means that you will lose all of the
                                information you have collected while using this plugin.
                                You will also lose all of your preferences.', 'lbakut'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php _e('<b>Ignore Admin?</b>', 'lbakut'); ?>
                        </td>
                        <td>
                            <input type="hidden" name="track_ignore_admin"
                                   value="0" />
                            <input type="checkbox" name="track_ignore_admin"
                                   value="1" <?php echo $options['track_ignore_admin'] ? 'checked' : ''; ?> />
                        </td>
                        <td>
                            <?php _e('Check this to stop tracking admin users.',
                                    'lbakut'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php _e('<b>Use "time ago" format?"', 'lbakut'); ?>
                        </td>
                        <td>
                            <input type="hidden" name="use_time_ago" value="0" />
                            <input type="checkbox" name="use_time_ago" value="1"
                                   <?php echo $options['use_time_ago'] ? 'checked' : '' ?> />
                        </td>
                        <td>
                            <?php _e('If this is checked, the time column will
                                display as values like "32s ago" and "3d ago"
                                to represent 32 seconds ago and 3 days ago
                                respectively. After 7 days, the time is formatted
                                as whatever you specify as your Time Format.', 'lbakut'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php _e('<b>Time Format</b>', 'lbakut'); ?>
                        </td>
                        <td>
                            <input type="text" name="time_format"
                                   value="<?php echo $options['time_format'] ?>" />
                        </td>
                        <td>
                            <?php _e('This is the way your time is formatted
                                using the PHP "strftime" function. The manual
                                on how to use this can be found
                                <a href="http://php.net/manual/en/function.strftime.php" target="_blank">here</a>', 'lbakut'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b><?php _e('Submit', 'lbakut'); ?></b>
                        </td>
                        <td>
                            <input type="submit" name="general_submit" class="button-primary" value="Submit" />
                        </td>
                        <td>
                            <p><?php _e('Submit the form and change the general options.', 'lbakut'); ?></p>
                        </td>
                    </tr>
                    <thead>
                    <th><?php _e('Setting', 'lbakut'); ?></th>
                    <th></th>
                    <th><?php _e('Description', 'lbakut'); ?></th>
                    </thead>
                </table>
            </form>
        </div>
    </div>
</div>

<div id="poststuff" class="ui-sortable meta-box-sortable">
    <div class="postbox" id="widget_settings">
        <h3><?php _e('Widget Settings', 'lbakut'); ?></h3>
        <div class="inside">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>?page=lbakut"
                  method="post" name="lbakut_widget_form">
                <?php wp_nonce_field('lbakut_nonce'); ?>
                <p>
                    <?php _e('The following options edit the display and behaviour of the
                    admin dashboard widget for the LBAK User Tracking plugin.
                    <b>Note that none of these options affect what data gets tracked
                        by the plugin, settings to edit what gets tracked are located
                        further down the page.</b>', 'lbakut'); ?>
                </p>
                <table class="widefat">
                    <thead>
                    <th><?php _e('Setting', 'lbakut'); ?></th>
                    <th></th>
                    <th><?php _e('Description', 'lbakut'); ?></th>
                    </thead>
                    <tr>
                        <td>
                            <label for="widget_ignore_ip">
                                <b><?php _e('IP Ignore List', 'lbakut'); ?></b>
                            </label>
                        </td>
                        <td>
                            <textarea id="widget_ignore_ip" name="widget_ignore_ip" rows="5" cols="15"><?php echo implode("\n", $options['widget_ignored_ips']); ?></textarea>
                        </td>
                        <td>
                            <p>
                                <?php _e('If you want to ignore certain IP addresses from the recent visitors
                                list please put one per line in this text box.', 'lbakut'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="widget_ignore_user">
                                <b><?php _e('User Ignore List', 'lbakut'); ?></b>
                            </label>
                        </td>
                        <td>
                            <textarea id="widget_ignore_user" name="widget_ignore_user" rows="5" cols="15"><?php echo implode("\n", $options['widget_ignored_users']); ?></textarea>
                        </td>
                        <td>
                            <p>
                                <?php _e('If you want to ignore certain users from the recent visitors
                                list please put one user ID per line in this text box.', 'lbakut'); ?>
                                <br />
                                <?php
                                wp_dropdown_users();
                                ?>
                                <span class="button-secondary" onclick="insert_user_id_from_dropdown()"><?php _e('Add user to list', 'lbakut'); ?></span>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="widget_no_to_display">
                                <b><?php _e('Rows to display', 'lbakut'); ?></b>
                            </label>
                        </td>
                        <td>
                            <input type="text" id="widget_no_to_display" name="widget_no_to_display" value="<?php echo $options['widget_no_to_display']; ?>" />
                        </td>
                        <td>
                            <p>
                                <?php _e('Choose how many rows to display on the dashboard widget.', 'lbakut'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b><?php _e('Submit', 'lbakut'); ?></b>
                        </td>
                        <td>
                            <input type="submit" name="widget_submit" class="button-primary" value="Submit" />
                        </td>
                        <td>
                            <p><?php _e('Submit the form and change the widget options.', 'lbakut'); ?></p>
                        </td>
                    </tr>
                    <thead>
                    <th><?php _e('Setting', 'lbakut'); ?></th>
                    <th></th>
                    <th><?php _e('Description', 'lbakut'); ?></th>
                    </thead>
                </table>
            </form>
        </div>
    </div>
</div>
<div id="poststuff" class="ui-sortable meta-box-sortable">
    <div class="postbox" id="tracker_settings">
        <h3><?php _e('Tracker Settings', 'lbakut'); ?></h3>
        <div class="inside">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>?page=lbakut"
                  method="post" name="lbakut_tracker_form">
                <?php wp_nonce_field('lbakut_nonce'); ?>
                <table class="widefat">
                    <thead>
                    <th><?php _e('Setting', 'lbakut'); ?></th>
                    <th></th>
                    <th><?php _e('Description', 'lbakut'); ?></th>
                    </thead>
                    <?php echo lbakut_print_options('track_'); ?>
                    <tr>
                        <td>
                            <b><?php _e('Submit', 'lbakut'); ?></b>
                        </td>
                        <td>
                            <input type="submit" name="track_submit" class="button-primary" value="Submit" />
                        </td>
                        <td>
                            <p><?php _e('Submit the form and change the tracking options.', 'lbakut'); ?></p>
                        </td>
                    </tr>
                    <thead>
                    <th><?php _e('Setting', 'lbakut'); ?></th>
                    <th></th>
                    <th><?php _e('Description', 'lbakut'); ?></th>
                    </thead>
                </table>
            </form>
        </div>
    </div>
</div>
<div id="poststuff" class="ui-sortable meta-box-sortable">
    <div class="postbox" id="search_settings">
        <h3><?php _e('Search Settings', 'lbakut'); ?></h3>
        <div class="inside">
            <p>
                <?php _e('The following options determine the style and behaviour of
                the search page.', 'lbakut'); ?>
            </p>
            <table class="widefat">
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>?page=lbakut"
                      method="post">
                    <?php wp_nonce_field('lbakut_nonce'); ?>
                    <thead>
                    <th><?php _e('Setting', 'lbakut'); ?></th>
                    <th></th>
                    <th><?php _e('Description', 'lbakut'); ?></th>
                    </thead>
                    <tr>
                        <td>
                            <b><?php _e('Results to show per page', 'lbakut'); ?></b>
                        </td>
                        <td>
                            <input type="text" name="search_no_to_display" value="<?php echo $options['search_no_to_display']; ?>" />
                        </td>
                        <td>
                            <p><?php _e('The number of results to show on each page of search results.', 'lbakut'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b><?php _e('Submit', 'lbakut'); ?></b>
                        </td>
                        <td>
                            <input type="submit" name="search_submit" class="button-primary" value="Submit" />
                        </td>
                        <td>
                            <p><?php _e('Submit the form and change the tracking options.', 'lbakut'); ?></p>
                        </td>
                    </tr>
                    <thead>
                    <th><?php _e('Setting', 'lbakut'); ?></th>
                    <th></th>
                    <th><?php _e('Description', 'lbakut'); ?></th>
                    </thead>
                </form>
            </table>
        </div>
    </div>
</div>

<div id="poststuff" class="ui-sortable meta-box-sortable">
    <div class="postbox" id="stats_settings">
        <h3><?php _e('Stats Settings', 'lbakut'); ?></h3>
        <div class="inside">
            <table class="widefat">
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>?page=lbakut" method="post">
                    <?php wp_nonce_field('lbakut_nonce'); ?>
                    <thead>
                    <th><?php _e('Setting', 'lbakut'); ?></th>
                    <th></th>
                    <th><?php _e('Description', 'lbakut'); ?></th>
                    </thead>
                    <tr>
                        <td>
                            <label for="stats_enable_shortcodes">
                                <?php _e('Enable shortcodes?', 'lbakut'); ?>
                            </label>
                        </td>
                        <td>
                            <input type="hidden" name="stats_enable_shortcodes" value="0" />
                            <input type="checkbox" name="stats_enable_shortcodes"
                                   id="stats_enable_shortcodes" value="1" <?php echo $options['stats_enable_shortcodes'] ? 'checked' : ''; ?> />
                        </td>
                        <td>
                            <?php _e('Check this to enable the parsing of stat based short codes
                            in blog posts. An explanation of this can be found in the
                            Help/FAQ section.', 'lbakut'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="stats_update_frequency">
                                <?php _e('Stats update frequency', 'lbakut'); ?>
                            </label>
                        </td>

                        <td>
                            <select name="stats_update_frequency"
                                    id="stats_update_frequency">
                                        <?php
                                        $schedules = wp_get_schedules();
                                        foreach ($schedules as $k => $v) {
                                            if ($k == $options['stats_update_frequency']) {
                                                $next = 'selected';
                                            }
                                            else {
                                                $next = '';
                                            }
                                            echo '<option value="'.$k.'" '.$next.'>'.$v['display'].'</option>';
                                        }
                                        ?>
                            </select>
                        </td>
                        <td>
                            <?php _e('How often you want the stats section to be updated.', 'lbakut'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="stats_browser_widget">
                                <?php _e('Show Browser stats on Dashboard?', 'lbakut'); ?>
                            </label>
                        </td>
                        <td>
                            <input type="hidden" name="stats_browser_widget" value="0" />
                            <input type="checkbox" name="stats_browser_widget"
                                   id="stats_browser_widget" value="1" <?php echo $options['stats_browser_widget'] ? 'checked' : ''; ?> />
                        </td>
                        <td>
                            <?php _e('Check this to enable the displaying of a dashboard widget
                            that gives you browser statistics.', 'lbakut'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="stats_os_widget">
                                <?php _e('Show OS stats on Dashboard?', 'lbakut'); ?>
                            </label>
                        </td>
                        <td>
                            <input type="hidden" name="stats_os_widget" value="0" />
                            <input type="checkbox" name="stats_os_widget"
                                   id="stats_os_widget" value="1" <?php echo $options['stats_os_widget'] ? 'checked' : ''; ?> />
                        </td>
                        <td>
                            <?php _e('Check this to enable the displaying of a dashboard widget
                            that gives you os statistics.', 'lbakut'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="stats_pageviews_widget">
                                <?php _e('Show Pageviews stats on Dashboard?', 'lbakut'); ?>
                            </label>
                        </td>
                        <td>
                            <input type="hidden" name="stats_pageviews_widget" value="0" />
                            <input type="checkbox" name="stats_pageviews_widget"
                                   id="stats_pageviews_widget" value="1" <?php echo $options['stats_pageviews_widget'] ? 'checked' : ''; ?> />
                        </td>
                        <td>
                            <?php _e('Check this to enable the displaying of a dashboard widget
                            that gives you page views statistics.', 'lbakut'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="stats_submit">
                                <?php _e('Submit', 'lbakut'); ?>
                            </label>
                        </td>
                        <td>
                            <input type="submit" name="stats_submit" class="button-primary"
                                   id="stats_submit" value="Submit" />
                        </td>
                        <td>
                            <?php _e('Click here to submit your new settings.', 'lbakut'); ?>
                        </td>
                    </tr>
                    <thead>
                    <th><?php _e('Setting', 'lbakut'); ?></th>
                    <th></th>
                    <th><?php _e('Description', 'lbakut'); ?></th>
                    </thead>
                </form>
            </table>
        </div>
    </div>
</div>
<br /><br /><br /><br />