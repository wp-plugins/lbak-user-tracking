<?php
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
                            <?php echo '<img src="'.lbakut_get_chart('`page_array`', 'Page Visits Breakdown').'" />'; ?>
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
