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
        <a class="button-secondary" href="?page=lbakut&step=search">Search</a>
        <a class="button-secondary" href="?page=lbakut&step=stats">Stats</a>
        <a class="button-secondary" href="?page=lbakut&step=database">Database Management</a>
        <a class="button-secondary" href="?page=lbakut&step=help">Help/FAQ</a>
        <a class="button-secondary" href="http://donate.lbak.co.uk/">Donate <3</a>
    </div>
    <br />
        <?php

        switch ($_GET['step']) {
            case 'stats':
                if ($_GET['update'] == 1) {
                    lbakut_do_cache_and_stats();
                }
                if ($_GET['addwidget']) {
                    $options['stats_'.$_GET['addwidget'].'_widget'] = true;
                    lbakut_update_options($options);
                }
                else if ($_GET['removewidget']) {
                    $options['stats_'.$_GET['removewidget'].'_widget'] = false;
                    lbakut_update_options($options);
                }
                ?>
    <div id="poststuff" class="ui-sortable meta-box-sortable">
        <div class="postbox">
            <h3><?php _e('LBAK User Tracking: Statistics', 'lbakut'); ?></h3>
            <div class="inside">
                            <?php $stats = lbakut_get_latest_stats(); ?>
                <p>
                                <?php _e('This page displays a range of statistics drawn from
                        the LBAK User Tracking plugin. The stats are updated at
                        midnight every day but you can do a manual update if you
                        want to using the update button below. Keep in mind that
                        this can take a few minutes.', 'lbakut'); ?>
                </p>
                <br />
                <ul>
                    <li><b>Stats last updated:</b> <?php echo strftime('%l:%M%p, %e %B %Y', $stats->time); ?>
                        <a href="?page=lbakut&step=stats&update=1" class="button-secondary">Update Now</a></li>
                    <li><b>Total pageclicks:</b> <?php echo $stats->rows; ?></li>
                    <li><b>Unique IP addresses:</b> <?php echo $stats->unique_ips; ?></li>
                    <li><b># of Different Browsers:</b> <?php echo $stats->user_agents; ?></li>
                </ul>
                <h2 style="margin: 0; display: inline;">Browser Breakdown</h2>
                            <?php
                            if ($options['stats_browser_widget']) {
                                echo '<a href="?page=lbakut&step=stats&removewidget=browser"
                        class="button-secondary">Remove this dashboard widget</a><br />';
                            }
                            else {
                                echo '<a href="?page=lbakut&step=stats&addwidget=browser"
                        class="button-secondary">Add this as a dashboard widget</a><br />';
                            }
                            ?>
                            <?php _e('The following pie chart shows the breakdown of
                    browser usage per page click on your blog. It does not
                    take into account crawlers (search engine spiders etc.)',
                                    'lbakut'); ?>
                <br />
                            <?php echo '<img src="'.lbakut_get_chart('`browser_array`', 'Browser Breakdown').'" />'; ?>
                <br />
                <h2 style="margin: 0; display: inline;">Page Visits Breakdown</h2>
                            <?php
                            if ($options['stats_pageviews_widget']) {
                                echo '<a href="?page=lbakut&step=stats&removewidget=pageviews"
                        class="button-secondary">Remove this dashboard widget</a><br />';
                            }
                            else {
                                echo '<a href="?page=lbakut&step=stats&addwidget=pageviews"
                        class="button-secondary">Add this as a dashboard widget</a><br />';
                            }
                            ?>
                            <?php _e('The following pie chart shows the breakdown of
                    page clicks on your blog. It does not
                    take into account crawlers (search engine spiders etc.)', 'lbakut'); ?>
                <br />
                            <?php echo '<img src="'.lbakut_get_chart('`script_name_array`', 'Page Visits Breakdown').'" />'; ?>
                <br />
                <h2 style="margin: 0; display: inline;">OS Breakdown</h2>
                            <?php
                            if ($options['stats_os_widget']) {
                                echo '<a href="?page=lbakut&step=stats&removewidget=os"
                        class="button-secondary">Remove this dashboard widget</a><br />';
                            }
                            else {
                                echo '<a href="?page=lbakut&step=stats&addwidget=os"
                        class="button-secondary">Add this as a dashboard widget</a><br />';
                            }
                            ?>
                            <?php _e('The following pie chart shows the breakdown of
                    operating system usage per page click on your blog. It does not
                    take into account crawlers (search engine spiders etc.)', 'lbakut'); ?>
                <br />
                            <?php echo '<img src="'.lbakut_get_chart('`platform_array`', 'OS Breakdown').'" />'; ?>
                <br />
                <h2 style="margin: 0;">More Browser Stats</h2>
                            <?php


                            _e('The following list shows a range of further statistics
                    on user browser capabilities:<br /><br />', 'lbakut');

                            ?>
                <ul>
                    <li><b>% Browsers Recognised:</b> <?php echo lbakut_percent($stats->recognised, $stats->rows).'%'; ?></li>
                    <li><b>% is Crawler:</b> <?php echo lbakut_percent($stats->crawler, $stats->recognised).'%'; ?></li>
                    <li><b>% Mobile Device:</b> <?php echo lbakut_percent($stats->ismobiledevice, $stats->recognised).'%'; ?></li>
                    <li><b>% Feed Reader:</b> <?php echo lbakut_percent($stats->issyndicationreader, $stats->recognised).'%'; ?></li>
                    <li><b>% use Javascript:</b> <?php echo lbakut_percent($stats->javascript, $stats->recognised).'%'; ?></li>
                    <li><b>% support CSS:</b> <?php echo lbakut_percent($stats->supportscss, $stats->recognised).'%'; ?></li>
                    <li><b>% support Frames:</b> <?php echo lbakut_percent($stats->frames, $stats->recognised).'%'; ?></li>
                    <li><b>% support Iframes:</b> <?php echo lbakut_percent($stats->iframes, $stats->recognised).'%'; ?></li>
                    <li><b>% use Cookies:</b> <?php echo lbakut_percent($stats->cookies, $stats->recognised).'%'; ?></li>
                </ul>
                <br />
                <br />
            </div>
        </div>
    </div>
                <?php
                break;
            case 'help':
                ?>
    <div id="poststuff" class="ui-sortable meta-box-sortable">
        <div class="postbox" id="top">
            <h3><?php _e('I\'m stuck and I need help!', 'lbakgc'); ?></h3>
            <div class="inside">
                            <?php _e('The following page is designed to answer any and all
                    queries you might have about the LBAK User Tracking plugin.
                    Click on any of the topics below to begin:
                    <br /><br />
                    <a href="#setup">How do I set up the plugin?</a><br /><br />
                    <a href="#shortcode">What is a shortcode and how do I use one?</a><br /><br />
                    <a href="#mulah">You\'re awesome. Can I give you some money?</a><br /><br />
                    <a href="#get">What are GET variables?</a><br /><br />
                    <a href="#post">What are POST variables?</a><br /><br />
                    <a href="#nomnom">What are Cookies when they aren\'t a delicious snack?</a><br /><br />
                    <a href="#referrer">What is a referrer?</a><br /><br />
                    <a href="#user_agent">What is a user agent?</a><br /><br />
                    <a href="#ip_address">What is an ip address and why do you have a "real" one?</a><br /><br />
                    <a href="#help">I\'m still confused :(</a><br /><br />
                    <br /><br /><br />
                    <div id="setup">
                    <b>How do I set up the plugin?</b>
                    <br /><br />
                    There really isn\'t any setting up to be done. Once installed
                    the plugin will happily chug along on its own without any need
                    for configuration. You can, however, use the somewhat extensive
                    settings in the Settings menu to customise your experience with
                    the LBAK User Tracking plugin. They all have explanations of what
                    they do associated with them. If you are confused by any of the
                    options, however, drop me an email and I\'ll work on improving
                    the documentation :) samwho@lbak.co.uk
                    <br /><br />
                    <a href="#top">Back to top</a>
                    <br /><br />
                    </div>
                    <div id="shortcode">
                    <b>What is a shortcode and how do I use one?</b>
                    <br /><br />
                    A shortcode is a bit of text you can put in your posts that get
                    filtered and changed into something useful. For example, a lot of
                    forums use "bbcode" which stands for bulletin board code, which is
                    a type of short code. They have tags like [b] for bold, [i] for
                    italic and so on.
                    <br /><br />
                    If you have the setting enabled, LBAK User Tracking allows you
                    to post the 3 pie charts found in the stats section to your
                    blog (they will update dynamically, too). Here are some examples
                    of how to use the shortcodes for you:
                    <blockquote>
                    [lbakut type="browser" title="Browser Stats" width="600" height="300"]
                    </blockquote>
                    Most of the syntax is self explanatory. The width and height are
                    in pixels, they are optional and will default to 700x350 if
                    left out and you can\'t exceed 300,000 total pixels in the image due to
                    restrictions on the Google Charts API so make sure width*height is less
                    than 300,000.
                    <br /><br />
                    The "type" attribute can be one of three values: "browser",
                    "os" or "pageviews" for each of the charts respectively. This is
                    the only mandatory attribute.
                    <br /><br />
                    The "title" attribute is the heading on the image. If left blank
                    no title will be shown.
                    <br /><br />
                    <a href="#top">Back to top</a>
                    <br /><br />
                    </div>
                    <div id="mulah">
                    <b>You\'re awesome. Can I give you some money?</b>
                    <br /><br />
                    You\'re too kind! Of course you can, there is a Donate link
                    at the top of this page that will take you to a page with a
                    link to my PayPal on it. Donations are greatly appreciated
                    and will find you a place in my heart. Right next to my love of
                    programming and The Big Bang Theory.
                    <br /><br />
                    <a href="#top">Back to top</a>
                    <br /><br />
                    </div>
                    <div id="get">
                    <b>What are GET variables?</b>
                    <br /><br />
                    GET variables refer to variables that are sent to a web server
                    in the URL. If you\'re somewhat new to all of this computer lark,
                    I will do my best to explain:
                    <br /><br />
                    A URL is a "Uniform resource locator". You use them every single
                    time you browse the web, here are some examples of a URL:
                    http://lbak.co.uk, http://facebook.com, http://google.com. They\'re
                    web addresses, if you want. Sometimes you will see a url like this:
                    http://lbak.co.uk?var1=hello. The question mark initiates the
                    start of the GET variables and the ampersand symbol (&) separates them.
                    They are simple key=value pairs that get passed to the web server
                    to help make content more dynamic and catered to you.
                    <br /><br />
                    In the LBAK User Tracking plugin these key=value pars will
                    be displayed to you without the question marks or the
                    ampersands. Just a list of key=value pairs that got sent in
                    that page click.
                    <br /><br />
                    <a href="#top">Back to top</a>
                    <br /><br />
                    </div>
                    <div id="post">
                    <b>What are POST variables?</b>
                    <br /><br />
                    POST variables are very similar to GET variables except that
                    they are not passed in the URL and they have less limitations.
                    GET variables, for examples, are subject to length limits. I\'m
                    not sure of the exact numbers off the top of my head but POST
                    variables are a lot less limited in length (to the point where
                    they are considered more or less unlimited).
                    <br /><br />
                    Form data is very often submitted to web pages as POST data.
                    <br /><br />
                    <a href="#top">Back to top</a>
                    <br /><br />
                    </div>
                    <div id="nomnom">
                    <b>What are Cookies when they are not a delicious snack?</b>
                    <br /><br />
                    Ahh, cookies. There are a lot of misconceptions and myths about
                    cookies. Here\'s what they really are:
                    <br /><br />
                    When your web browser accesses a web page, that webpage can
                    ask your web browser to save some information on your hard drive
                    for future reference. Don\'t worry, as far as I am aware there
                    aren\'t currently any ways to install virusses using cookies
                    but they can be used to kind of track your internet usage.
                    <br /><br />
                    Your web browser will have a folder deep inside your hard drive
                    somewhere that contains a whole host of small text files containing
                    what will most likely look like gibberish. Very often websites will
                    want to keep what is called "session data" on you (data stored until
                    you close your browser or are inactive for a certain amount of time,
                    usually 15 minutes), shopping sites do this to store baskets or
                    recommend items that would interest you. Session data is saved
                    as a long, seemingly meaningless string of characters with the key
                    "PHPSESSID" or something like that (it will differ if the site
                    does not use PHP as a scripting language or decides to specify
                    their own names for sessions). This is actually just a unique
                    identifier for the server to recognise and retrive the right
                    data for.
                    <br /><br />
                    In conclusion, cookies are just variables that web pages want
                    to save that last through browser sessions. They tend not to
                    be malicious and there is no real reason to be scared of them.
                    <br /><br />
                    <a href="#top">Back to top</a>
                    <br /><br />
                    </div>
                    <div id="referrer">
                    <b>What is a referrer?</b>
                    <br /><br />
                    A referrer is something sent by your browser to tell the next
                    web page where you came from. It is an optional thing, though,
                    and some browsers choose not to send it or they send you misinformation.
                    It isn\'t recommended to trust it.
                    <br /><br />
                    <a href="#top">Back to top</a>
                    <br /><br />
                    </div>
                    <div id="user_agent">
                    <b>What is a user agent?</b>
                    <br /><br />
                    User Agent is another piece of information that your browser
                    sends to web pages. It is a unique string that identifies
                    what web browser and operating system you are using. There is
                    a project called the "PHP Browscap" which aims to try and
                    identify the browser capabilities of every browser out there
                    and they are doing a really good job. LBAK User Tracking uses
                    the PHP Browscap technology (automatically downloads updates
                    every week for you, too, so you\'ve always got the up to date
                    info on web browsers) that is worked on by a guy called Gary Keith
                    and can be found here: <a href="http://code.google.com/p/phpbrowscap/" target="_blank">
                    http://code.google.com/p/phpbrowscap/</a>.
                    <br /><br />
                    Note: LBAK User Tracking only uses his definitions file, not his
                    class file. Plans to use his class file may formulate at some point
                    in the future.
                    <br /><br />
                    <a href="#top">Back to top</a>
                    <br /><br />
                    </div>
                    <div id="ip_address">
                    <b>What is an ip address and why do you have a "real" one?</b>
                    <br /><br />
                    IP address stands for Internet Protocol Address. Before routers
                    it would uniquely identify your computer for a short period of time but now
                    it will identify you as anyone connecting through the same router as you.
                    This causes some interesting problems on large networks such as schools and
                    Universities.
                    <br /><br />
                    An IP address consists of 4 "octets" (sections ranging from
                    0 to 255). A sample IP address would be 173.194.36.104, which is
                    Google (go on, put it into your web browser if you don\'t believe me).
                    It is how both clients and servers are identified on the web. Clients
                    are likely to change their IP addresses from time to time (it just happens)
                    but servers tend to stick with a "static" ip address that tends not to
                    change.
                    <br /><br />
                    The reason that there is a column called "real" ip address is
                    because of something called a "proxy". A proxy will attempt to
                    mask your IP address from web servers so you can browse the
                    internet anonymously. Some of these proxies, however, will send
                    your real IP address in a subtle way. LBAK User Tracking checks
                    for the tell tale signs of proxy usage and attempts to log the
                    "real" ip address of the user. The field will generally be empty,
                    though, because most proxies don\'t reveal your real IP address
                    so I wouldn\'t hold out much hope for catching those pesky proxy
                    using spammers. Attempts are being made to find better methods of
                    detecting real IP addresses, though, and may be released into
                    this plugin in future.
                    <br /><br />
                    <a href="#top">Back to top</a>
                    <br /><br />
                    </div>
                    <div id="help">
                    <b>I\'m still confused :(</b>
                    <br /><br />
                    If the above help has not... well... helped you then you are
                    welcome to email me: samwho@lbak.co.uk and I will try my best
                    to get back to you asap :)
                    <br /><br />
                    <a href="#top">Back to top</a>
                    <br /><br />
                    </div>
                    <br /><br /><br /><br /><br /><br />
                    <br /><br /><br /><br /><br /><br />', 'lbakut'); ?>
            </div>
        </div>
    </div>
                <?php
                break;
            case '':
            case 'settings':
                ?>
    <div id="top">
        <a href="#widget_settings" class="button-primary">Widget Settings</a>
        <a href="#tracker_settings" class="button-primary">Tracker Settings</a>
        <a href="#search_settings" class="button-primary">Search Settings</a>
        <a href="#stats_settings" class="button-primary">Stats Settings</a>
    </div>
                <?php

                //If the form is submitted, process it with the following.
                if ($_POST['widget_submit'] == 'Submit') {
                    check_admin_referer('lbakut_nonce');
                    unset($_POST['widget_submit']);
                    $options['widget_ignore_admin'] = $wpdb->escape($_POST['widget_ignore_admin']);
                    $options['widget_no_to_display'] = $_POST['widget_no_to_display'] ? intval($_POST['widget_no_to_display']) : 10;

                    if ($_POST['widget_ignore_ip'] != 'Add ignored IP addresses in here.') {
                        $options['widget_ignored_ips'] = explode("\n", $wpdb->escape($_POST['widget_ignore_ip']));
                        if (!is_array($options['widget_ignored_ips'])) {
                            $options['widget_ignored_ips'] = array(0 => '');
                        }
                    }

                    if ($_POST['widget_ignore_user'] != 'Add ignored user IDs in here.') {
                        $options['widget_ignored_users'] = explode("\n", $wpdb->escape($_POST['widget_ignore_user']));
                        if (!is_array($options['widget_ignored_users'])) {
                            $options['widget_ignored_users'] = array(0 => '');
                        }
                    }

                    foreach ($_POST as $k => $v) {
                        if (preg_match('/widget_show/',$k)) {
                            $options[$k] = $wpdb->escape($_POST[$k]);
                        }
                    }

                    lbakut_update_options($options);

                    $updated = '<div class="updated">LBAK User Tracking Widget Options Updated!</div>';
                }
                else if ($_POST['track_submit'] == 'Submit') {
                    check_admin_referer('lbakut_nonce');
                    unset($_POST['track_submit']);
                    foreach ($_POST as $k => $v) {
                        if (preg_match('/track_/',$k)) {
                            $options[$k] = $wpdb->escape($_POST[$k]);
                        }
                    }
                    $options['delete_on_uninstall'] = $wpdb->escape($_POST['delete_on_uninstall']);
                    lbakut_update_options($options);
                    $updated = '<div class="updated">LBAK User Tracking Tracker Options Updated!</div>';
                }
                else if ($_POST['search_submit'] == 'Submit') {
                    check_admin_referer('lbakut_nonce');
                    unset($_POST['search_submit']);
                    foreach ($_POST as $k => $v) {
                        if (preg_match('/search_/',$k)) {
                            $options[$k] = $wpdb->escape($_POST[$k]);
                        }
                    }
                    lbakut_update_options($options);
                    $updated = '<div class="updated">LBAK User Tracking Search Options Updated!</div>';
                }
                else {
                    $updated = '';
                }

                //Get the checkbox setting for the ignore_admin box.
                if ($options['widget_ignore_admin']) {
                    $widget_ignore_admin_checked = 'checked';
                }
                else {
                    $widget_ignore_admin_checked = '';
                }

                if ($options['delete_on_uninstall'] == true) {
                    $delete_on_uninstall_checked = 'checked';
                }
                else {
                    $delete_on_uninstall_checked = '';
                }

                //This is to keep the implode() function happy.
                if ($options['widget_ignored_ips'] == '') {
                    $options['widget_ignored_ips'] = array(0 => 'Add ignored IP addresses in here.');
                }
                if ($options['widget_ignored_users'] == '') {
                    $options['widget_ignored_users'] = array(0 => 'Add ignored user IDs in here.');
                }

                ?>
    <br />
                <?php echo $updated; ?>
    <div id="poststuff" class="ui-sortable meta-box-sortable">
        <div class="postbox" id="widget_settings">
            <h3><?php _e('LBAK User Tracking: Widget Settings', 'lbakut'); ?></h3>
            <div class="inside">
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>?page=lbakut#widget_settings" method="post" name="lbakut_widget_form">
                                <?php wp_nonce_field('lbakut_nonce'); ?>
                    <p>
                        The following options edit the display and behaviour of the
                        admin dashboard widget for the LBAK User Tracking plugin.
                        <b>Note that none of these options affect what data gets tracked
                            by the plugin, settings to edit what gets tracked are located
                            further down the page.</b>
                    </p>
                    <table class="widefat">
                        <thead>
                        <th>Setting</th>
                        <th>Options</th>
                        <th>Description</th>
                        </thead>
                        <tr>
                            <td>
                                <label for="widget_ignore_admin">
                                    <b><?php _e('Ignore admin?', 'lbakut'); ?></b>
                                </label>
                            </td>
                            <td>
                                <input type="checkbox" id="widget_ignore_admin"
                                       name="widget_ignore_admin" value="1" <?php echo $widget_ignore_admin_checked; ?> />
                            </td>
                            <td>
                                <p>
                                    Check to ignore admin users in the user tracking widget.
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="widget_ignore_ip">
                                    <b><?php _e('IP Ignore List', 'lbakut'); ?></b>
                                </label>
                            </td>
                            <td>
                                <textarea id="widget_ignore_ip" name="widget_ignore_ip" rows="5" cols="15"><?php echo implode("\n", $options['widget_ignored_ips']); ?></textarea>
                            </td>
                            <td>
                                <p>
                                    If you want to ignore certain IP addresses from the recent visitors
                                    list please put one per line in this text box.
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="widget_ignore_user">
                                    <b><?php _e('User Ignore List', 'lbakut'); ?></b>
                                </label>
                            </td>
                            <td>
                                <textarea id="widget_ignore_user" name="widget_ignore_user" rows="5" cols="15"><?php echo implode("\n", $options['widget_ignored_users']); ?></textarea>
                            </td>
                            <td>
                                <p>
                                    If you want to ignore certain users from the recent visitors
                                    list please put one user ID per line in this text box.
                                    <br />
                                                <?php
                                                wp_dropdown_users();
                                                ?>
                                    <span class="button-secondary" onclick="insert_user_id_from_dropdown()">Add user to list</span>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="widget_no_to_display">
                                    <b><?php _e('Rows to display', 'lbakut'); ?></b>
                                </label>
                            </td>
                            <td>
                                <input type="text" id="widget_no_to_display" name="widget_no_to_display" value="<?php echo $options['widget_no_to_display']; ?>" />
                            </td>
                            <td>
                                <p>
                                    Choose how many rows to display on the dashboard widget.
                                </p>
                            </td>
                        </tr>
                                    <?php echo lbakut_print_options('widget_show_', $options); ?>
                        <tr>
                            <td>
                                <b>Submit</b>
                            </td>
                            <td>
                                <input type="submit" name="widget_submit" class="button-primary" value="Submit" />
                            </td>
                            <td>
                                <p>Submit the form and change the widget options.</p>
                            </td>
                        </tr>
                        <thead>
                        <th>Setting</th>
                        <th>Options</th>
                        <th>Description</th>
                        </thead>
                    </table>
                </form>
            </div>
        </div>
    </div>
    <div id="poststuff" class="ui-sortable meta-box-sortable">
        <div class="postbox" id="tracker_settings">
            <h3><?php _e('LBAK User Tracking: Tracker Settings', 'lbakut'); ?></h3>
            <div class="inside">
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>?page=lbakut#tracker_settings" method="post" name="lbakut_tracker_form">
                                <?php wp_nonce_field('lbakut_nonce'); ?>
                    <p>
                        Below are a variety of options that help you tailor what you
                        track to your needs. If you are uncertain of what something is
                        or does it is advised that you stick to the default setting.<br /><br />
                        It is worth noting that if you turn any of the tracking options off,
                        that bit of information will not be tracked at all and there will be
                        no way of filling in the gaps if you decide to track it again
                        in the future.
                    </p>
                    <table class="widefat">
                        <thead>
                        <th>Setting</th>
                        <th>Options</th>
                        <th>Description</th>
                        </thead>
                        <tr>
                            <td>
                                <b>Delete data on uninstall?</b>
                            </td>
                            <td>
                                <input type="checkbox" id="delete_on_uninstall"
                                       name="delete_on_uninstall" value="1" <?php echo $delete_on_uninstall_checked; ?> />
                            </td>
                            <td>
                                <p>
                                    If this is checked, the lbakut_activity_log table
                                    will be deleted from your database when you uninstall
                                    this plugin. This means that you will lose all of the
                                    information you have collected while using this plugin.
                                    You will also lose all of your preferences.
                                </p>
                            </td>
                        </tr>
                                    <?php echo lbakut_print_options('track_', $options); ?>
                        <tr>
                            <td>
                                <b>Submit</b>
                            </td>
                            <td>
                                <input type="submit" name="track_submit" class="button-primary" value="Submit" />
                            </td>
                            <td>
                                <p>Submit the form and change the tracking options.</p>
                            </td>
                        </tr>
                        <thead>
                        <th>Setting</th>
                        <th>Options</th>
                        <th>Description</th>
                        </thead>
                    </table>
                </form>
            </div>
        </div>
    </div>
    <div id="poststuff" class="ui-sortable meta-box-sortable">
        <div class="postbox" id="search_settings">
            <h3><?php _e('LBAK User Tracking: Search Settings', 'lbakut'); ?></h3>
            <div class="inside">
                <p>
                    The following options determine the style and behaviour of
                    the search page.
                </p>
                <table class="widefat">
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>?page=lbakut#search_settings" method="post">
                                    <?php wp_nonce_field('lbakut_nonce'); ?>
                        <thead>
                        <th>Setting</th>
                        <th>Options</th>
                        <th>Description</th>
                        </thead>
                                    <?php echo lbakut_print_options('search_show_', $options); ?>
                        <tr>
                            <td>
                                <b>Results to show per page</b>
                            </td>
                            <td>
                                <input type="text" name="search_no_to_display" value="<?php echo $options['search_no_to_display']; ?>" />
                            </td>
                            <td>
                                <p>The number of results to show on each page of search results.</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Submit</b>
                            </td>
                            <td>
                                <input type="submit" name="search_submit" class="button-primary" value="Submit" />
                            </td>
                            <td>
                                <p>Submit the form and change the tracking options.</p>
                            </td>
                        </tr>
                        <thead>
                        <th>Setting</th>
                        <th>Options</th>
                        <th>Description</th>
                        </thead>
                    </form>
                </table>
            </div>
        </div>
    </div>

                <?php
                //Stats POST updtaing.
                if (isset($_POST['stats_submit'])) {
                    check_admin_referer('lbakut_nonce');
                    $options['stats_enable_shortcodes'] = $wpdb->escape($_POST['stats_enable_shortcodes']);
                    $options['stats_update_frequency'] = $wpdb->escape($_POST['stats_update_frequency']);
                    $options['stats_browser_widget'] = $wpdb->escape($_POST['stats_browser_widget']);
                    $options['stats_os_widget'] = $wpdb->escape($_POST['stats_os_widget']);
                    $options['stats_pageviews_widget'] = $wpdb->escape($_POST['stats_pageviews_widget']);
                    lbakut_update_options($options);
                    lbakut_cron_jobs('reset');
                    echo '<div class="updated">LBAK User Tracking Stats settings updated!</div>';
                }
                ?>

    <div id="poststuff" class="ui-sortable meta-box-sortable">
        <div class="postbox" id="stats_settings">
            <h3><?php _e('LBAK User Tracking: Stats Settings', 'lbakut'); ?></h3>
            <div class="inside">
                <table class="widefat">
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>?page=lbakut#stats_settings" method="post">
                                    <?php wp_nonce_field('lbakut_nonce'); ?>
                        <thead>
                        <th>Setting</th>
                        <th>Options</th>
                        <th>Description</th>
                        </thead>
                        <tr>
                            <td>
                                <label for="stats_enable_shortcodes">
                                    Enable shortcodes?
                                </label>
                            </td>
                            <td>
                                <input type="hidden" name="stats_enable_shortcodes" value="0" />
                                <input type="checkbox" name="stats_enable_shortcodes"
                                       id="stats_enable_shortcodes" value="1" <?php echo $options['stats_enable_shortcodes'] ? 'checked' : ''; ?> />
                            </td>
                            <td>
                                Check this to enable the parsing of stat based short codes
                                in blog posts. An explanation of this can be found in the
                                Help/FAQ section.
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="stats_update_frequency">
                                    Stats update frequency
                                </label>
                            </td>

                            <td>
                                <select name="stats_update_frequency"
                                        id="stats_update_frequency">
                                                        <?php
                                                        $schedules = wp_get_schedules();
                                                        foreach ($schedules as $k => $v) {
                                                            if ($k == $options['stats_update_frequency']) {
                                                                $next = 'selected';
                                                            }
                                                            else {
                                                                $next = '';
                                                            }
                                                            echo '<option value="'.$k.'" '.$next.'>'.$v['display'].'</option>';
                                                        }
                                                        ?>
                                </select>
                            </td>
                            <td>
                                How often you want the stats section to be updated.
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="stats_browser_widget">
                                    Show Browser stats on Dashboard?
                                </label>
                            </td>
                            <td>
                                <input type="hidden" name="stats_browser_widget" value="0" />
                                <input type="checkbox" name="stats_browser_widget"
                                       id="stats_browser_widget" value="1" <?php echo $options['stats_browser_widget'] ? 'checked' : ''; ?> />
                            </td>
                            <td>
                                Check this to enable the displaying of a dashboard widget
                                that gives you browser statistics.
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="stats_os_widget">
                                    Show os stats on Dashboard?
                                </label>
                            </td>
                            <td>
                                <input type="hidden" name="stats_os_widget" value="0" />
                                <input type="checkbox" name="stats_os_widget"
                                       id="stats_os_widget" value="1" <?php echo $options['stats_os_widget'] ? 'checked' : ''; ?> />
                            </td>
                            <td>
                                Check this to enable the displaying of a dashboard widget
                                that gives you os statistics.
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="stats_pageviews_widget">
                                    Show pageviews stats on Dashboard?
                                </label>
                            </td>
                            <td>
                                <input type="hidden" name="stats_pageviews_widget" value="0" />
                                <input type="checkbox" name="stats_pageviews_widget"
                                       id="stats_pageviews_widget" value="1" <?php echo $options['stats_pageviews_widget'] ? 'checked' : ''; ?> />
                            </td>
                            <td>
                                Check this to enable the displaying of a dashboard widget
                                that gives you page views statistics.
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="stats_submit">
                                    Submit
                                </label>
                            </td>
                            <td>
                                <input type="submit" name="stats_submit" class="button-primary"
                                       id="stats_submit" value="Submit" />
                            </td>
                            <td>
                                Click here to submit your new settings.
                            </td>
                        </tr>
                        <thead>
                        <th>Setting</th>
                        <th>Options</th>
                        <th>Description</th>
                        </thead>
                    </form>
                </table>
            </div>
        </div>
    </div>
    <br /><br /><br /><br />


                <?php
                break;

            case 'search':
                ?>
    <div id="poststuff" class="ui-sortable meta-box-sortable">
        <div class="postbox">
            <h3><?php _e('What are you looking for?', 'lbakut'); ?></h3>
            <div class="inside">
                <p>
                    Below are the fields that you are search in. User comma
                    separated values to search for multiple values. Unfortunately
                    searching on the Browser and OS field is not viable due to the
                    way that they are worked out. A solution to this may be worked
                    out in future but currently it is not an option.
                </p>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
                    <input type="hidden" name="page" value="lbakut" />
                    <input type="hidden" name="step" value="search" />
                    <table class="widefat">
                        <tr>
                            <td>
                                <label for="display_name">
                                                <?php echo __('Display Name', 'lbakut'); ?>
                                </label>
                            </td>
                            <td>
                                            <?php echo lbakut_is_isnt_box('display_name_is'); ?>
                                <input type="text" name="display_name"
                                       id="display_name"
                                       value="<?php echo $_GET['display_name']; ?>" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="user_id">
                                                <?php echo __('User ID', 'lbakut'); ?>
                                </label>
                            </td>
                            <td>
                                            <?php echo lbakut_is_isnt_box('user_id_is'); ?>
                                <input type="text" name="user_id"
                                       id="user_id"
                                       value="<?php echo $_GET['user_id']; ?>"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="user_level">
                                                <?php echo __('User Level', 'lbakut'); ?>
                                </label>
                            </td>
                            <td>
                                            <?php echo lbakut_is_isnt_box('user_level_is'); ?>
                                <input type="text" name="user_level"
                                       id="user_level"
                                       value="<?php echo $_GET['user_level']; ?>"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="ip_address">
                                                <?php echo __('IP Address', 'lbakut'); ?>
                                </label>
                            </td>
                            <td>
                                            <?php echo lbakut_is_isnt_box('ip_address_is'); ?>
                                <input type="text" name="ip_address"
                                       id="ip_address"
                                       value="<?php echo $_GET['ip_address']; ?>"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="real_ip_address">
                                                <?php echo __('Real IP Address', 'lbakut'); ?>
                                </label>
                            </td>
                            <td>
                                            <?php echo lbakut_is_isnt_box('real_ip_address_is'); ?>
                                <input type="text" name="real_ip_address"
                                       id="real_ip_address"
                                       value="<?php echo $_GET['real_ip_address']; ?>"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="script_name">
                                                <?php echo __('Page', 'lbakut'); ?>
                                </label>
                            </td>
                            <td>
                                            <?php echo lbakut_is_isnt_box('script_name_is'); ?>
                                <input type="text" name="script_name"
                                       id="script_name"
                                       value="<?php echo $_GET['script_name']; ?>"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                            <?php echo __('Between', 'lbakut'); ?>
                            </td>
                            <td>
                                <input type="text" name="time_first"
                                       id="time_first" size="5"
                                       value="<?php echo $_GET['time_first']; ?>"/>
                                                   <?php echo lbakut_time_ago_select_box('time_first_multiplier'); ?>
                                                   <?php echo __(' and ', 'lbakut'); ?>
                                <input type="text" name="time_second"
                                       id="time_second" size="5"
                                       value="<?php echo $_GET['time_second']; ?>"/>
                                                   <?php echo lbakut_time_ago_select_box('time_second_multiplier'); ?>
                                                   <?php echo __('ago.', 'lbakut'); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>

                            </td>
                            <td>
                                <input type="submit" value="Search" class="button-primary" />
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
    <div id="poststuff" class="ui-sortable meta-box-sortable">
        <div class="postbox">
            <h3><?php _e('Search results...', 'lbakut'); ?></h3>
            <div class="inside">
                <p>
                    To choose what columns appear on this table please
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=lbakut#search_settings">click here</a>.
                </p>
                            <?php

                            if (!empty($_GET['time_first']) && !empty($_GET['time_second'])) {
                                $time = 'AND `time` BETWEEN '.(time()-(intval($_GET['time_second']) *
                                                        intval($_GET['time_second_multiplier']))).' AND
                                '.(time()-(intval($_GET['time_first']) *
                                                        intval($_GET['time_first_multiplier'])));
                            }
                            else {
                                $time = 'AND 1';
                            }

                            $display_name = lbakut_search_var_prepare('display_name', 'string');
                            $user_id = lbakut_search_var_prepare('user_id', 'int');
                            $user_level = lbakut_search_var_prepare('user_level', 'int');
                            $ip_address = lbakut_search_var_prepare('ip_address', 'string');
                            $real_ip_address = lbakut_search_var_prepare('real_ip_address', 'string');
                            $script_name = lbakut_search_var_prepare('script_name', 'string');

                            $query = "
                                WHERE 1
                                    $time
                                    $display_name
                                    $user_id
                                    $user_level
                                    $ip_address
                                    $real_ip_address
                                    $script_name";

                            //AN EXPLAIN SELECT QUERY FOR INDEX TESTING.
                            /*
                            $explain = $wpdb->get_row('EXPLAIN '.$query, ARRAY_A);

                            echo '<table class="widefat">';
                            echo '<thead>';
                            foreach($explain as $k => $v) {
                                echo '<th>'.$k.'</th>';
                            }
                            echo '</thead>';
                            echo '<tr>';
                            foreach($explain as $k => $v) {
                                echo '<td>'.$v.'</td>';
                            }
                            echo '</tr>';
                            echo '</table>';
                            echo '<br />';
                            */

                            echo lbakut_print_table($query, 'search', $options);

                            break;

                        case 'database':
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
                                    <?php
                                    break;

                                default:
                                    break;
                            }
                            //close div class wrap
                            ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <?php
    }
    ?>
