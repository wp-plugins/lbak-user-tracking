<?php
if (isset($_POST['submit'])) {
    $success = 0;
    $fail = 0;
    foreach ($_POST['files'] as $file) {
        if (unlink($file)) {
            $success++;
        }
        else {
            $fail++;
        }
    }

    if ($fail > 0) {
        echo '<div class="error">'.$fail.' files failed to delete properly.
            Please ensure that you have write access to the server and try
            again.</div>';
    }
    else {
        echo '<div class="updated">'.$success.' file(s) successfully deleted.</div>';
    }
}


$files = array();

foreach(scandir(lbakut_get_base_dir().'/csv/') as $file) {
    if (preg_match('/csv/', $file)) {
        $files[] = $file;
    }
}

if (sizeof($files) > 0) {
    $table = '<table class="widefat"><tr><th></th><th>File</th><th>Size</th></tr>';
    foreach ($files as $file) {
        $fullpath = lbakut_get_base_dir().'/csv/'.$file;
        $fullurl = lbakut_get_base_url().'/csv/'.$file;
        $table .= '<tr>
                <td>
                <input type="checkbox" name="files[]" value="'.$fullpath.'" />
                </td>
                <td>
                <a href="'.$fullurl.'">'.$file.'</a>
                </td>
                <td>
                '.lbakut_format_filesize(filesize($fullpath)).'
                </td>
                </tr>';
    }

    $table .= '</table>';
}
else {
    $table = null;
}

?>
<div id="poststuff" class="ui-sortable meta-box-sortable">
    <div class="postbox">
        <h3><?php _e('CSV File Management', 'lbakut'); ?></h3>
        <div class="inside">
            <p>
                <?php _e('The following is a list of all of the CSV files
                    that you have generated. If you want to delete any of them,
                    check the checkboxes next to their name and click on the
                    delete button at the bottom of the list.', 'lbakut'); ?>
            </p>

            <p>
            <?php if ($table != null) { ?>
            <form name="file_delete_form" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=lbakut&step=filemanagement">
             <?php echo $table; ?>
            <input type="submit" class="button-primary" name="submit" value="Delete" />
            <? } else { ?>
            <p>
                You have no CSV files.
            </p>
            <? } ?>
            </form>
            </p>
        </div>
    </div>
</div>