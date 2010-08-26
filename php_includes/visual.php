<?php

/*
 * Adds the dashboard widget. Dashboard widget is created in a function called
 * lbakut_widget() later in this file.
*/
function  lbakut_dashboard_setup() {
    $options = lbakut_get_options();
    if ($options['widget_show'] == true) {
        wp_add_dashboard_widget('lbakut_widget',
                __('User Tracking', 'lbakut'), 'lbakut_widget');
    }
}

/*
 * This function creates the link to the admin settings panel. It also links
 * the lbak_add_scripts() function to the loading of the admin panel page
 * through the admin_print_scripts-% hook.
*/
function lbakut_admin_menu() {
    $page = add_submenu_page('tools.php', 'LBAK User Tracking Options',
            'User Tracking', 'manage_options', 'lbakut', 'lbakut_menu_options');
    add_action('admin_print_scripts-' . $page, 'lbakut_add_scripts');
}

/*
 * This function displays the admin panel menu. All options etc. that you want
 * to include in this plugin have to go in here.
*/
function lbakut_menu_options() {
    //Check that the user is able to view this page.
    if (!current_user_can('manage_options')) {
        wp_die( __('You do not have sufficient permissions to access this page.') );
    }

    //Declare global variables.
    global $wpdb;

    //Get lbakut options.
    $options = lbakut_get_options();

    ?>
<h1>LBAK User Tracking</h1>
<div class="wrap">
    <div id="navigation">
        <a href="?page=lbakut">Settings</a> |
        <a href="?page=lbakut&step=search">Search</a> |
        <a href="?page=lbakut&step=donate">Donate <3</a>
    </div>
    <br />
        <?php

        switch ($_GET['step']) {
            case 'donate':
                echo 'Hi, my name is <a href="http://lbak.co.uk/">Sam Rose</a>
                    and I write code in my spare time
                    around doing a Computer Science degree. While I love doing
                    what I do, it doesn\'t really pay the bills. For this reason,
                    I would greatly appreciate any and all donations. If you
                    use my plugin and think it is good then why not chip in
                    a few dollars to keep a coder going? :) Thank you.<br />
                    '.lbakut_donate_button();
            case '':
            case 'settings':

            //If the form is submitted, process it with the following.
                if ($_POST['widget_submit'] == 'Submit') {

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

                    $updated = '<div class="updated">LBAK User Tracking Widget Options Updated!</div>';
                }
                else if ($_POST['track_submit'] == 'Submit') {
                    foreach ($_POST as $k => $v) {
                        if (preg_match('/track_/',$k)) {
                            $options[$k] = $wpdb->escape($_POST[$k]);
                        }
                    }
                    $options['delete_on_uninstall'] = $wpdb->escape($_POST['delete_on_uninstall']);
                    lbakut_update_options($options);
                    $updated = '<div class="updated">LBAK User Tracking Tracker Options Updated!</div>';
                }
                else if ($_POST['search_submit'] == 'Submit') {
                    foreach ($_POST as $k => $v) {
                        if (preg_match('/search_/',$k)) {
                            $options[$k] = $wpdb->escape($_POST[$k]);
                        }
                    }
                    lbakut_update_options($options);
                    $updated = '<div class="updated">LBAK User Tracking Search Options Updated!</div>';
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
        <div class="postbox" id="widget_settings">
            <h3><?php _e('LBAK User Tracking: Widget Settings', 'lbakut'); ?></h3>
            <div class="inside">
                <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" name="lbakut_widget_form">
                                <?php wp_nonce_field('update-options'); ?>
                    <p>
                        The following options edit the display and behaviour of the
                        admin dashboard widget for the LBAK User Tracking plugin.
                        <b>Note that none of these options affect what data gets tracked
                            by the plugin, settings to edit what gets tracked are located
                            further down the page.</b>
                    </p>
                    <table class="widefat">
                        <thead>
                        <th>Setting</th>
                        <th>Options</th>
                        <th>Description</th>
                        </thead>
                        <tr>
                            <td>
                                <label for="widget_ignore_admin">
                                    <b><?php _e('Ignore admin?', 'lbakut'); ?></b>
                                </label>
                            </td>
                            <td>
                                <input type="checkbox" id="widget_ignore_admin"
                                       name="widget_ignore_admin" value="1" <?php echo $widget_ignore_admin_checked; ?> />
                            </td>
                            <td>
                                <p>
                                    Check to ignore admin users in the user tracking widget.
                                </p>
                            </td>
                        </tr>
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
                                    If you want to ignore certain IP addresses from the recent visitors
                                    list please put one per line in this text box.
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
                                    If you want to ignore certain users from the recent visitors
                                    list please put one user ID per line in this text box.
                                    <br />
                                                <?php
                                                wp_dropdown_users();
                                                ?>
                                    <span class="button-secondary" onclick="insert_user_id_from_dropdown()">Add user to list</span>
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
                                    Choose how many rows to display on the dashboard widget.
                                </p>
                            </td>
                        </tr>
                                    <?php echo lbakut_print_options('widget_show_', $options); ?>
                        <tr>
                            <td>
                                <b>Submit</b>
                            </td>
                            <td>
                                <input type="submit" name="widget_submit" class="button-primary" value="Submit" />
                            </td>
                            <td>
                                <p>Submit the form and change the widget options.</p>
                            </td>
                        </tr>
                        <thead>
                        <th>Setting</th>
                        <th>Options</th>
                        <th>Description</th>
                        </thead>
                    </table>
                </form>
            </div>
        </div>
    </div>
    <div id="poststuff" class="ui-sortable meta-box-sortable">
        <div class="postbox" id="tracker_settings">
            <h3><?php _e('LBAK User Tracking: Tracker Settings', 'lbakut'); ?></h3>
            <div class="inside">
                <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" name="lbakut_tracker_form">
                                <?php wp_nonce_field('update-options'); ?>
                    <p>
                        Below are a variety of options that help you tailor what you
                        track to your needs. If you are uncertain of what something is
                        or does it is advised that you stick to the default setting.<br /><br />
                        It is worth noting that if you turn any of the tracking options off,
                        that bit of information will not be tracked at all and there will be
                        no way of filling in the gaps if you decide to track it again
                        in the future.
                    </p>
                    <table class="widefat">
                        <thead>
                        <th>Setting</th>
                        <th>Options</th>
                        <th>Description</th>
                        </thead>
                        <tr>
                            <td>
                                <b>Delete data on uninstall?</b>
                            </td>
                            <td>
                                <input type="checkbox" id="delete_on_uninstall"
                                       name="delete_on_uninstall" value="1" <?php echo $delete_on_uninstall_checked; ?> />
                            </td>
                            <td>
                                <p>
                                    If this is checked, the lbakut_activity_log table
                                    will be deleted from your database when you uninstall
                                    this plugin. This means that you will lose all of the
                                    information you have collected while using this plugin.
                                    You will also lose all of your preferences.
                                </p>
                            </td>
                        </tr>
                                    <?php echo lbakut_print_options('track_', $options); ?>
                        <tr>
                            <td>
                                <b>Submit</b>
                            </td>
                            <td>
                                <input type="submit" name="track_submit" class="button-primary" value="Submit" />
                            </td>
                            <td>
                                <p>Submit the form and change the tracking options.</p>
                            </td>
                        </tr>
                        <thead>
                        <th>Setting</th>
                        <th>Options</th>
                        <th>Description</th>
                        </thead>
                    </table>
                </form>
            </div>
        </div>
    </div>
    <div id="poststuff" class="ui-sortable meta-box-sortable">
        <div class="postbox" id="search_settings">
            <h3><?php _e('LBAK User Tracking: Search Settings', 'lbakut'); ?></h3>
            <div class="inside">
                <p>
                    The following options determine the style and behaviour of
                    the search page.
                </p>
                <table class="widefat">
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>?page=lbakut#search_settings" method="post">
                        <thead>
                        <th>Setting</th>
                        <th>Options</th>
                        <th>Description</th>
                        </thead>
                            <?php echo lbakut_print_options('search_show_', $options); ?>
                        <tr>
                            <td>
                                <b>Results to show per page</b>
                            </td>
                            <td>
                                <input type="text" name="search_no_to_display" value="<?php echo $options['search_no_to_display']; ?>" />
                            </td>
                            <td>
                                <p>The number of results to show on each page of search results.</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Submit</b>
                            </td>
                            <td>
                                <input type="submit" name="search_submit" class="button-primary" value="Submit" />
                            </td>
                            <td>
                                <p>Submit the form and change the tracking options.</p>
                            </td>
                        </tr>
                        <thead>
                        <th>Setting</th>
                        <th>Options</th>
                        <th>Description</th>
                        </thead>
                    </form>
                </table>
            </div>
        </div>
    </div>
    <br /><br /><br /><br />
                <?php
                break;

            case 'search':
                ?>
    <div id="poststuff" class="ui-sortable meta-box-sortable">
        <div class="postbox">
            <h3><?php _e('What are you looking for?', 'lbakut'); ?></h3>
            <div class="inside">
                <p>
                    Below are the fields that you are search in. User comma
                    separated values to search for multiple values. Unfortunately
                    searching on the Browser and OS field is not viable due to the
                    way that they are worked out. A solution to this may be worked
                    out in future but currently it is not an option.
                </p>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
                    <input type="hidden" name="page" value="lbakut" />
                    <input type="hidden" name="step" value="search" />
                    <table class="widefat">
                        <tr>
                            <td>
                                <label for="display_name">
                                                <?php echo __('Display Name', 'lbakut'); ?>
                                </label>
                            </td>
                            <td>
                                            <?php echo lbakut_is_isnt_box('display_name_is'); ?>
                                <input type="text" name="display_name"
                                       id="display_name"
                                       value="<?php echo $_GET['display_name']; ?>" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="user_id">
                                                <?php echo __('User ID', 'lbakut'); ?>
                                </label>
                            </td>
                            <td>
                                            <?php echo lbakut_is_isnt_box('user_id_is'); ?>
                                <input type="text" name="user_id"
                                       id="user_id"
                                       value="<?php echo $_GET['user_id']; ?>"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="user_level">
                                                <?php echo __('User Level', 'lbakut'); ?>
                                </label>
                            </td>
                            <td>
                                            <?php echo lbakut_is_isnt_box('user_level_is'); ?>
                                <input type="text" name="user_level"
                                       id="user_level"
                                       value="<?php echo $_GET['user_level']; ?>"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="ip_address">
                                                <?php echo __('IP Address', 'lbakut'); ?>
                                </label>
                            </td>
                            <td>
                                            <?php echo lbakut_is_isnt_box('ip_address_is'); ?>
                                <input type="text" name="ip_address"
                                       id="ip_address"
                                       value="<?php echo $_GET['ip_address']; ?>"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="real_ip_address">
                                                <?php echo __('Real IP Address', 'lbakut'); ?>
                                </label>
                            </td>
                            <td>
                                            <?php echo lbakut_is_isnt_box('real_ip_address_is'); ?>
                                <input type="text" name="real_ip_address"
                                       id="real_ip_address"
                                       value="<?php echo $_GET['real_ip_address']; ?>"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="script_name">
                                                <?php echo __('Page', 'lbakut'); ?>
                                </label>
                            </td>
                            <td>
                                            <?php echo lbakut_is_isnt_box('script_name_is'); ?>
                                <input type="text" name="script_name"
                                       id="script_name"
                                       value="<?php echo $_GET['script_name']; ?>"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                            <?php echo __('Between', 'lbakut'); ?>
                            </td>
                            <td>
                                <input type="text" name="time_first"
                                       id="time_first" size="5"
                                       value="<?php echo $_GET['time_first']; ?>"/>
                                                   <?php echo lbakut_time_ago_select_box('time_first_multiplier'); ?>
                                                   <?php echo __(' and ', 'lbakut'); ?>
                                <input type="text" name="time_second"
                                       id="time_second" size="5"
                                       value="<?php echo $_GET['time_second']; ?>"/>
                                                   <?php echo lbakut_time_ago_select_box('time_second_multiplier'); ?>
                                                   <?php echo __('ago.', 'lbakut'); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>

                            </td>
                            <td>
                                <input type="submit" value="Search" class="button-primary" />
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
    <div id="poststuff" class="ui-sortable meta-box-sortable">
        <div class="postbox">
            <h3><?php _e('Search results...', 'lbakut'); ?></h3>
            <div class="inside">
                <p>
                    To choose what columns appear on this table please
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=lbakut#search_settings">click here</a>.
                </p>
                            <?php

                            if (!empty($_GET['time_first']) && !empty($_GET['time_second'])) {
                                $time = 'AND `time` BETWEEN '.(time()-(intval($_GET['time_second']) *
                                                        intval($_GET['time_second_multiplier']))).' AND
                                '.(time()-(intval($_GET['time_first']) *
                                                        intval($_GET['time_first_multiplier'])));
                            }
                            else {
                                $time = 'AND 1';
                            }

                            $display_name = lbakut_search_var_prepare('display_name', 'string');
                            $user_id = lbakut_search_var_prepare('user_id', 'int');
                            $user_level = lbakut_search_var_prepare('user_level', 'int');
                            $ip_address = lbakut_search_var_prepare('ip_address', 'string');
                            $real_ip_address = lbakut_search_var_prepare('real_ip_address', 'string');
                            $script_name = lbakut_search_var_prepare('script_name', 'string');

                            $query = "
                                WHERE 1
                                    $time
                                    $display_name
                                    $user_id
                                    $user_level
                                    $ip_address
                                    $real_ip_address
                                    $script_name";

                            //AN EXPLAIN SELECT QUERY FOR INDEX TESTING.
                            /*
                            $explain = $wpdb->get_row('EXPLAIN '.$query, ARRAY_A);

                            echo '<table class="widefat">';
                            echo '<thead>';
                            foreach($explain as $k => $v) {
                                echo '<th>'.$k.'</th>';
                            }
                            echo '</thead>';
                            echo '<tr>';
                            foreach($explain as $k => $v) {
                                echo '<td>'.$v.'</td>';
                            }
                            echo '</tr>';
                            echo '</table>';
                            echo '<br />';
                            */

                            echo lbakut_print_table($query, 'search', $options);

                            break;

                        default:
                            break;
                    }
                    //close div class wrap
                    ?>
            </div>
        </div>
    </div>
