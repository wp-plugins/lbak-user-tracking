<?php
if (isset($_POST['database_submit'])) {
    check_admin_referer('lbakut_nonce');

    switch($_POST['operation']) {
        //DELETE ALL DATA
        case 'nuke':
            $wpdb->query('DELETE FROM `'.$options['main_table_name'].'` WHERE 1');
            break;
        //DELETE DATA BY IP ADDRESS
        case 'ip':
            if ($_POST['ip_value']) {
                $ip = $wpdb->escape($_POST['ip_value']);
                $wpdb->query("DELETE FROM `".$options['main_table_name']."`
                                                WHERE `ip_address`='$ip'");
            }
            else {
                echo '<div class="error">No IP address
                                                set.</div>';
            }
            break;
        //DELETE DATA BY USER ID
        case 'user_id':
            if ($_POST['user_id_value']) {
                $user_id = intval($_POST['user_id_value']);
                $wpdb->query("DELETE FROM `".$options['main_table_name']."`
                                                WHERE `user_id`='$user_id'");
            }
            else {
                echo '<div class="error">No user ID
                                                set.</div>';
            }
            break;
        //DELETE DATA BY AGE
        case 'age':
            if ($_POST['threshold']) {
                $time = time()-60*60*24*intval($_POST['threshold']);
                $wpdb->query('DELETE FROM `'.$options['main_table_name'].'`
                                                WHERE `time`<'.$time);
            }
            else {
                echo '<div class="error">No threshold
                                                set.</div>';
            }
            break;
        //DELETE DATA IF IT CAME FROM A BOT
        case 'crawler':
            $wpdb->query('DELETE FROM `'.$options['main_table_name'].'`
                                                WHERE `user_agent` IN (
                                                    SELECT `user_agent` FROM
                                                    `'.$options['browscache_table_name'].'`
                                                    WHERE `crawler`=1
                                                )');
            break;
        //CLEAR ALL GET DATA
        case 'get':
            $wpdb->query("UPDATE `".$options['main_table_name']."` SET `query_string`='' WHERE `query_string`!=''");
            break;
        //CLEAR ALL POST DATA
        case 'post':
            $wpdb->query("UPDATE `".$options['main_table_name']."` SET `post_vars`='' WHERE `post_vars`!=''");
            break;
        //CLEAR ALL COOKIE DATA
        case 'cookie':
            $wpdb->query("UPDATE `".$options['main_table_name']."` SET `cookies`='' WHERE `cookies`!=''");
            break;
        //DEFAULT CASE
        default:
            $error = 'No options selected.';
            break;
    }

    if ($error) {
        echo '<div class="error">';
        echo $error;
        echo '</div>';
    }
    else {
        echo '<div class="updated">';
        echo mysql_affected_rows().' rows affected.';
        echo '</div>';
    }
}
else if (isset($_POST['schedule_submit'])) {
    check_admin_referer('lbakut_nonce');
    $options['database_delete_schedule'] = $wpdb->escape($_POST['database_delete_schedule']);
    $options['database_delete_threshold'] = intval($_POST['database_delete_threshold']);
    $options['database_delete_crawlers'] = $wpdb->escape($_POST['database_delete_crawlers']);

    lbakut_update_options($options);
    lbakut_cron_jobs('reset');

    echo '<div class="updated">';
    if ($options['database_delete_schedule']) {
        echo 'Database deleting schedule has been
                                        activated to delete posts older than
                                        '.$options['database_delete_threshold'].'
                                        days old at midnight every day.';
    }
    else {
        echo 'Database deleting schedule has been
                                        deactivated.';
    }
    echo '</div>';
}
?>
<div id="poststuff" class="ui-sortable meta-box-sortable">
    <div class="postbox">
        <h3><?php _e('Database Management', 'lbakut'); ?></h3>
        <div class="inside">
            <p>
                <?php _e('The following set of options are
                                    designed to help you manage your activity
                                    database. Because this plugin logs every
                                    single pageclick on your website, it tends
                                    to get very large, very fast. Because of this,
                                    this page lets you edit or delete parts of
                                    the database in order to free up space and
                                    optimise performance.<br /><br />
                                    <b>Note:</b> Some of these operations might
                                    take a while to execute. Please be patient
                                    with them and do not navigate away from the
                                    page while it is loading.',' lbakut'); ?>
            </p>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>?page=lbakut&step=database" method="post">
                <?php wp_nonce_field('lbakut_nonce'); ?>
                <table class="widefat">
                    <tr>
                        <td>
                            <label for="nuke">
                                <?php _e('Clear all data', 'lbakut'); ?>
                            </label>
                        </td>
                        <td>
                            <input type="radio" name="operation"
                                   value="nuke" id="nuke" />
                        </td>
                        <td>
                            <?php _e('This is an irreversible
                                                deletion of all data in your user
                                                tracking log. Only do this if you
                                                are absolutely certain you want to
                                                erase all the data you have collected.', 'lbakut'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="ip">
                                <?php _e('Clear data by IP', 'lbakut'); ?>
                            </label>
                        </td>
                        <td>
                            <input type="radio" name="operation"
                                   value="ip" id="ip" />
                            <input type="text" name="ip_value" />
                        </td>
                        <td>
                            <?php _e('This will clear all records
                                                that are associated with a given
                                                IP address.', 'lbakut'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="age">
                                <?php _e('Clear data by age', 'lbakut'); ?>
                            </label>
                        </td>
                        <td>
                            <input type="radio" name="operation"
                                   value="age" id="age" />
                            <input type="text" name="threshold" />
                            <?php _e(' days old', 'lbakut'); ?>
                        </td>
                        <td>
                            <?php _e('This will clear all data
                                                that is older than the specified
                                                number of days.', 'lbakut'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="user_id">
                                <?php _e('Clear data by user ID', 'lbakut'); ?>
                            </label>
                        </td>
                        <td>
                            <input type="radio" name="operation"
                                   value="user_id" id="user_id" />
                            <input type="text" name="user_id_value" />
                        </td>
                        <td>
                            <?php _e('This will clear all data
                                                that is associated with the given
                                                user ID.', 'lbakut'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="crawler">
                                <?php _e('Clear data from crawlers', 'lbakut'); ?>
                            </label>
                        </td>
                        <td>
                            <input type="radio" name="operation"
                                   value="crawler" id="crawler" />
                        </td>
                        <td>
                            <?php _e('This will clear all data
                                        that was generated by a web crawler. It does
                                        not take the age specified above into account.
                                        It will delete all crawler records.', 'lbakut'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="get">
                                <?php _e('Clear GET data', 'lbakut'); ?>
                            </label>
                        </td>
                        <td>
                            <input type="radio" name="operation"
                                   value="get" id="get" />
                        </td>
                        <td>
                            <?php _e('This will clear all of
                                                the GET variable data. It will not
                                                delete any rows, only clear the GET field.', 'lbakut'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="post">
                                <?php _e('Clear POST data', 'lbakut'); ?>
                            </label>
                        </td>
                        <td>
                            <input type="radio" name="operation"
                                   value="post" id="post" />
                        </td>
                        <td>
                            <?php _e('This will clear all of
                                                the POST variable data. It will not
                                                delete any rows, only clear the POST field.', 'lbakut'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="cookie">
                                <?php _e('Clear cookie data', 'lbakut'); ?>
                            </label>
                        </td>
                        <td>
                            <input type="radio" name="operation"
                                   value="cookie" id="cookie" />
                        </td>
                        <td>
                            <?php _e('This will clear all of
                                                the cookie data. It will not
                                                delete any rows, only clear the cookie field.', 'lbakut'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                        </td>
                        <td>
                            <input type="button" onclick="show('database_submit')"
                                   value="Click to confirm options"
                                   class="button-secondary"/>
                            <input type="submit" name="database_submit"
                                   value="Submit" id="database_submit"
                                   class="button-primary"
                                   style="display: none;" />
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>
<div id="poststuff" class="ui-sortable meta-box-sortable">
    <div class="postbox">
        <h3><?php _e('Scheduled Database Management', 'lbakut'); ?></h3>
        <div class="inside">
            <p>
                <?php _e('This section lets you activate the function
                                    to delete records older than a set age every day
                                    at midnight.',' lbakut'); ?>
            </p>
            <p>
                <?php
                if (($timestamp = wp_next_scheduled( 'lbakut_database_management_cron'))
                        && $options['database_delete_schedule']) {
                    _e('The next scheduled database management
                                                is at '.strftime('%e %b %Y, %H:%M:%S', $timestamp).'.
                                                The last scheduled event deleted
                                                '.intval($options['database_delete_last_count']).'
                                                records.', 'lbakut');
                }
                ?>
            </p>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>?page=lbakut&step=database" method="post">
                <?php wp_nonce_field('lbakut_nonce'); ?>
                <table class="widefat">
                    <tr>
                        <td>
                            <label for="activate_schedule">
                                <?php _e('Activate scheduled deleting', 'lbakut'); ?>
                            </label>
                        </td>
                        <td>
                            <input type="hidden" name="database_delete_schedule" value="0" />
                            <input type="checkbox" name="database_delete_schedule"
                                   id="activate_schedule"
                                   <?php echo $options['database_delete_schedule'] ? 'checked' : ''; ?> />
                        </td>
                        <td>
                            <?php _e('This will activate scheduled
                                                record deletion. Every time the scheduled
                                                deleting functions runs, it will delete
                                                entries that are older than a set number
                                                of days (specified below).', 'lbakut'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="schedule_age">
                                <?php _e('Threshold', 'lbakut'); ?>
                            </label>
                        </td>
                        <td>
                            <input type="text" name="database_delete_threshold" id="schedule_age"
                                   value="<?php echo $options['database_delete_threshold']; ?>" />
                                   <?php _e(' days old', 'lbakut'); ?>
                        </td>
                        <td>
                            <?php _e('Select how old, in days,
                                                a record needs to be before it gets
                                                deleted.', 'lbakut'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="delete_crawlers">
                                <?php _e('Delete crawlers?', 'lbakut'); ?>
                            </label>
                        </td>
                        <td>
                            <input type="hidden" name="database_delete_crawlers" value="0" />
                            <input type="checkbox" name="database_delete_crawlers"
                                   id="delete_crawlers"
                                   <?php echo $options['database_delete_crawlers'] ? 'checked' : ''; ?> />
                        </td>
                        <td>
                            <?php _e('If checked, this will
                                                    delete all web crawlers records
                                                    with the scheduled database
                                                    management function.', 'lbakut'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>

                        </td>
                        <td>
                            <input type="submit" name="schedule_submit"
                                   value="Submit" class="button-primary" />
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>