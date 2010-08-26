<?php
/*
    Plugin Name: LBAK User Tracking
    Plugin URI: null
    Description: An extensive user tracking plugin.
    Author: Sam Rose
    Version: 1.0
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

require_once 'php_includes/housekeeping.php';
require_once 'php_includes/visual.php';
require_once 'php_includes/main.php';

add_action('wp_dashboard_setup', 'lbakut_dashboard_setup');
add_action('init', 'lbakut_log_activity_start');
add_action('admin_menu', 'lbakut_admin_menu');
add_action('admin_head', 'lbakut_add_admin_header');
register_activation_hook(__FILE__, 'lbakut_activation_setup');
register_uninstall_hook(__FILE__, 'lbakut_uninstall');

// i18n
$plugin_dir = basename(dirname(__FILE__)). '/languages';
load_plugin_textdomain( 'lbakut', WP_PLUGIN_DIR.'/'.$plugin_dir, $plugin_dir );

?>