</div>
    <?php
}

function lbakut_widget() {

    //Declare global variables.
    global $wpdb;

    //Get lbakut options.
    $options = lbakut_get_options();

    //Create comma separated lists for ip addresses and user ids for
    //database use.
    $ips = "'" . implode("', '", array_filter($options['widget_ignored_ips'])) . "'";
    $user_ids = implode(", ", array_filter($options['widget_ignored_users'], 'intval'));

    //If the ip or user id lists are empty give them value that won't upset the query.
    if ($ips == "''" || $ips == "'Add ignored IP addresses in here.'") {
        $ips = "'0.0.0.0'";
    }
    if ($user_ids == '' || $user_ids == "Add ignored user IDs in here.") {
        $user_ids = '-1';
    }

    //Get the part of the query that will ignore the admin user.
    if ($options['widget_ignore_admin']) {
        $ignore_admin = 'AND `user_level`!=10';
    }
    else {
        $ignore_admin = '';
    }

    //get the number of results to display.
    if (!isset($options['widget_no_to_display'])) {
        $options['widget_no_to_display'] = 10;
    }
    else {
        $options['widget_no_to_display'] = intval($options['widget_no_to_display']);
    }

    //Get rows from the database based on the options set in the admin panel
    $query = 'WHERE `ip_address` NOT IN ('.$ips.')
            '.$ignore_admin.'
            AND `user_id` NOT IN ('.$user_ids.')';

    echo '<p>To edit the style and behaviour of this widget,
        <a href="tools.php?page=lbakut">click here</a> or to search these
        results, <a href="tools.php?page=lbakut&step=search">click here</a>.</p>';
    
    echo lbakut_print_table($query, 'widget', $options);
}

