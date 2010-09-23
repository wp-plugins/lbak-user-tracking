<?php

/*
 * This function is called upon the plugin being activated. It creates the
 * database tables and default options.
*/
function lbakut_activation_setup() {
    //Get database object.
    global $wpdb;

    //Check for current options.
    $cur_options = lbakut_get_options();

    //CALL UPGRADE FUNCTION HERE IF NEEDED

    lbakut_upgrade_fix('1.7', 'lbakut_stats_format_fix');

    //END UPGRADE FUNCTION CALLS


    //If the plugin is already installed.
    $options_exist = $cur_options ? true : false;

    //Declare table names.
    $main_table_name = $wpdb->prefix . "lbakut_activity_log";
    $browscache_table_name = $wpdb->prefix . "lbakut_browser_cache";
    $stats_table_name = $wpdb->prefix . "lbakut_stats";
    $user_stats_table_name = $wpdb->prefix . "lbakut_user_stats";

    $create_table_sql = "
    CREATE TABLE $main_table_name (
            id int NOT NULL AUTO_INCREMENT,
            ip_address text NOT NULL,
            real_ip_address text NOT NULL,
            referrer text NOT NULL,
            time int NOT NULL,
            user_id int DEFAULT '0' NOT NULL,
            user_level smallint NULL,
            display_name text NULL,
            user_agent text NOT NULL,
            script_name text NOT NULL,
            page_title text NOT NULL,
            query_string text NOT NULL,
            post_vars text NOT NULL,
            cookies text NOT NULL,
            PRIMARY KEY (id),
            KEY time (time),
            KEY ip (ip_address(9)),
            KEY user_id (user_id),
            KEY user_level (user_level)
        );
            ";
    $create_table_sql .= "
    CREATE TABLE $browscache_table_name (
            id int NOT NULL AUTO_INCREMENT,
            user_agent text NOT NULL,
            browser_name_regex text NOT NULL,
            browser_name_pattern text NOT NULL,
            parent text NOT NULL,
            platform text NOT NULL,
            browser text NOT NULL,
            version text NOT NULL,
            majorver mediumint(9) NOT NULL,
            minorver mediumint(9) NOT NULL,
            frames tinyint(1) NOT NULL,
            iframes tinyint(1) NOT NULL,
            tables_ tinyint(1) NOT NULL,
            cookies tinyint(1) NOT NULL,
            javaapplets tinyint(1) NOT NULL,
            javascript tinyint(1) NOT NULL,
            cssversion mediumint(9) NOT NULL,
            supportscss tinyint(1) NOT NULL,
            alpha tinyint(1) NOT NULL,
            beta tinyint(1) NOT NULL,
            win16 tinyint(1) NOT NULL,
            win32 tinyint(1) NOT NULL,
            win64 tinyint(1) NOT NULL,
            backgroundsounds tinyint(1) NOT NULL,
            cdf tinyint(1) NOT NULL,
            vbscript tinyint(1) NOT NULL,
            activexcontrols tinyint(1) NOT NULL,
            isbanned tinyint(1) NOT NULL,
            ismobiledevice tinyint(1) NOT NULL,
            issyndicationreader tinyint(1) NOT NULL,
            crawler tinyint(1) NOT NULL,
            aol tinyint(1) NOT NULL,
            aolversion text NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY user_agent (user_agent(128)),
            KEY crawler (crawler),
            KEY issyndicationreader (issyndicationreader)
        );
            ";
    $create_table_sql .= "
    CREATE TABLE $stats_table_name (
            id int NOT NULL AUTO_INCREMENT,
            time int NOT NULL,
            rows int NOT NULL,
            unique_ips int NOT NULL,
            recognised int NOT NULL,
            browser_array text NOT NULL,
            platform_array text NOT NULL,
            script_name_array text NOT NULL,
            user_agents int NOT NULL,
            frames int NOT NULL,
            iframes int NOT NULL,
            tables_ int NOT NULL,
            cookies int NOT NULL,
            javaapplets int NOT NULL,
            javascript tinyint(1) NOT NULL,
            cssversion int NOT NULL,
            supportscss int NOT NULL,
            alpha int NOT NULL,
            beta int NOT NULL,
            win16 int NOT NULL,
            win32 int NOT NULL,
            win64 int NOT NULL,
            backgroundsounds int NOT NULL,
            cdf int NOT NULL,
            vbscript int NOT NULL,
            activexcontrols int NOT NULL,
            isbanned int NOT NULL,
            ismobiledevice int NOT NULL,
            issyndicationreader int NOT NULL,
            crawler int NOT NULL,
            aol int NOT NULL,
            aolversion int NOT NULL,
            PRIMARY KEY (id),
            KEY time (time)
        );
            ";
    $create_table_sql .= "
    CREATE TABLE $user_stats_table_name (
            id int NOT NULL AUTO_INCREMENT,
            ip text NOT NULL,
            first_visit int NOT NULL,
            last_visit int NOT NULL,
            user_agents text NOT NULL,
            user_ids text NOT NULL,
            page_views text NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY ip (ip(15))
        );
            ";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    if (function_exists('dbDelta')) {
        dbDelta($create_table_sql);
    }
    else {
        trigger_error('Could not create tables.', E_USER_ERROR);
    }

    $option_default = array(
            'version' => lbakut_get_version(),
            'main_table_name' => $main_table_name,
            'browscache_table_name' => $browscache_table_name,
            'stats_table_name' => $stats_table_name,
            'user_stats_table_name' => $user_stats_table_name,
            'widget_show' => true,
            'widget_ignored_ips' => array(0 => ''),
            'widget_ignored_users' => array(0 => ''),
            'widget_show_name' => true,
            'widget_show_ip' => true,
            'widget_show_real_ip' => false,
            'widget_show_page' => false,
            'widget_show_browser' => true,
            'widget_show_os' => true,
            'widget_show_referrer' => false,
            'widget_show_time' => true,
            'widget_show_get' => false,
            'widget_show_post' => false,
            'widget_show_cookies' => false,
            'widget_no_to_display' => 10,
            'delete_on_uninstall' => true,
            'track_ip' => true,
            'track_real_ip' => true,
            'track_referrer' => true,
            'track_time' => true,
            'track_user_id' => true,
            'track_user_level' => true,
            'track_display_name' => true,
            'track_user_agent' => true,
            'track_script_name' => true,
            'track_query_string' => false,
            'track_post_vars' => false,
            'track_cookies' => false,
            'track_ignore_admin' => false,
            'search_show_name' => true,
            'search_show_ip' => true,
            'search_show_real_ip' => false,
            'search_show_user_id' => false,
            'search_show_user_level' => false,
            'search_show_page' => false,
            'search_show_browser' => true,
            'search_show_os' => true,
            'search_show_referrer' => false,
            'search_show_time' => true,
            'search_show_get' => false,
            'search_show_post' => false,
            'search_show_cookies' => false,
            'search_no_to_display' => 20,
            'stats_enable_shortcodes' => true,
            'stats_update_frequency' => 'daily',
            'stats_browser_widget' => false,
            'stats_os_widget' => false,
            'stats_pageviews_widget' => false,
            'database_delete_schedule' => false,
            'database_delete_threshold' => 60,
            'database_delete_crawlers' => false,
            'database_delete_last_count' => 0,
            'use_time_ago' => true,
            'time_format' => '%l:%M:%S %P, %a %e %b, %Y',
            'log' => true
    );

    //BEGIN OPTION INITIALISATION LOOP
    $options = array();
    foreach ($option_default as $k => $v) {
        if ($options_exist) {
            //if the options are already set
            if (!isset($cur_options[$k])) {
                //Check if the option the loop is on exists
                $options[$k] = $v;
                //Set it to a default value if it doesn't
            }
            else {
                //set it to the old value if it does already exist
                $options[$k] = $cur_options[$k];
            }
        }
        else {
            //if option s aren't set
            $options[$k] = $v;
        }
    }
    //END OPTION INITIALISATION LOOP

    //Some options housekeeping...
    $options['version'] = $option_default['version'];

    if ($options_exist) {
        lbakut_delete_options();
    }

    //Add AND update to account for the plugin already being there.
    add_option('lbakut_options', null, null, 'no');
    lbakut_update_options($options);

    //Set the lbakut cron jobs
    lbakut_cron_jobs('set');

    //If no stats or cache exist, create them.
    if (lbakut_get_stats_last_updated() < 1) {
        lbakut_do_cache_and_stats();
        lbakut_log('Activation stats function call run.', __FILE__.':'.__LINE__);
    }

    lbakut_log('Plugin activated.');
}

