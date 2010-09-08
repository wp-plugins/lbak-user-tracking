<?php
/*
    Plugin Name: LBAK User Tracking
    Plugin URI: http://wordpress.org/extend/plugins/lbak-user-tracking/
    Description: An extensive user tracking plugin.
    Author: Sam Rose
    Version: 1.4
    Author URI: http://lbak.co.uk/
*/

/*
    LBAK User Tracking Copyright (C) 2010  Sam Rose  (email : samwho@lbak.co.uk)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU GPL v2.

    This plugin is distributed without any warranty. The plugin author will not
    take any responsibility for the data or actions of this software. The
    plugin author will attempt to support the plugin as much as possible but
    there are no guarentees of support or help from the author.
*/

function lbakut_get_base_url() {
    return WP_PLUGIN_URL. '/'. basename(dirname(__FILE__));
}
function lbakut_get_base_dir() {
    return WP_PLUGIN_DIR. '/'. basename(dirname(__FILE__));
}
function lbakut_get_version() {
    return '1.4';
}

// i18n
$plugin_dir = basename(dirname(__FILE__));
$languages_dir = $plugin_dir.'/languages';
load_plugin_textdomain( 'lbakut', WP_PLUGIN_DIR.'/'.$languages_dir,
        $languages_dir );

require_once 'php_includes/housekeeping.php';
require_once 'php_includes/stats.php';
require_once 'php_includes/main.php';
require_once 'php_includes/visual.php';
require_once 'php_includes/admin.php';
require_once 'php_includes/shortcodes.php';

//Adding more WP Cron options.
function lbakut_cron_schedules() {
    return array(
        'weekly' => array('interval' => 604800, 'display' => 'Once Weekly'),
        'fortnightly' => array('interval' => 1209600, 'display' => 'Once Fortnightly'),
    );
}
add_filter('cron_schedules', 'lbakut_cron_schedules');

add_action('wp_dashboard_setup', 'lbakut_dashboard_setup');
add_action('init', 'lbakut_log_activity_start');
//add_action('shutdown', 'lbakut_log_activity_end');
add_action('admin_menu', 'lbakut_admin_menu');
add_action('admin_head', 'lbakut_add_admin_header');
add_filter('the_content', 'lbakut_parse_shortcode');
register_activation_hook(__FILE__, 'lbakut_activation_setup');
register_uninstall_hook(__FILE__, 'lbakut_uninstall');

add_action('lbakut_do_user_stats', 'lbakut_do_user_stats');
add_action('lbakut_update_browscap', 'lbakut_update_browscap');
add_action('lbakut_do_cache_and_stats', 'lbakut_do_cache_and_stats');
add_action('lbakut_database_management_cron', 'lbakut_database_management_cron');

?>