/*
 * Standard format for displaying time and dates in lbakut.
*/
function lbakut_time_format($time) {
    return strftime("%l%P, %a %e %b, %Y", $time);
}

/*
 * A 'time ago' function. Displays how long ago a unix timestamp occured in
 * relation to time() (now). If it was over a week ago, the function just
 * returns the time and date formatted by lbakut_time_format().
*/
function lbakut_time_ago_format($time) {
    $temp = time() - $time;
    if ($temp > (60*60*24*7)) {
        return lbakut_time_format($time);
    }

    $days = floor($temp / 86400);
    $temp -= ($days * 86400);
    $hours = floor($temp / 3600);
    $temp -= ($hours * 3600);
    $mins = floor($temp / 60);
    $temp -= ($mins * 60);
    $secs = $temp;

    if ($days) {
        return $days . "d ago";
    }
    if ($hours) {
        return $hours . "h ago";
    }
    if ($mins) {
        return $mins . "m ago";
    }
    return $secs . "s ago";

    /*
    if ($days) {
        $length .= $days . "d ";
    }
    if ($hours) {
        $length .= $hours . "h ";
    }
    if ($mins) {
        $length .= $mins . "m ";
    }
    return $length .= $secs . "s ago";
    */
}

/*
 * Function used to translate options in the database
 * associative array (list available in housekeeping.php) into a human readable
 * format.
*/
function lbakut_option_translate($k) {
    switch($k) {

        //TRACKER SETTINGS
        case 'track_ip': return 'IP Address';
        case 'track_real_ip': return 'Real IP Address';
        case 'track_referrer': return 'Referrer';
        case 'track_time': return 'Time';
        case 'track_user_id': return 'User ID';
        case 'track_user_level': return 'User Level';
        case 'track_display_name': return 'Display Name';
        case 'track_user_agent': return 'User Agent';
        case 'track_script_name': return 'Script Name';
        case 'track_query_string': return 'GET vars';
        case 'track_post_vars': return 'POST vars';
        case 'track_cookies': return 'Cookies';

        //WIDGET SETTINGS
        case 'widget_show': return 'widget';
        case 'widget_show_referrer': return 'Referrer';
        case 'widget_show_name': return 'Name';
        case 'widget_show_ip': return 'IP Address';
        case 'widget_show_real_ip': return 'Real IP Address';
        case 'widget_show_page': return 'Page';
        case 'widget_show_browser': return 'Browser';
        case 'widget_show_os': return 'OS';
        case 'widget_show_time': return 'Time Ago';
        case 'widget_show_get': return 'GET vars';
        case 'widget_show_post': return 'POST vars';
        case 'widget_show_cookies': return 'Cookies';

        //SEARCH SETTINGS
        case 'search_show_ip': return 'IP Address';
        case 'search_show_real_ip': return 'Real IP Address';
        case 'search_show_referrer': return 'Referrer';
        case 'search_show_time': return 'Time';
        case 'search_show_user_id': return 'User ID';
        case 'search_show_user_level': return 'User Level';
        case 'search_show_name': return 'Display Name';
        case 'search_show_browser': return 'Browser';
        case 'search_show_os': return 'OS';
        case 'search_show_page': return 'Script Name';
        case 'search_show_get': return 'GET vars';
        case 'search_show_post': return 'POST vars';
        case 'search_show_cookies': return 'Cookies';

        //DEFAULT
        default: return $k;
    }
}