function lbakut_deactivate() {
    lbakut_log('Plugin deactivated.');
}

/*
 * This function deals with the uninstalling of the plugin and only gets called
 * when the plugin is being uninstalled. Use it for cleaning up the databases
 * and options.
*/
function lbakut_uninstall() {
    global $wpdb;
    $options = lbakut_get_options();

    if ($options['delete_on_uninstall'] == true) {
        $wpdb->query('DROP TABLE `' . $options['main_table_name'] . '`');
        $wpdb->query('DROP TABLE `' . $options['stats_table_name'] . '`');
        $wpdb->query('DROP TABLE `' . $options['browscache_table_name'] . '`');

        //Erase options field in the settings table.
        lbakut_delete_options();
        lbakut_log('Plugin deleted, data deleted.');
    }
    else {
        lbakut_log('Plugin uninstalled without deleting data.');
    }

    //Remove the WP Cron jobs.
    lbakut_cron_jobs('remove');
}

/*
 * This function gets called as part of the admin options page header. Add any
 * scripts or styles to this.
*/
function lbakut_add_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('lbakut_admin_script', lbakut_get_base_url().'/js_includes/admin_page.js?v='.filectime(lbakut_get_base_dir().'/js_includes/admin_page.js'));
    wp_enqueue_script('jquery-tooltip', 'http://cdn.jquerytools.org/1.2.4/jquery.tools.min.js');

}

