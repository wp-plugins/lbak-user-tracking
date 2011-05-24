<?php
//If the form is submitted, process it with the following.
if ($_POST['display_submit'] == 'Submit') {
    check_admin_referer('lbakut_nonce');

    foreach ($_POST as $k => $v) {
        if (preg_match('/(widget_show|search_show_)/',$k)) {
            $options[$k] = $wpdb->escape($_POST[$k]);
        }
    }

    lbakut_update_options($options);

    $updated = '<div class="updated">LBAK User Tracking Display Options Updated!</div>';
}
$display = array(
        'name',
        'ip',
        'real_ip',
        'page',
        'browser',
        'os',
        'referrer',
        'time',
        'get',
        'post',
        'cookies',
);
?>
<br />
<?php echo $updated; ?>
<div id="poststuff" class="ui-sortable meta-box-sortable">
    <div class="postbox" id="display_settings">
        <h3><?php _e('LBAK User Tracking: Display Settings', 'lbakut'); ?></h3>
        <div class="inside">
            <p>
                <?php _e('This page allows you to select which columns you want
                    to be visible on each section of this plugin that shows
                    your records. Please note that records are subject to the
                    tracking settings at the time, this means that if you were
                    not tracking a certain piece of data at a given time, it
                    will show as blank when displayed.', 'lbakut'); ?>
            </p>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>?page=lbakut&step=displaysettings"
                  method="post" name="lbakut_display_form">
                <?php wp_nonce_field('lbakut_nonce'); ?>
                <table class="widefat">
                    <thead>
                    <th><?php _e('Column', 'lbakut'); ?></th>
                    <th><?php _e('Widget', 'lbakut'); ?></th>
                    <th><?php _e('Search Page', 'lbakut'); ?></th>
                    <th><?php _e('Description', 'lbakut'); ?></th>
                    </thead>
                    <?php
                    foreach ($display as $name) {
                        echo lbakut_print_display_option($name);
                    }
                    ?>
                    <tr>
                        <td>

                        </td>
                        <td>
                            <input type="submit" name="display_submit"
                                   value="Submit" class="button-primary" />
                        </td>
                    </tr>
                    <thead>
                    <th><?php _e('Column', 'lbakut'); ?></th>
                    <th><?php _e('Widget', 'lbakut'); ?></th>
                    <th><?php _e('Search Page', 'lbakut'); ?></th>
                    <th><?php _e('Description', 'lbakut'); ?></th>
                    </thead>
                </table>
            </form>
        </div>
    </div>
</div>

<?php
function lbakut_print_display_option($name) {
    $options = lbakut_get_options();

    $widget_box = '<input type="hidden" name="widget_show_'.$name.'"
        value="'.false.'" />';
    $widget_box .= '<input type="checkbox" name="widget_show_'.$name.'"
        value="'.true.'" '.($options['widget_show_'.$name] ? 'checked' : '').'/>';

    $search_box = '<input type="hidden" name="search_show_'.$name.'"
        value="'.false.'" />';
    $search_box .= '<input type="checkbox" name="search_show_'.$name.'"
        value="'.true.'" '.($options['search_show_'.$name] ? 'checked' : '').' />';

    return '<tr><td>'.lbakut_option_translate($name).'</td>
        <td>'.$widget_box.'</td><td>'.$search_box.'</td>
            <td><p>'.lbakut_option_description($name).'</p></td></tr>';
}
?>