/*
 * Settings page descriptions based on the name of the options in the database
 * associative array (list available in housekeeping.php).
*/
function lbakut_option_description($k) {
    if (preg_match('/track_/', $k)) {
        $prefix = 'Check to track '.lbakut_option_translate($k).'.';
    }
    else if (preg_match('/search_/', $k)) {
        $prefix = 'Check to show '.lbakut_option_translate($k).'.';
    }
    else if (preg_match('/widget_show_/', $k)) {
        $prefix = 'Check to show '.lbakut_option_translate($k).'.';
    }
    else {
        $prefix = 'Check to track '.lbakut_option_translate($k).'.';
    }
    switch($k) {

        //TRACKER SETTINGS
        case 'track_real_ip': return $prefix.'.
            This attempts to get a user\'s real IP address if they are browsing
            through a proxy, however, the success rate is very low. Most good
            proxies will mask the IP address properly.';
        case 'track_post_vars': return $prefix.'.
            POST variables are variables sent to web servers in forms and such.';
        case 'track_user_agent': return $prefix.'.
            <b>NOTE: This is required for browser and OS detection.</b>';

        //WIDGET SETTINGS
        case 'widget_show_real_ip': return $prefix.'
                    column on the admin panel widget. This field will only show an
                    ip address if the user has viewed your site through some sort
                    of proxy (perhaps even without their knowledge). Not 100% reliable.';
        case 'widget_show_referrer': return $prefix.'
                    column on the admin panel widget. There are a lot of ways to modify
                    the referrer variable so this field may not always be trustable.';
        case 'widget_show': return $prefix.'
                    on the admin panel widget.';
        default: return $prefix.'
                    column on the admin panel widget.';

        //SEARCH SETTINGS

        //DEFAULT
        default: return $prefix;
    }
}

