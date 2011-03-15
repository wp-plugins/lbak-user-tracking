<?php
set_time_limit(0);
$options = lbakut_get_options();
$rows = $wpdb->get_results('SELECT `id`,`user_agent` FROM `'.$options['main_table_name'].'`');
$brows = lbakut_get_browscap();

foreach ($rows as $row) {
    $id = $row->id;
    $cached = lbakut_get_browscap_from_cache($row->user_agent) ? true : false;
    $browscap = lbakut_browser_info($row->user_agent, $brows);
    $recognised = $browscap ? true : false;
    $crawler = $browscap['crawler'] ? true : false;
    $reader = $browscap['issyndicationreader'] ? true : false;

    echo "$id: ";
    if ($cached) {
        echo '<span style="font-weight: bold; color: green;">Cached.</span> ';
    }
    else {
        echo '<span style="font-weight: bold; color: red;">Cached.</span> ';
    }
    if ($recognised) {
        echo '<span style="font-weight: bold; color: green;">Recognised.</span> ';
    }
    else {
        echo '<span style="font-weight: bold; color: red;">Recognised.</span> ';
    }
    if ($crawler) {
        echo '<span style="font-weight: bold; color: green;">Crawler.</span> ';
    }
    else {
        echo '<span style="font-weight: bold; color: red;">Crawler.</span> ';
    }
    if ($reader) {
        echo '<span style="font-weight: bold; color: green;">Reader.</span> ';
    }
    else {
        echo '<span style="font-weight: bold; color: red;">Reader.</span> ';
    }

    echo $row->user_agent;

    echo '<br />';
}
?>
