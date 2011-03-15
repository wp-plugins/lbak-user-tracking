<?php

/*
 * Adds the dashboard widget. Dashboard widget is created in a function called
 * lbakut_widget() later in this file.
 */

function lbakut_dashboard_setup() {
    if (current_user_can('manage_options')) {
        $options = lbakut_get_options();
        if ($options['widget_show'] == true) {
            wp_add_dashboard_widget('lbakut_widget',
                    __('User Tracking', 'lbakut'), 'lbakut_widget');
        }
        if ($options['stats_browser_widget'] == true) {
            wp_add_dashboard_widget('lbakut_browser_widget',
                    __('Browser Stats', 'lbakut'), 'lbakut_browser_widget');
        }
        if ($options['stats_os_widget'] == true) {
            wp_add_dashboard_widget('lbakut_os_widget',
                    __('OS Stats', 'lbakut'), 'lbakut_os_widget');
        }
        if ($options['stats_pageviews_widget'] == true) {
            wp_add_dashboard_widget('lbakut_pageviews_widget',
                    __('Page View Stats', 'lbakut'), 'lbakut_pageviews_widget');
        }
    }
}

/*
 * Stats widgets functions.
 */

function lbakut_browser_widget() {
    $stat = 'browser';
    echo '<p>This is a pie chart generated by the LBAK User Tracking plugin
        using the Google Charts API to give you a breakdown of what browsers
        your users are using. Please note that this chart is based on page
        clicks, not unique visitors. To hide this widget, <a href="tools.php?page=lbakut#stats_settings">click here</a>.
        To view the stats page, <a href="tools.php?page=lbakut&step=stats">click here</a>.</p>';
    echo '<br />';
    echo '<img src="' . lbakut_get_chart(lbakut_chart_type($stat)) . '" />';
}

function lbakut_os_widget() {
    $stat = 'os';
    echo '<p>This is a pie chart generated by the LBAK User Tracking plugin
        using the Google Charts API to give you a breakdown of what operating systems
        your users are using. Please note that this chart is based on page
        clicks, not unique visitors. To hide this widget, <a href="tools.php?page=lbakut#stats_settings">click here</a>.
        To view the stats page, <a href="tools.php?page=lbakut&step=stats">click here</a>.</p>';
    echo '<br />';
    echo '<img src="' . lbakut_get_chart(lbakut_chart_type($stat)) . '" />';
}

function lbakut_pageviews_widget() {
    $stat = 'pageviews';
    echo '<p>This is a pie chart generated by the LBAK User Tracking plugin
        using the Google Charts API to give you a breakdown of what pages
        your users are viewing. Please note that this chart is based on page
        clicks, not unique visitors. To hide this widget, <a href="tools.php?page=lbakut#stats_settings">click here</a>.
        To view the stats page, <a href="tools.php?page=lbakut&step=stats">click here</a>.</p>';
    echo '<br />';
    echo '<img src="' . lbakut_get_chart(lbakut_chart_type($stat)) . '" />';
}

/*
 * This function creates the link to the admin settings panel. It also links
 * the lbak_add_scripts() function to the loading of the admin panel page
 * through the admin_print_scripts-% hook.
 */

function lbakut_admin_menu() {
    $page = add_submenu_page('tools.php', 'LBAK User Tracking Options',
                    'User Tracking', 'manage_options', 'lbakut', 'lbakut_menu_options');
    add_action('admin_print_scripts-'.$page, 'lbakut_add_scripts');
}

/*
 * This is the widget that will appear on the admin dashboard.
 */

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

    //get the number of results to display.
    if (!isset($options['widget_no_to_display'])) {
        $options['widget_no_to_display'] = 10;
    } else {
        $options['widget_no_to_display'] = intval($options['widget_no_to_display']);
    }

    //Get rows from the database based on the options set in the admin panel
    $query = 'WHERE `ip_address` NOT IN (' . $ips . ')
            AND `user_id` NOT IN (' . $user_ids . ')';

    _e('<p>Welcome to the LBAK User Tracking dashboard widget.
        To see more detailed information on a section, hover over the clickable links.
        If you have the Page column activated, bold results indicate that the
        record has GET/POST variables associated with it and they are visible
        by hovering over the result.
        <br /><br /><a href="tools.php?page=lbakut" class="button-secondary">Settings</a>
        <a href="tools.php?page=lbakut&step=search" class="button-secondary">Search</a>
        <a href="tools.php?page=lbakut&step=stats" class="button-secondary">Stats</a>
        <a href="tools.php?page=lbakut&step=help" class="button-secondary">Help/FAQ</a></p>', 'lbakut');

    echo lbakut_print_table($query, 'widget', $options);
}