/*
 * This function replaces all of the & symbols in a query string with line
 * breaks and bolds the variable name. Vastly increases readability.
*/
function lbakut_query_string_formatter($string) {
    //Decode html special chars (&, <, >, and quotes).
    $string = htmlspecialchars_decode($string);
    //Decode the URL chars (the ones with a % in front of them)
    $string = urldecode($string);
    //Corrects what seems to be a bug in how query strings are handled with
    //html encoding.
    $string = str_replace('amp;', '', $string);
    return preg_replace('/(.*?)=(.*?)(?:&|$)/i', '<b>$1</b>=$2<br />', $string);
}

/*
 * Function to show all of the options that start with $prefix correctly
 * formatted as table rows and part of a form. Used in the admin panel page to
 * change the plugin settings.
*/
function lbakut_print_options($prefix, $options = null) {
    if ($options == null) {
        $options = lbakut_get_options();
    }
    $return = '';
    foreach ($options as $k => $v) {
        if (preg_match('/'.$prefix.'/',$k)) {

            if ($options[$k] == true) {
                $checked = 'checked';
            }
            else {
                $checked = '';
            }

            if ($prefix == 'track_') {
                $pre = 'Track';
            }
            else if ($prefix == 'widget_show_' || $prefix == 'search_') {
                $pre = 'Show';
            }
            else {
                $pre = 'Enable';
            }

            $return .= '
            <tr>
                <td>
                    <label for="'.$k.'">
                        '.__($pre.' '.lbakut_option_translate($k).'?', 'lbakut').'
                    </label>
                </td>
                <td>
                    <input type="hidden" name="'.$k.'" value="0" />
                    <input type="checkbox" id="'.$k.'"
                    name="'.$k.'" value="1" '.$checked.' />
                </td>
                <td>
                    <p>'.lbakut_option_description($k).'</p>
                </td>
            </tr>';
        }
    }

    return $return;
}

