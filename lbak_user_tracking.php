<?php
/*
    Plugin Name: LBAK User Tracking
    Plugin URI: http://wordpress.org/extend/plugins/lbak-user-tracking/
    Description: An extensive user tracking plugin.
    Author: Sam Rose
    Version: 1.7.5
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

/*
 * These two functions are really useful as easy ways to get the base plugin
 * directory and URL from any file you want. It is necesary for them to be in
 * this file (or another php file in this directory).
 */
function lbakut_get_base_url() {
    return WP_PLUGIN_URL. '/'. basename(dirname(__FILE__));
}
function lbakut_get_base_dir() {
    return WP_PLUGIN_DIR. '/'. basename(dirname(__FILE__));
}

/*
 * This function needs to be updated with every version release. It is
 * necessary for this to be correct because the upgrade.php file depends
 * on it for running upgrade scripts where necessary.
 */
function lbakut_get_version() {
    return '1.7.5';
}

// i18n (internationalisation)
$plugin_dir = basename(dirname(__FILE__));
$languages_dir = $plugin_dir.'/languages';
load_plugin_textdomain( 'lbakut', WP_PLUGIN_DIR.'/'.$languages_dir,
        $languages_dir );

//Including all of the files necessary for the plugin to function.
require_once 'php_includes/upgrades.php';
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
//this filter adds to the list of schedule options for the WP Cron.
add_filter('cron_schedules', 'lbakut_cron_schedules');

/*
 * The following actions and filters are what makes the plugin function
 * correctly.
 */
add_action('wp_dashboard_setup', 'lbakut_dashboard_setup');
add_action('wp_loaded', 'lbakut_log_activity_start');
add_action('admin_menu', 'lbakut_admin_menu');
add_action('admin_head', 'lbakut_add_admin_header');
add_filter('the_content', 'lbakut_parse_shortcode');
add_action('admin_print_scripts-index.php', 'lbakut_add_scripts');

/*
 * Activation and uninstallation actions. The plugin upgrade script is
 * located in the activation hook function.
 */
register_activation_hook(__FILE__, 'lbakut_activation_setup');
register_deactivation_hook(__FILE__, 'lbakut_deactivate');
register_uninstall_hook(__FILE__, 'lbakut_uninstall');

/*
 * The next actions are necessary for the lbakut cron jobs to run.
 */
add_action('lbakut_update_browscap', 'lbakut_update_browscap');
add_action('lbakut_do_cache_and_stats', 'lbakut_do_cache_and_stats');
add_action('lbakut_database_management_cron', 'lbakut_database_management_cron');

?>