<?php
/*
    Plugin Name: LBAK User Tracking
    Plugin URI: null
    Description: An extensive user tracking plugin.
    Author: Sam Rose
    Version: ALPHA
    Author URI: http://lbak.co.uk/
*/

/*
    Dashboard Notepad Copyright (C) 2010  Sam Rose  (email : samwho@lbak.co.uk)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation in the Version 2.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
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