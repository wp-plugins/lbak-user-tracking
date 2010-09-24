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

    //If the options can't found, return false. No options indicates no plugin.
    if (!$options) {
        return false;
    }

    //Empty the stats table. Regrettable but necessary. Return false on failure.
    if (!$wpdb->query('TRUNCATE TABLE `'.$options['user_stats_table'].'`')) {
        return false;
    }
    
    //Execute the user stats function. Cache function not required.
    lbakut_do_user_stats(true);
    lbakut_log('Successfully run the 1.7 stats update fix script.', null, true);
    return true;
}

?>