/*
 * I decided to put this into a function to make it easier to keep the box
 * state through page reloads. It prints a dropdown box of 'times ago' with
 * their corresponding value in seconds.
*/
function lbakut_time_ago_select_box($get_var) {
    $times = array(
            'Seconds' => 1,
            'Minutes' => 60,
            'Hours' => 3600,
            'Days' => 86400,
            'Weeks' => 604800,
            'Months' => 2628000,
            'Years' => 31536000
    );

    $return = '<select name="'.$get_var.'">';
    foreach($times as $k=>$v) {
        if ($_GET[$get_var] == $v) {
            $selected = 'selected';
        }
        else {
            $selected = '';
        }
        $return .= '<option value="'.$v.'" '.$selected.'>'.$k.'</option>';
    }
    $return .= '</select>';
    return $return;
}

/*
 * This is in a function to make it easier for the box to keep its state
 * through page reloads. For use with the is/isn't dropdowns on the search page.
*/
function lbakut_is_isnt_box($get_var) {
    global $_GET;
    if (stripslashes($_GET[$get_var]) != "isn't") {
        $is = 'selected';
        $isnt = '';
    }
    else {
        $is = '';
        $isnt = 'selected';
    }

    return "<select name='$get_var'>
            <option value='is' $is>is</option>
            <option value=\"isn't\" $isnt>isn't</option>
        </select>";
}

/*
 * This function takes a list of comma separated values (cvs's) and turns them
 * into part of an SQL query for use in the search page for this plugin.
*/
function lbakut_search_var_prepare($get_var, $type) {

    global $wpdb;

    if (!empty($_GET[$get_var])) {
        //Create array out of comma separated values (csv).
        $csv_array = explode(",", $_GET[$get_var]);

        //Get the correct operator for the SQL query.
        if (stripslashes($_GET[$get_var.'_is']) != "isn't") {
            $operator = '=';
        }
        else {
            $operator = '!=';
        }

        //If there is more than 1 value in the array.
        if (sizeof($csv_array) > 1) {
            $return = 'AND (';
            foreach ($csv_array as $value) {
                if ($operator == '=') {
                    $andor = ' OR ';
                }
                else {
                    $andor = ' AND ';
                }

                //Prepare the value for database entry based on $type.
                if ($type == 'string') {
                    $prepared = $wpdb->escape(trim($value));
                }
                else {
                    $prepared = intval($value);
                }
                $return .= '`'.$get_var.'`'.$operator."
                                            '".$prepared."'".$andor;
            }
            $return = rtrim($return, $andor).')';
        }
        else {

            //Prepare the value for database entry based on $type.
            if ($type == 'string') {
                $prepared = $wpdb->escape(trim($_GET[$get_var]));
            }
            else {
                $prepared = intval($_GET[$get_var]);
            }
            $return = 'AND `'.$get_var.'`'.$operator."
                                        '".$prepared."'";
        }
    }
    else {
        $return = 'AND 1';
    }
    return $return;
}

