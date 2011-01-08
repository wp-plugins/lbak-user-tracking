<?php
    function lbakut_generate_user_login_csv($since) {
        $file_name = 'user-logins-'.$since.'-days-'.strftime('%d%m%y%H%M%S', time()).'.csv';
        $file = fopen(lbakut_get_base_dir().'/csv/'.$file_name, 'x');

        if (!$file) {
            echo '<div class="error">Could not create .csv file. Please ensure that
                you have write permission on your server in the csv directory
                of this plugin.</div>';
        }
        else {
            $rows = lbakut_get_users_since($since, true);
            foreach ($rows as $row) {
                if ($row['display_name'] == "") $row['display_name'] = 'Guest / Unregistered';
                fputcsv($file, $row);
            }

            echo '<div class="updated"><p>.csv file created. Download it here:
                <a href="'.lbakut_get_base_url().'/csv/'.$file_name.'">'.$file_name.'</a>
                    (right click and select "Save target as...")</p></div>';
        }
    }

    $since = $_POST['since'] ? $_POST['since'] : 7;

    if ($_POST['csv'] == 'Get CSV') {
        lbakut_generate_user_login_csv($since);
    }

    $rows = lbakut_get_users_since($since);

    $table = '<table class="widefat"><th>User ID</th><th>User Name</th><th>Page Clicks</th>';

    foreach ($rows as $row) {
        $table .= '<tr>';
        $table .= '<td>'.$row->user_id.'</td>';
        $table .= '<td>'.($row->display_name ? $row->display_name : 'Guest / Unregistered').'</td>';
        $table .= '<td>'.$row->count.'</td>';
        $table .= '</tr>';
    }

    $table .= '</table>';
?>
<div id="poststuff" class="ui-sortable meta-box-sortable">
    <div class="postbox">
        <h3><?php _e('LBAK User Tracking: User Statistics', 'lbakut'); ?></h3>
        <div class="inside">
            <?php _e('This section will give you a list of the number of page clicks
                each user has given in a set number of time. The default is the last
                7 days but that can be changed. Currently days are the only input
                method but the form accepts decimal numbers.', 'lbakut'); ?>
            <form action="?page=lbakut&step=userstats" method="post" name="userstats">
            <?php _e('Viewing the users who have logged in in the last ', 'lbakut'); ?>
            <input type="text" name="since" value="<?php echo $since; ?>" />
             days.
            <input type="submit" name="submit" value="Change" class="button-primary"/>
            <input type="submit" name="csv" value="Get CSV" class="button-primary"/>
            </form>
            <br /><br />
            <?php echo $table; ?>
        </div>
    </div>
</div>