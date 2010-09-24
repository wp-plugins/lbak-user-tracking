<?php
if ($_GET['question'] == 'success') {
    echo '<div class="updated">Question submitted successfully!
        Please wait at least a few days for an answer.</div>';
}
else if ($_GET['question'] == 'fail') {
    echo '<div class="error">There was a problem submitting
        your question. Please fill out all of the form fields.</div>';
}
?>
<div id="poststuff" class="ui-sortable meta-box-sortable">
    <div class="postbox" id="top">
        <h3><?php _e('I\'m stuck and I need help!', 'lbakgc'); ?></h3>
        <div class="inside">
            <?php _e(lbakut_get_web_page(
                    'http://lbak.co.uk/faq.php?step=get&tag=lbakut'), 'lbakut'); ?>
        </div>
    </div>
</div>