/*
 * Standard format for displaying time and dates in lbakut.
 */

function lbakut_time_format($time, $options = null) {
    if ($options == null) {
        $options = lbakut_get_options();
    }

    $timezone = date_default_timezone_get();
    date_default_timezone_set('UTC');

    $gmt_offset = get_option('gmt_offset');

    $time = $time + ($gmt_offset ? $gmt_offset : 0) * 3600;

    date_default_timezone_set($timezone);

    return strftime($options['time_format'] ? $options['time_format'] :
                    '%l:%M:%S %P, %a %e %b, %Y', $time);
}

/*
 * A 'time ago' function. Displays how long ago a unix timestamp occured in
 * relation to time() (now). If it was over a week ago, the function just
 * returns the time and date formatted by lbakut_time_format().
 *
 * This also handles the user's settings. If they opt not to use the time ago
 * format, this function will react appropriately and format the time using
 * the user specified time string.
 */

function lbakut_time_ago_format($time, $options = null) {
    $temp = time() - $time;
    if ($options == null) {
        $options = lbakut_get_options();
    }
    if ($temp > (60 * 60 * 24 * 7) || !$options['use_time_ago']) {
        return lbakut_time_format($time);
    }

    $days = floor($temp / 86400);
    $temp -= ( $days * 86400);
    $hours = floor($temp / 3600);
    $temp -= ( $hours * 3600);
    $mins = floor($temp / 60);
    $temp -= ( $mins * 60);
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
}

/*
 * Function used to translate options in the database
 * associative array (list available in housekeeping.php) into a human readable
 * format.
 */

function lbakut_option_translate($k) {
    //Remove the option name prefix.
    $k = preg_replace('/(widget_show_|track_|search_show_)/i', '', $k);
    switch ($k) {

        //GENERIC NAME TRANSLATION
        case 'ip': $return = 'IP Address';
            break;
        case 'real_ip': $return = 'Real IP Address';
            break;
        case 'referrer': $return = 'Referrer';
            break;
        case 'time': $return = 'Time';
            break;
        case 'user_id': $return = 'User ID';
            break;
        case 'user_level': $return = 'User Level';
            break;
        case 'display_name': $return = 'Display Name';
            break;
        case 'user_agent': $return = 'User Agent';
            break;
        case 'script_name': $return = 'Script Name';
            break;
        case 'query_string': $return = 'GET vars';
            break;
        case 'post_vars': $return = 'POST vars';
            break;
        case 'cookies': $return = 'Cookies';
            break;
        case 'os': $return = 'OS';
            break;
        case 'name': $return = 'Display Name';
            break;
        case 'page': $return = 'Page';
            break;
        case 'browser': $return = 'Browser';
            break;
        case 'get': $return = 'GET vars';
            break;
        case 'post': $return = 'POST vars';
            break;
        case 'ignore_admin': $return = '<b>Ignore Admin</b>';
            break;

        //DEFAULT
        default: $return = $k;
            break;
    }
    return __($return, 'lbakut');
}

/*
 * Settings page descriptions based on the name of the options in the database
 * associative array (list available in housekeeping.php).
 */