/*
 * This is the function that prints the table of data out. The two required
 * parameters are $where_clause, the where clause of the query string to get
 * data from the database and $place, which is the place of
 * the table. This is for sifting through options to find out what columns to
 * show, that is all.
 */
function lbakut_print_table($where_clause, $place, $options = null, $brows = null) {
    global $wpdb;

    if ($options == null) {
        $options = lbakut_get_options();
    }
    if ($brows == null) {
        $brows = lbakut_get_browscap();
    }

    $no_of_results = $wpdb->get_var('SELECT COUNT(`id`) as `count` FROM
        '.$options['main_table_name'].' WHERE `id` IN (SELECT `id` FROM 
            '.$options['main_table_name'].' '.$where_clause.')');

    $rows = $wpdb->get_results('SELECT * FROM `'.$options['main_table_name'].'`
        '.$where_clause.'
            ORDER BY `time` DESC 
            LIMIT '.$options[$place.'_no_to_display'].'
            OFFSET '.((lbakut_page_var())-1)*$options[$place.'_no_to_display']);

    $return .= lbakut_do_pagination($no_of_results, $place, $options).'<br />';
    $return .= '<table class="widefat">';
    $return .= '<thead>';
    if ($options[$place.'_show_time'] == true)
        $return .= '<th>Time</th>';
    if ($options[$place.'_show_name'] == true)
        $return .= '<th>Name</th>';
    if ($options[$place.'_show_user_id'] == true)
        $return .= '<th>User ID</th>';
    if ($options[$place.'_show_user_level'] == true)
        $return .= '<th>User Level</th>';
    if ($options[$place.'_show_ip'] == true)
        $return .= '<th>IP</th>';
    if ($options[$place.'_show_real_ip'] == true)
        $return .= '<th>Real IP</th>';
    if ($options[$place.'_show_browser'] == true)
        $return .= '<th>Browser</th>';
    if ($options[$place.'_show_os'] == true)
        $return .= '<th>OS</th>';
    if ($options[$place.'_show_referrer'] == true)
        $return .= '<th>Referrer</th>';
    if ($options[$place.'_show_page'] == true)
        $return .= '<th>Page</th>';
    if ($options[$place.'_show_get'] == true)
        $return .= '<th>GET vars</th>';
    if ($options[$place.'_show_post'] == true)
        $return .= '<th>POST vars</th>';
    if ($options[$place.'_show_cookies'] == true)
        $return .= '<th>Cookies</th>';
    $return .= '</thead>';
    foreach ($rows as $row) {
        if ($row->user_agent) {
            $browscap = lbakut_browser_info($row->user_agent, $brows);
        }
        else {
            $browscap = null;
        }

        if ($options[$place.'_show_referrer'] == true)
            $referrer = parse_url($row->referrer);

        $return .= '<tr>';

        if ($options[$place.'_show_time'] == true)
            $return .= '<td>'.lbakut_time_ago_format($row->time).'</td>';
        if ($options[$place.'_show_name'] == true)
            $return .= '<td><a href="tools.php?page=lbakut&step=search&user_id='.$row->user_id.'"
                title="Search for activity from the user '.$row->display_name.'">
                    '.$row->display_name.'</a></td>';
        if ($options[$place.'_show_user_id'] == true)
            $return .= '<td><a href="tools.php?page=lbakut&step=search&user_id='.$row->user_id.'"
                title="Search for activity from the user '.$row->display_name.'">
                    '.$row->user_id.'</td>';
        if ($options[$place.'_show_user_level'] == true)
            $return .= '<td>'.$row->user_level.'</td>';
        if ($options[$place.'_show_ip'] == true)
            $return .= '<td><a href="tools.php?page=lbakut&step=search&ip_address='.$row->ip_address.'"
                title="Search for activity for the ip '.$row->ip_address.'">'.$row->ip_address.'</a></td>';
        if ($options[$place.'_show_real_ip'] == true)
            $return .= '<td><a href="tools.php?page=lbakut&step=search&real_ip_address='.$row->real_ip_address.'"
                title="Search for activity for the ip '.$row->real_ip_address.'">
                    '.$row->real_ip_address.'</a></td>';
        if ($options[$place.'_show_browser'] == true)
            $return .= '<td>'.$browscap['parent'].'</td>';
        if ($options[$place.'_show_os'] == true)
            $return .= '<td>'.$browscap['platform'].'</td>';
        if ($options[$place.'_show_referrer'] == true)
            $return .= $row->referrer ? '<td>'.$referrer['scheme'].'://'.$referrer['host'].$referrer['path'].'</td>' : '<td></td>';
        if ($options[$place.'_show_page'] == true)
            $return .= '<td>'.$row->script_name.'</td>';
        if ($options[$place.'_show_get'] == true)
            $return .= '<td>'.lbakut_query_string_formatter($row->query_string).'</td>';
        if ($options[$place.'_show_post'] == true)
            $return .= '<td>'.lbakut_query_string_formatter($row->post_vars).'</td>';
        if ($options[$place.'_show_cookies'] == true)
            $return .= '<td>'.lbakut_query_string_formatter($row->cookies).'</td>';

        $return .= '</tr>';
    }
    $return .= '</table>';

    $return .= '<br />'.lbakut_do_pagination($no_of_results, $place, $options);

    return $return;
}

/*
 * Parsing the browscap ini file with backwards compatibility.
 */
function lbakut_get_browscap() {
    if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
        $brows = parse_ini_file("php_browscap.ini", true, INI_SCANNER_RAW);
    } else {
        $brows = parse_ini_file("php_browscap.ini", true);
    }
    return $brows;
}

/*
 * Get the variable used in pagination.
 */
function lbakut_page_var() {
    if (!isset($_GET['lbakut_page'])) {
        return 1;
    }
    else {
        return $_GET['lbakut_page'];
    }
}

/*
 * Generates the pagination based on the amount of results and the place of
 * the table. $place is used for deciding how many rows get shown, thus how
 * many pages there are.
 */
function lbakut_do_pagination($no_of_results, $place, $options = null) {
    if ($options == null) {
        $options = lbakut_get_options();
    }

    $pages = ceil($no_of_results/$options[$place.'_no_to_display']);

    if ($pages < 2) {
        return null;
    }

    $start = max(1, lbakut_page_var() - 3);
    $end = min($pages, lbakut_page_var() + 3);

    $result .= '<div class="lbakut_pages">';
    $result .= 'Showing results '.((lbakut_page_var()-1)*$options[$place.'_no_to_display']+1).'
        - '.(lbakut_page_var()*$options[$place.'_no_to_display']).' of '.$no_of_results.'<br /><br />';

    $uri = preg_replace('/&lbakut_page=[0-9]+/', '', $_SERVER['QUERY_STRING']);

    if ($start != 1) {
        if ($uri == '') {
            $page_uri = '?lbakut_page=1';
        }
        else {
            $page_uri = '?'.$uri.'&lbakut_page=1';
        }
        $result .= '<span class="lbakut_page"><a href="'.$page_uri.'">&nbsp;1&nbsp;</a></span> ... ';
    }

    for ($page = $start; $page <= $end; $page++) {
        if ($uri == '') {
            $page_uri = '?lbakut_page='.$page;
        }
        else {
            $page_uri = '?'.$uri.'&lbakut_page='.$page;
        }

        if ($page == lbakut_page_var()) {
            $result .= '<span class="lbakut_page_selected"><a href="'.$page_uri.'">&nbsp;'.$page.'&nbsp;</a></span>';
        }
        else {
            $result .= '<span class="lbakut_page"><a href="'.$page_uri.'">&nbsp;'.$page.'&nbsp;</a></span>';
        }
    }

    if ($end != $pages) {
        if ($uri == '') {
            $page_uri = '?lbakut_page='.$pages;
        }
        else {
            $page_uri = '?'.$uri.'&lbakut_page='.$pages;
        }
        $result .= ' ... <span class="lbakut_page"><a href="'.$page_uri.'">&nbsp;'.$pages.'&nbsp;</a></span>';
    }

    $result .= '</div>';

    return $result;
}

function lbakut_donate_button() {
    return '<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_donations">
<input type="hidden" name="business" value="samwho@lbak.co.uk">
<input type="hidden" name="lc" value="GB">
<input type="hidden" name="item_name" value="Sam Rose Web Development">
<input type="hidden" name="no_note" value="0">
<input type="hidden" name="currency_code" value="USD">
<input type="hidden" name="bn" value="PP-DonationsBF:btn_donate_SM.gif:NonHostedGuest">
<input type="image" src="https://www.paypal.com/en_GB/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online.">
<img alt="" border="0" src="https://www.paypal.com/en_GB/i/scr/pixel.gif" width="1" height="1">
</form>
';

}

?>