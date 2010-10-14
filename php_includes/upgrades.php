<?php
/* 
 * This file's purpose is to hold all of the functions that are designed as
 * one off fixes in version upgrades. Introduced in v1.6.
 */

/*
 * This function is designed to run a callback function is the user is
 * upgrading to the specified version and only if they are upgrading, never
 * if they are just reactivating after deactivating.
 *
 * For this to work you need to call it in the lbakut_activation_setup()
 * function in /php_includes/housekeeping.php just after the current options
 * are drawn from the database. It WILL NOT WORK anywhere else.
 */
function lbakut_upgrade_fix($version, $callback) {
    ///Get the options and check if they existed. Return false if they didn't.
    $options = lbakut_get_options();
    if (!$options) {
        return false;
    }

    /*
     * If the current stored version number is not equal to the version passed
     * and the version in the lbakut_get_verion() function IS equal to the
     * passed verion number then execute the callback. Otherwise return false.
     *
     * This method ensures that, provided the lbakut_get_verion() function gets
     * updated properly, this function will ONLY run on update activations.
     */

    if ($options['version'] != $version && lbakut_get_version() == $version) {
        return call_user_func($callback);
    }
    else {
        return false;
    }
}

/*
 * With the release of 1.7, the browser section of the user stats got reformatted
 * to an incompatible format to 1.6 and below. Because of this, the user stats
 * table needs rebuilding.
 *
 * If _would_ be possible to do it without doing a full rebuild but I think
 * a full rebuild is a good idea.
 */
function lbakut_stats_format_fix() {
    global $wpdb;
    $options = lbakut_get_options();

    //Empty the stats table. Regrettable but necessary. Return false on failure.
    $wpdb->query('TRUNCATE TABLE `'.$options['user_stats_table_name'].'`');
    
    //Execute the user stats function. Cache function not required.
    lbakut_do_user_stats(true);
    lbakut_log('Successfully run the 1.7.2 stats update fix script.', null, "message", true);
    return true;
}

function lbakut_remove_deprecated_vars() {
    $options = lbakut_get_options();
    unset(  $options['search_show_script_name'],
            $options['widget_show_script_name'],
            $options['track_page_title'],
            $options['widget_show_page_title'],
            $options['search_show_page_title']);
    lbakut_update_options($options);
    lbakut_log('Run removal of deprecated variables (1.7.3 upgrade).');
}

function lbakut_alter_user_agent_index() {
    global $wpdb;
    $options = lbakut_get_options();

    $wpdb->query("ALTER TABLE ".$options['browscache_table_name']." DROP INDEX user_agent");
    $wpdb->query("ALTER TABLE ".$options['browscache_table_name']." ADD INDEX user_agent (user_agent(196))");

    lbakut_log('Run the 1.7.5 index alteration fix.');
}

?>