/*
 * Adding to the admin header. This is called on the admin_head action.
*/
function lbakut_add_admin_header() {
    echo '<link type="text/css" rel="stylesheet"
        href="'.lbakut_get_base_url().'/css_includes/admin_head.css?v='.filectime(lbakut_get_base_dir().'/css_includes/admin_head.css').'" /> ' . "\n";
}

/*
 * Update the browscap file from Gary Keith's webpage. Note that doing this
 * more than once daily could see you banned from accessing his website.
 * More details are here: http://browsers.garykeith.com/terms.asp
*/
function lbakut_update_browscap() {
    $browscap = lbakut_get_browscap();
    if ($browscap['GJK_Browscap_Version']['Version'] <
            intval(lbakut_get_web_page('http://updates.browserproject.com/version-number.asp'))) {

        $filename = lbakut_get_base_dir() . '/php_includes/php_browscap.ini';
        $data = lbakut_get_web_page('http://browsers.garykeith.com/stream.asp?PHP_BrowsCapINI');
        if (file_put_contents($filename, $data)) {
            return true;
        }
        else {
            return false;
        }
    }
    else {
        return false;
    }
}

function lbakut_cron_jobs($operation = 'reset') {
    $options = lbakut_get_options();

    if ($operation == 'remove' || $operation == 'reset') {

        //Remove the WP Cron jobs.
        if (($timestamp = wp_next_scheduled( 'lbakut_update_browscap'))) {
            wp_unschedule_event($timestamp, 'lbakut_update_browscap');
        }
        if (($timestamp = wp_next_scheduled( 'lbakut_do_cache_and_stats'))) {
            wp_unschedule_event($timestamp, 'lbakut_do_cache_and_stats');
        }
        if (($timestamp = wp_next_scheduled( 'lbakut_database_management_cron'))) {
            wp_unschedule_event($timestamp, 'lbakut_database_management_cron');
        }

    }

    if ($operation == 'set' || $operation == 'reset') {

        //Add the browscap updating function to the WP Cron.
        if (!wp_next_scheduled('lbakut_update_browscap')) {
            /*
             * IMPORTANT: Please do not change the frequency of this Cron job.
             * Gary Keith kindly allows me to check his website for updates but
             * only provided it does not cause too much server load for him.
             * I accept no responsibility if he bans you from his site
            */
            wp_schedule_event( (strtotime('tomorrow')+60*60*24*7), 'weekly',
                    'lbakut_update_browscap' );
            //echo strftime('%e %b %Y, %H:%M:%S', wp_next_scheduled('lbakut_update_browscap')).'<br />';
        }
        //Add the stats and caching function to the WP Cron.
        if (!wp_next_scheduled('lbakut_do_cache_and_stats')) {
            wp_schedule_event( time()+120, $options['stats_update_frequency'],
                    'lbakut_do_cache_and_stats' );
            //echo strftime('%e %b %Y, %H:%M:%S', wp_next_scheduled('lbakut_do_cache_and_stats')).'<br />';
        }
        //Add the database deleting schedule to the WP Cron.
        if (!wp_next_scheduled('lbakut_database_management_cron')) {
            wp_schedule_event( strtotime('tomorrow')+1, 'daily',
                    'lbakut_database_management_cron' );
            //echo strftime('%e %b %Y, %H:%M:%S', wp_next_scheduled('lbakut_do_user_stats')).'<br />';
        }

    }
}

/*
 * Function to delete records older than a certain age. Runs every day.
*/
function lbakut_database_management_cron() {
    global $wpdb;
    $options = lbakut_get_options();
    if ($options['database_delete_schedule'] && $options['database_delete_threshold']) {
        $time = time()-60*60*24*intval($options['database_delete_threshold']);
        $wpdb->query('DELETE FROM `'.$options['main_table_name'].'` WHERE `time`<'.$time);
        $affected = mysql_affected_rows();
        if ($options['database_delete_crawlers']) {
            $wpdb->query('DELETE FROM `'.$options['main_table_name'].'`
                WHERE `user_agent` IN (
                    SELECT `user_agent` FROM
                    `'.$options['browscache_table_name'].'`
                    WHERE `crawler`=1
                )');
            $affected += mysql_affected_rows();
        }
        $options['database_delete_last_count'] = $affected;
        lbakut_update_options($options);
    }
}


/*
 * Functions to get, delete and update the lbakut options.
*/
function lbakut_get_options() {
    global $lbakut_options;
    if (!isset($lbakut_options)) {
        $lbakut_options = get_option('lbakut_options');
    }
    return $lbakut_options;
}
function lbakut_update_options($options) {
    global $lbakut_options;
    update_option('lbakut_options', $options);
    $lbakut_options = $options;
    return $lbakut_options;
}
function lbakut_delete_options() {
    global $lbakut_options;
    unset($lbakut_options);
    return delete_option('lbakut_options');
}

?>
