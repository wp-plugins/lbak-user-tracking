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
        <h3><?php _e('I\'m stuck and I need help!', 'lbakut'); ?></h3>
        <div class="inside">
            <?php
            if (!($lbak_faq = lbakut_get_web_page('http://lbak.co.uk/faq.php?step=get&tag=lbakut'))) {
                lbakut_log('Server does not support cURL or allow_url_fopen');
                $lbak_faq = 'It seems like your server does not allow PHP
                    access to external sites. Because of this, you cannot view
                    the LBAK User Tracking FAQ inside WordPress. However,
                    you can view it on the LBAK server by clicking the following
                    link:
                    <br /><br />
                    <a href="http://lbak.co.uk/faq.php?step=get&tag=lbakut"
                    target="_blank">LBAK User Tracking FAQ</a>';
            }
            _e($lbak_faq, 'lbakut');
            ?>
        </div>
    </div>
</div>
