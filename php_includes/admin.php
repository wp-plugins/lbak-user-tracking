<?php
/*
 * This function displays the admin panel menu. All options etc. that you want
 * to include in this plugin have to go in here.
*/
function lbakut_menu_options() {
    //Check that the user is able to view this page.
    if (!current_user_can('manage_options')) {
        wp_die( __('You do not have sufficient permissions to access this page.') );
    }

    //Declare global variables.
    global $wpdb;

    //Get lbakut options.
    $options = lbakut_get_options();

    ?>
<div class="wrap">
    <h2>LBAK User Tracking</h2>
    <div id="navigation">
        <a class="button-secondary" href="?page=lbakut">Settings</a>
        <a class="button-secondary" href="?page=lbakut&step=displaysettings">Display Settings</a>
        <a class="button-secondary" href="?page=lbakut&step=search">Search</a>
        <a class="button-secondary" href="?page=lbakut&step=stats">Stats</a>
        <a class="button-secondary" href="?page=lbakut&step=userstats">User Stats</a>
        <a class="button-secondary" href="?page=lbakut&step=database">Database Management</a>
        <a class="button-secondary" href="?page=lbakut&step=filemanagement">File Management</a>
        <a class="button-secondary" href="?page=lbakut&step=help">Help/FAQ</a>
        <?php //echo $options['log'] ? '<a class="button-secondary" href="?page=lbakut&step=log">Logs</a>': ''; ?>
        <a class="button-secondary" href="http://donate.lbak.co.uk/" target="_blank">Donate <3</a>
    </div>
    <br />
        <?php

        switch ($_GET['step']) {
            case 'stats':
                require_once 'admin/stats.php';
                break;

            case 'help':
                require_once 'admin/help.php';
                break;
            
            case 'displaysettings':
                require_once 'admin/displaysettings.php';
                break;

            case '':
            case 'mainsettings':
                require_once 'admin/mainsettings.php';
                break;

            case 'search':
                require_once 'admin/search.php';
                break;

            case 'database':
                require_once 'admin/dbmanagement.php';
                break;

            case 'log':
                require_once 'admin/log.php';
                break;

            case 'test':
                require_once 'admin/test.php';
                break;

            case 'userstats':
                require_once 'admin/userstats.php';
                break;

            case 'filemanagement':
                require_once 'admin/filemanagement.php';
                break;

            default:
                break;
        }
        ?>
</div>
<?php
}
?>