function lbakut_option_description($k) {
    if (preg_match('/track_/', $k)) {
        $prefix = 'Check to track ' . lbakut_option_translate($k) . '.';
    } else if (preg_match('/search_/', $k)) {
        $prefix = 'Check to show ' . lbakut_option_translate($k) . ' on the search page.';
    } else if (preg_match('/widget_show_/', $k)) {
        $prefix = 'Check to show ' . lbakut_option_translate($k) . ' in the dashboard widget.';
    }
    switch ($k) {

        //GENERIC NAME DESCRIPTIONS

        case 'ip':
            $return = 'An IP address is a number the can tell you what machine
                or network a request came from. Generally, records sharing the
                same IP address will have come from the same place (there are
                a few exceptions to this). For more information on IP addresses,
                <a href="http://whatismyipaddress.com/ip-address" target="_blank">click here</a>.';
            break;

        case 'real_ip':
            $return = 'The "real" IP address field is used to try and detect
                IP addresses through proxies. A proxy is a web service that
                masks your IP address from the view of other web servers
                but some proxy services will still send your real IP
                address in a non-standard manner to the web server. The plugin
                attempts to find this IP address and, if it does, it will be
                displayed here.';
            break;

        case 'referrer':
            $return = 'This column will tell you where the request came from.
                For example, requests that have come through google will display
                as www.google.com and a query string that will include data 
                such as a search term to help you identify what search brough that 
                user to your site. This field is easily tampered with, though, so
                it is not advised to trust it 100%.';
            break;

        case 'time':
            $return = 'This column will tell you the exact time that the click
                happened. Time formats can be changed in the settings and you
                can choose whether or not you want to use the "time ago" format.';
            break;

        case 'user_id':
            $return = 'The user ID associated with the user who generated this
                page click. If no user was logged in at the time this column
                will display as blank.';
            break;

        case 'user_level':
            $return = 'The user level of the user that generated this page click.
                A description of all the user levels WordPress has can be found
                <a href="http://codex.wordpress.org/User_Levels" target="_blank">here</a>.';
            break;

        case 'name':
        case 'display_name':
            $return = 'The display name of the user that generated this page click.
                If the user was not logged in then this column will display as
                blank.';
            break;

        case 'user_agent':
            $return = 'User Agent';
            break;

        case 'page':
        case 'script_name':
            $return = 'The name of the page that the user visited in the page
                click. Only the name of the script will be displayed here but
                if you hover over it you will get a far more detailed description
                of the data that the user sent to your server.';
            break;

        case 'browser':
            $return = 'The browser that the user was using to generate the page
                click. If this column is blank that means the browser was not
                recognised by the browser detection method employed in this
                plugin. We use Gary Keith\'s "browscap" definitions file. To
                learn more, <a href="http://browsers.garykeith.com/" target="_blank">click here</a>.
                <b>Note that this column relies on the tracking of the User Agent
                field in the tracking settings.</b>';
            break;

        case 'os':
            $return = 'The Operating system that the user was using to generate
                the page clicl. If this column is blank that means the Operating
                System was not recognised. We use Gary Keith\'s "browscap" definitions file. To
                learn more, <a href="http://browsers.garykeith.com/" target="_blank">click here</a>.
                <b>Note that this column relies on the tracking of the User Agent
                field in the tracking settings.</b>';
            break;

        case 'get':
        case 'query_string':
            $return = 'The GET variables are a list of variables sent in the
                URL string to the webserver as an easy way to send data to
                a webserver. To find out more, <a href="http://en.wikipedia.org/wiki/Query_string" target="_blank">click here</a>.';
            break;

        case 'post':
        case 'post_vars':
            $return = 'POST variables are sent via HTTP headers to a webserver
                as a slightly more secure way of transmitting data. To find
                out more, <a href="http://en.wikipedia.org/wiki/POST_(HTTP)" target="_blank">click here</a>.';
            break;

        case 'cookies':
            $return = 'Cookies are small text files stored by your browser
                as a means of storing data about you over "sessions". To learn
                more, <a href="http://en.wikipedia.org/wiki/HTTP_cookie" target="_blank">click here</a>.';
            break;

        //TRACKER SETTINGS
        case 'track_real_ip': $return = $prefix . '.
            This attempts to get a user\'s real IP address if they are browsing
            through a proxy, however, the success rate is very low. Most good
            proxies will mask the IP address properly.';
            break;
        case 'track_post_vars': $return = $prefix . '.
            POST variables are variables sent to web servers in forms and such.';
            break;
        case 'track_user_agent': $return = $prefix . '.
            <b>NOTE: This is required for browser and OS detection.</b>';
            break;
        case 'track_ignore_admin': $return = '<b>Check this to disable the tracking
            of admin users.</b>';
            break;

        //DEFAULT
        default: $return = $prefix;
            break;
    }

    return __($return, 'lbakut');
    break;
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
        if (preg_match('/' . $prefix . '/', $k)) {

            if ($options[$k] == true) {
                $checked = 'checked';
            } else {
                $checked = '';
            }

            if ($prefix == 'track_') {
                if ($k == 'track_ignore_admin') {
                    $pre = '';
                } else {
                    $pre = 'Track';
                }
            } else if ($prefix == 'widget_show_' || $prefix == 'search_') {
                $pre = 'Show';
            } else {
                $pre = 'Enable';
            }

            $return .= '
            <tr>
                <td>
                    <label for="' . $k . '">
                        ' . __($pre . ' ' . lbakut_option_translate($k) . '?', 'lbakut') . '
                    </label>
                </td>
                <td>
                    <input type="hidden" name="' . $k . '" value="0" />
                    <input type="checkbox" id="' . $k . '"
                    name="' . $k . '" value="1" ' . $checked . ' />
                </td>
                <td>
                    <p>' . lbakut_option_description($k) . '</p>
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

    $return = '<select name="' . $get_var . '">';
    foreach ($times as $k => $v) {
        if ($_GET[$get_var] == $v) {
            $selected = 'selected';
        } else {
            $selected = '';
        }
        $return .= '<option value="' . $v . '" ' . $selected . '>' . $k . '</option>';
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
    } else {
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
    $column = ($get_var == 'page_name') ? 'page' : $get_var;

    if (!empty($_GET[$get_var])) {
        //Create array out of comma separated values (csv).
        $csv_array = explode(",", $_GET[$get_var]);

        //Get the correct operator for the SQL query.
        if (stripslashes($_GET[$get_var . '_is']) != "isn't") {
            $operator = '=';
        } else {
            $operator = '!=';
        }

        //If there is more than 1 value in the array.
        if (sizeof($csv_array) > 1) {
            $return = 'AND (';
            foreach ($csv_array as $value) {
                if ($operator == '=') {
                    $andor = ' OR ';
                } else {
                    $andor = ' AND ';
                }

                //Prepare the value for database entry based on $type.
                if ($type == 'string') {
                    $prepared = $wpdb->escape(trim($value));
                } else {
                    $prepared = intval($value);
                }
                $return .= '`' . $column  . '`' . $operator . "
                                            '" . $prepared . "'" . $andor;
            }
            $return = rtrim($return, $andor) . ')';
        } else {

            //Prepare the value for database entry based on $type.
            if ($type == 'string') {
                $prepared = $wpdb->escape(trim($_GET[$get_var]));
            } else {
                $prepared = intval($_GET[$get_var]);
            }
            $return = 'AND `' . $column . '`' . $operator . "
                                        '" . $prepared . "'";
        }
    } else {
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
        ' . $options['main_table_name'] . ' WHERE `id` IN (SELECT `id` FROM
            ' . $options['main_table_name'] . ' ' . $where_clause . ')');

    $rows = $wpdb->get_results('SELECT * FROM `' . $options['main_table_name'] . '`
        ' . $where_clause . '
            ORDER BY `time` DESC 
            LIMIT ' . $options[$place . '_no_to_display'] . '
            OFFSET ' . ((lbakut_page_var()) - 1) * $options[$place . '_no_to_display']);

    if ($place != 'widget') {
        $return .= lbakut_do_pagination($no_of_results, $place, $options) . '<br />';
    }
    $return .= '<table class="widefat">';
    $return .= '<thead>';
    if ($options[$place . '_show_time'] == true)
        $return .= '<th>Time</th>';
    if ($options[$place . '_show_name'] == true)
        $return .= '<th>Name</th>';
    if ($options[$place . '_show_user_id'] == true)
        $return .= '<th>User ID</th>';
    if ($options[$place . '_show_user_level'] == true)
        $return .= '<th>User Level</th>';
    if ($options[$place . '_show_ip'] == true)
        $return .= '<th>IP</th>';
    if ($options[$place . '_show_real_ip'] == true)
        $return .= '<th>Real IP</th>';
    if ($options[$place . '_show_browser'] == true)
        $return .= '<th>Browser</th>';
    if ($options[$place . '_show_os'] == true)
        $return .= '<th>OS</th>';
    if ($options[$place . '_show_referrer'] == true)
        $return .= '<th>Referrer</th>';
    if ($options[$place . '_show_page'] == true)
        $return .= '<th>Page Visited</th>';
    if ($options[$place . '_show_page_title'] == true)
        $return .= '<th>Page Title</th>';
    if ($options[$place . '_show_get'] == true)
        $return .= '<th>GET vars</th>';
    if ($options[$place . '_show_post'] == true)
        $return .= '<th>POST vars</th>';
    if ($options[$place . '_show_cookies'] == true)
        $return .= '<th>Cookies</th>';
    $return .= '</thead>';
    foreach ($rows as $row) {
        if ($row->user_agent) {
            $browscap = lbakut_browser_info($row->user_agent, $brows);
        } else {
            $browscap = null;
        }

        if ($options[$place . '_show_referrer'] == true)
            $referrer = parse_url($row->referrer);

        $return .= '<tr>';

        if ($options[$place . '_show_time'] == true)
            $return .= '<td>' . lbakut_time_ago_format($row->time) . '</td>';
        if ($options[$place . '_show_name'] == true)
            $return .= '<td><a href="tools.php?page=lbakut&step=search&user_id=' . $row->user_id . '"
                title="Search for activity from the user ' . $row->display_name . '">
                    ' . $row->display_name . '</a></td>';
        if ($options[$place . '_show_user_id'] == true)
            $return .= '<td><a href="tools.php?page=lbakut&step=search&user_id=' . $row->user_id . '"
                title="Search for activity from the user ' . $row->display_name . '">
                    ' . $row->user_id . '</td>';
        if ($options[$place . '_show_user_level'] == true)
            $return .= '<td>' . $row->user_level . '</td>';
        if ($options[$place . '_show_ip'] == true) {
            $title = lbakut_get_ip_tooltip($row->ip_address, $options, $brows);
            if ($title) {
                $return .= '<td><a href="tools.php?page=lbakut&step=search&ip_address=' . $row->ip_address . '"
                    class="ip_address" title="' . $title . '">' . $row->ip_address . '</a></td>';
            } else {
                $return .= '<td><a href="tools.php?page=lbakut&step=search&ip_address=' . $row->ip_address . '">' . $row->ip_address . '</a></td>';
            }
        }
        if ($options[$place . '_show_real_ip'] == true) {
            $title = lbakut_get_ip_tooltip($row->real_ip_address, $options, $brows);
            if ($title) {
                $return .= '<td><a href="tools.php?page=lbakut&step=search&ip_address=' . $row->real_ip_address . '"
                    class="ip_address" title="' . $title . '">' . $row->real_ip_address . '</a></td>';
            } else {
                $return .= '<td><a href="tools.php?page=lbakut&step=search&ip_address=' . $row->real_ip_address . '">' . $row->real_ip_address . '</a></td>';
            }
        }
        if ($options[$place . '_show_browser'] == true) {
            $title = lbakut_get_browser_tooltip($browscap, $options);
            if ($title) {
                $return .= '<td><a href="#" class="browser" title="' . $title . '">' . $browscap['parent'] . '</a></td>';
            } else {
                $return .= '<td>' . $browscap['parent'] . '</td>';
            }
        }
        if ($options[$place . '_show_os'] == true)
            $return .= '<td>' . $browscap['platform'] . '</td>';
        if ($options[$place . '_show_referrer'] == true)
            $return .= $row->referrer ? '<td>' . $referrer['scheme'] . '://' . $referrer['host'] . $referrer['path'] . '</td>' : '<td></td>';
        if ($options[$place.'_show_page'] == true) {
            $title = lbakut_get_page_tooltip($row, $options);
            $page_display = preg_replace('/\?.+/i', '', $row->page);
            if ($title) {
                if ($row->query_string || $row->post_vars) {
                    $return .= '<td><a href="' . $row->page . '" class="script_name"
                        title="' . $title . '"><b>' . $page_display . '</b></a></td>';
                } else {
                    $return .= '<td><a href="' . $row->page . '" class="script_name"
                        title="' . $title . '">' . $page_display . '</a></td>';
                }
            } else {
                $return .= '<td><a href="' . $row->script_name . '" class="script_name">' . $page_display . '</a></td>';
            }
        }
        if ($options[$place . '_show_page_title'] == true)
            $return .= '<td>' . $row->page_title . '</td>';
        if ($options[$place . '_show_get'] == true)
            $return .= '<td>' . lbakut_query_string_formatter($row->query_string) . '</td>';
        if ($options[$place . '_show_post'] == true)
            $return .= '<td>' . lbakut_query_string_formatter($row->post_vars) . '</td>';
        if ($options[$place . '_show_cookies'] == true)
            $return .= '<td>' . lbakut_query_string_formatter($row->cookies) . '</td>';

        $return .= '</tr>';
    }
    $return .= '</table>';

    $return .= '<br />' . lbakut_do_pagination($no_of_results, $place, $options);

    return $return;
}

function lbakut_format_filesize($bytes) {
   if ($bytes < 1024) return $bytes.' B';
   elseif ($bytes < 1048576) return round($bytes / 1024, 2).' KB';
   elseif ($bytes < 1073741824) return round($bytes / 1048576, 2).' MB';
   elseif ($bytes < 1099511627776) return round($bytes / 1073741824, 2).' GB';
   else return round($bytes / 1099511627776, 2).' TB';
}

/*
 * This gets the tooltip to put in the title attribute of the page column
 * of records tables.
 */

function lbakut_get_page_tooltip($row, $options = null) {
    global $wpdb;
    if ($options == null) {
        $options = lbakut_get_options();
    }
    if ($row->page) {
        if ($row->post_vars) {
            $post_vars = '<br /><br /><b>POST vars</b><br /><br />' . lbakut_query_string_formatter($row->post_vars);
        }
        return '<b>Full URL</b><br/>Please note that this is subject to the
            data you were tracking when this was logged.<br /><br />
            <a href=\'' . $row->page . '\'>' . $row->page . '</a>' . $post_vars;
    } else {
        return null;
    }
}

/*
 * This gets the tooltip to put in the title attribute of the browser column
 * of records tables.
 */

function lbakut_get_browser_tooltip($browscap, $brows = null, $options = null) {
    global $wpdb;
    if ($options == null) {
        $options = lbakut_get_options();
    }
    if (!$browscap) {
        return null;
    }

    $return = '<b>Name</b>: ' . $browscap['browser'] . '<br />';
    $return .= '<b>Version</b>: ' . $browscap['version'] . '<br /><br />';
    $return .= '<b>Javascript</b>: ' . lbakut_yes_no($browscap['javascript']) . '<br />';
    $return .= '<b>Frames</b>: ' . lbakut_yes_no($browscap['frames']) . '<br />';
    $return .= '<b>Iframes</b>: ' . lbakut_yes_no($browscap['iframes']) . '<br />';
    $return .= '<b>Tables</b>: ' . lbakut_yes_no($browscap['tables']) . '<br />';
    $return .= '<b>CSS</b>: ' . lbakut_yes_no($browscap['supportscss']) . ' (v' . $browscap['cssversion'] . ')<br />';
    $return .= '<b>Cookies</b>: ' . lbakut_yes_no($browscap['cookies']) . '<br />';
    $return .= '<b>Mobile Device</b>: ' . lbakut_yes_no($browscap['ismobiledevice']) . '<br />';
    $return .= '<b>Crawler</b>: ' . lbakut_yes_no($browscap['crawler']) . '<br />';
    $return .= '<b>Syndication Reader</b>: ' . lbakut_yes_no($browscap['issyndicationreader']) . '<br />';

    return $return;
}

/*
 * Prints either a yes in green or a no in red depending on the boolean you
 * pass to it.
 */

function lbakut_yes_no($bool) {
    if ($bool == true) {
        return '<span style=\'color: #aaffaa;\'>Yes</span>';
    } else {
        return '<span style=\'color: #ffaaaa;\'>No</span>';
    }
}

/*
 * This gets the tooltip to put in the title attribute of the IP address column
 * of records tables.
 */

function lbakut_get_ip_tooltip($ip, $options = null, $brows = null) {
    global $wpdb;
    global $lbakut_ip_tooltip_cache;
    if ($options == null) {
        $options = lbakut_get_options();
    }

    //caching mechanism
    if ($lbakut_ip_tooltip_cache[$ip]) {
        return $lbakut_ip_tooltip_cache[$ip];
    }

    $row = $wpdb->get_row('SELECT * FROM `' . $options['user_stats_table_name'] . '` WHERE `ip`="' . $wpdb->escape($ip) . '"');
    if ($row != false) {
        $return .= '<b>First visit:</b> ' . lbakut_time_ago_format($row->first_visit) . '<br />';
        $return .= '<b>Last visit:</b> ' . lbakut_time_ago_format($row->last_visit) . '<br />';
        $return .= '<b>Last updated:</b> ' . lbakut_time_ago_format(lbakut_get_stats_last_updated($options)) . '<br /><br />';
        $users = unserialize($row->user_ids);
        unset($users[0]); //get rid of guest logins

        if ($users) {
            $return .= '<b>Accounts</b> <br />';
            foreach ($users as $user_id => $records) {
                $userdata = get_userdata($user_id);
                $return .= $user_id . ': <a href=\'user-edit.php?user_id=' . $user_id . '\'>' . $userdata->user_login . '</a> - ' . $records . ' records<br />';
            }
        }

        $pages = unserialize($row->page_views);

        if ($pages) {
            $return .= '<b>Page views</b><br />';
            foreach ($pages as $page => $clicks) {
                $return .= '<a href=\'' . $page . '\'>' . $page . '</a> - ' . $clicks . ' views<br />';
            }
        }

        $user_agents = unserialize($row->user_agents);
        if ($user_agents) {
            $return .= '<b>Browsers</b><br />';
            foreach ($user_agents as $user_agent => $count) {
                if ($user_agent) {
                    $browscap = lbakut_browser_info($user_agent, $brows);
                }

                if ($browscap) {
                    $return .= $browscap['parent'] . '<br />';
                } else {
                    $return .= 'Unrecognised.<br />';
                }
            }
        }
        //Add this result to the cache just in case it is needed later.
        $lbakut_ip_tooltip_cache[$ip] = $return;
        return $return;
    } else {
        return 'There is nothing on record for this IP address. This information
            gets updated on the same schedule as the stats page.';
    }
}

/*
 * Get the variable used in pagination.
 */

function lbakut_page_var() {
    if (!isset($_GET['lbakut_page'])) {
        return 1;
    } else {
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

    $pages = ceil($no_of_results / $options[$place . '_no_to_display']);

    if ($pages < 2) {
        return null;
    }

    $start = max(1, lbakut_page_var() - 3);
    $end = min($pages, lbakut_page_var() + 3);

    $result .= '<div class="lbakut_pages">';
    $result .= 'Showing results ' . ((lbakut_page_var() - 1) * $options[$place . '_no_to_display'] + 1) . '
        - ' . (lbakut_page_var() * $options[$place . '_no_to_display']) . ' of ' . $no_of_results . '<br /><br />';

    $uri = preg_replace('/&lbakut_page=[0-9]+/', '', $_SERVER['QUERY_STRING']);

    if ($start != 1) {
        if ($uri == '') {
            $page_uri = '?lbakut_page=1';
        } else {
            $page_uri = '?' . $uri . '&lbakut_page=1';
        }
        $result .= '<span class="lbakut_page"><a href="' . $page_uri . '">&nbsp;1&nbsp;</a></span> ... ';
    }

    for ($page = $start; $page <= $end; $page++) {
        if ($uri == '') {
            $page_uri = '?lbakut_page=' . $page;
        } else {
            $page_uri = '?' . $uri . '&lbakut_page=' . $page;
        }

        if ($page == lbakut_page_var()) {
            $result .= '<span class="lbakut_page_selected"><a href="' . $page_uri . '">&nbsp;' . $page . '&nbsp;</a></span>';
        } else {
            $result .= '<span class="lbakut_page"><a href="' . $page_uri . '">&nbsp;' . $page . '&nbsp;</a></span>';
        }
    }

    if ($end != $pages) {
        if ($uri == '') {
            $page_uri = '?lbakut_page=' . $pages;
        } else {
            $page_uri = '?' . $uri . '&lbakut_page=' . $pages;
        }
        $result .= ' ... <span class="lbakut_page"><a href="' . $page_uri . '">&nbsp;' . $pages . '&nbsp;</a></span>';
    }

    $result .= '</div>';

    return $result;
}

?>