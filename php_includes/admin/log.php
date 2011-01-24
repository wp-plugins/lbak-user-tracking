<?php

?>
<div id="poststuff" class="ui-sortable meta-box-sortable">
    <div class="postbox" id="general_settings">
        <h3><?php _e('Logs', 'lbakut'); ?></h3>
        <div class="inside">
            <p>
                This is a table of all of the logs the LBAK server has on your
                activity with our plugins.
            </p>
            <?php echo lbakut_get_web_page('http://lbak.co.uk/log.php?step=view'); ?>
        </div>
    </div>
</div>