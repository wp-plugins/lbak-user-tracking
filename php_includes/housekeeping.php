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


    //If the plugin is already installed.
    if ($cur_options) {
        $options_exist = true;
    }
    else {
        $options_exist = false;
    }

    //Declare table name.
    $main_table_name = $wpdb->prefix . "lbakut_activity_log";

    //Check table is not in use.
    if($wpdb->get_var("SHOW TABLES LIKE '$main_table_name'") != $main_table_name) {
        $db_main_table_exists = true;
    }
    else {
        $db_main_table_exists = false;
    }

    $create_main_table_sql = "
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

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    if (function_exists('dbDelta')) {
        dbDelta($create_main_table_sql);
    }
    else {
        if ($db_main_table_exists) {
            //This is a temporary solution to the dbDelta function not existing.
            $wpdb->query("DROP TABLE $main_table_name");
        }
        $wpdb->query($create_main_table_sql);
    }
    
    $option_default = array(
        'version' => lbakut_get_version(),
        'main_table_name' => $main_table_name,
        'widget_show' => true,
        'widget_ignored_ips' => array(0 => ''),
        'widget_ignored_users' => array(0 => ''),
        'widget_ignore_admin' => false,
        'widget_show_name' => true,
        'widget_show_ip' => true,
        'widget_show_real_ip' => false,
        'widget_show_page' => true,
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
        'search_show_name' => true,
        'search_show_ip' => true,
        'search_show_real_ip' => false,
        'search_show_user_id' => false,
        'search_show_user_level' => false,
        'search_show_page' => true,
        'search_show_browser' => true,
        'search_show_os' => true,
        'search_show_referrer' => false,
        'search_show_time' => true,
        'search_show_get' => false,
        'search_show_post' => false,
        'search_show_cookies' => false,
        'search_no_to_display' => 20
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

        if ($options['main_table_name']) {
            //Drop the rv_activity_log table.
            $wpdb->query('DROP TABLE `' . $options['main_table_name'] . '`');
        }
        else {
            //Table name option not defined... No idea why this would happen
            //but it can't hurt to have it checked :p
            trigger_error(__("Could not delete LBAK User Tracking database.", 'lbakut'), E_USER_ERROR);

        }

        //Erase options field in the settings table.
        lbakut_delete_options();
    }
}

/*
 * This function gets called as part of the admin options page header. Add any
 * scripts or styles to this.
*/
function lbakut_add_scripts() {
    wp_enqueue_script('lbakut_admin_script', lbakut_get_base_url().'/js_includes/admin_page.js');
}

/*
 * Adding to the admin header. This is called on the admin_head action.
 */
function lbakut_add_admin_header() {
    echo '<link type="text/css" rel="stylesheet"
        href="'.lbakut_get_base_url().'/css_includes/admin_head.css?v=1.0" /> ' . "\n";
}


/*
 * Functions to get, delete and update the lbakut options.
 */
function lbakut_get_options() {
    return get_option('lbakut_options');
}
function lbakut_update_options($options) {
    return update_option('lbakut_options', $options);
}
function lbakut_delete_options() {
    return delete_option('lbakut_options');
}

?>
