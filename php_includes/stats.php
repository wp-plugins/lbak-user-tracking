<?php
function lbakut_do_cache_and_stats() {
    //Run the user stats function first
    lbakut_do_user_stats();
    set_time_limit(0);
    ignore_user_abort(true);
    global $wpdb;
    $options = lbakut_get_options();
    $brows = lbakut_get_browscap();
    $no_of = array();
    $browser_array = array();
    $platform_array = array();
    $script_name_array = array();
    $recognised = 0;

    //$wpdb->show_errors();

    $rows = $wpdb->get_results('SELECT DISTINCT `user_agent`
        FROM `'.$options['main_table_name'].'`');

    $no_of['user_agents'] = sizeof($rows);

    //Cache the user_agent info.
    foreach ($rows as $row) {

        $browscap = lbakut_browser_info($row->user_agent, $brows);

        if ($browscap) {

            $browscap['user_agent'] = $row->user_agent;
            $browscap['tables_'] = $browscap['tables'];
            unset($browscap['tables']);

            $format = array();
            foreach ($browscap as $data) {
                if (is_int($data) || is_bool($data)) {
                    $format[] = '%d';
                }
                else if (is_float($data)) {
                    $format[] = '%f';
                }
                else {
                    $format[] = '%s';
                }
            }
            $wpdb->insert($options['browscache_table_name'], $browscap, $format);
        }
    }

    $no_of['rows'] = $wpdb->get_var('SELECT COUNT(`id`)
        FROM `'.$options['main_table_name'].'`');

    $rows = $wpdb->get_results('SELECT `id`, `user_agent`, `script_name`
        FROM `'.$options['main_table_name'].'`');
    foreach ($rows as $row) {
        $browscap = lbakut_browser_info($row->user_agent, $brows);

        if (!$browscap['crawler']) {
            if(!isset($script_name_array[$row->script_name])) {
                $script_name_array[$row->script_name] = 0;
            }
            $script_name_array[$row->script_name]++;
        }

        if ($browscap) {
            $recognised++;
            foreach ($browscap as $k => $v) {
                if ($k == 'browser') {
                    if (!$browscap['crawler']) {
                        if (!isset($browser_array[$v])) {
                            $browser_array[$v] = 0;
                        }
                        $browser_array[$v]++;
                    }
                }
                else if ($k == 'platform') {
                    if (!$browscap['crawler']) {
                        if (!isset($platform_array[$v])) {
                            $platform_array[$v] = 0;
                        }
                        $platform_array[$v]++;
                    }
                }
                else if ($v == true && strlen($v) < 2
                        && $k != 'majorver' && $k != 'minorver' && $k != 'id') {
                    if (!isset($no_of[$k])) {
                        $no_of[$k] = 0;
                    }
                    $no_of[$k]++;
                }
            }
        }
    }

    $no_of['browser_array'] = serialize($browser_array);
    $no_of['platform_array'] = serialize($platform_array);
    $no_of['script_name_array'] = serialize($script_name_array);
    $no_of['time'] = time();
    $no_of['tables_'] = $no_of['tables'];
    $no_of['recognised'] = $recognised;
    $no_of['unique_ips'] = sizeof($wpdb->get_results('SELECT DISTINCT `ip_address`
        FROM `'.$options['main_table_name'].'`'));
    //Tables is a mysql keyword. Bad times :(
    unset($no_of['tables']);

    //print_r($no_of);

    $wpdb->insert($options['stats_table_name'], $no_of);
    flush();
}

/*
 * Return browscap stats from the cache.
*/
function lbakut_get_browscap_from_cache($user_agent, $options = null) {
    global $wpdb;
    if ($options == null) {
        $options = lbakut_get_options();
    }

    $user_agent = $wpdb->escape($user_agent);

    $row = $wpdb->get_row("SELECT * FROM `".$options['browscache_table_name']."`
        WHERE `user_agent`='$user_agent' LIMIT 1", ARRAY_A);
    //Becuase tables is a mysql keyword, convert it on retrieval.
    if($row) {
        $row['tables'] = $row['tables_'];
        unset($row['tables_']);
        return $row;
    }
    else {
        return false;
    }
}

function lbakut_do_user_stats() {
    set_time_limit(0);
    ignore_user_abort(true);
    global $wpdb;
    $options = lbakut_get_options();
    $unique_ip_array = array();
    $page_views_array = array();
    $last_updated = intval(lbakut_get_stats_last_updated());
    //$wpdb->show_errors();

    $unique_ips = $wpdb->get_results('SELECT DISTINCT `ip_address`
        FROM `'.$options['main_table_name'].'`
        WHERE `time` > '.$last_updated.'');

    foreach ($unique_ips as $row) {
        $first = $wpdb->get_row('SELECT `time` FROM
            `'.$options['main_table_name'].'` WHERE `ip_address`="'.$row->ip_address.'"
                ORDER BY `time` ASC');
        $last = $wpdb->get_row('SELECT `time` FROM
            `'.$options['main_table_name'].'` WHERE `ip_address`="'.$row->ip_address.'"
                ORDER BY `time` DESC');
        $user_agents = $wpdb->get_results('SELECT DISTINCT `user_agent` FROM
            `'.$options['main_table_name'].'` WHERE `ip_address`="'.$row->ip_address.'"
                AND `time` > '.$last_updated.'', ARRAY_N);
        $page_views = $wpdb->get_results('SELECT `script_name`, COUNT(*) as `count`
            FROM `'.$options['main_table_name'].'` WHERE `ip_address`="'.$row->ip_address.'"
                AND `time` > '.$last_updated.'
                GROUP BY `script_name`');
        $user_ids = $wpdb->get_results('SELECT `user_id`, COUNT(*) as `count`
            FROM `'.$options['main_table_name'].'` WHERE `ip_address`="'.$row->ip_address.'"
                AND `time` > '.$last_updated.'
                GROUP BY `user_id`');

        $page_views_array = array();
        foreach ($page_views as $r) {
            $page_views_array[$r->script_name] = intval($r->count);
        }

        $user_ids_array = array();
        foreach ($user_ids as $t) {
            $user_ids_array[$t->user_id] = intval($t->count);
        }

        $unique_ip_array[$row->ip_address]['first_visit'] = $first->time;
        $unique_ip_array[$row->ip_address]['last_visit'] = $last->time;
        $unique_ip_array[$row->ip_address]['user_agents'] = lbakut_array_flatten($user_agents);
        $unique_ip_array[$row->ip_address]['page_views'] = $page_views_array;
        $unique_ip_array[$row->ip_address]['user_ids'] = $user_ids_array;
    }

    foreach ($unique_ip_array as $ip => $row) {
        $exists = $wpdb->get_var('SELECT `ip` FROM `'.$options['user_stats_table_name'].'`
                WHERE `ip`="'.$ip.'"');
        if ($exists) {
            //get the current stats from the database
            $curr = $wpdb->get_row('SELECT * FROM `'.$options['user_stats_table_name'].'` WHERE `ip`="'.$ip.'"', ARRAY_A);
            //unserialize any serialized results
            foreach ($curr as $k => $v) {
                if (unserialize($curr[$k]) != false) {
                    $curr[$k] = unserialize($curr[$k]);
                }
            }
            //merge the current stats to the new ones
            foreach ($row as $k => $v) {
                if ($k != 'first_visit' && $k != 'last_visit') {
                    if (is_array($v) && !empty($v)) {
                        foreach($v as $k2 => $v2) {
                            $row[$k][$k2] += $curr[$k][$k2];
                        }
                    }
                    else if (!empty($v)) {
                        $row[$k] = $curr[$k];
                    }
                }
            }
            //reserialize the results
            foreach ($row as $k => $v) {
                if (is_array($v)) {
                    $row[$k] = serialize($v);
                }
            }
            //Update the row
            $wpdb->update($options['user_stats_table_name'], $row, array('ip' => $ip));
        }
        else {
            $row['ip'] = $ip;
            foreach ($row as $k => $v) {
                if (is_array($v)) {
                    $row[$k] = serialize($v);
                }
            }
            $format = lbakut_get_format_array($row);
            $wpdb->insert($options['user_stats_table_name'], $row, $format);
        }
    }
}

function lbakut_get_format_array($array) {
    $format = array();
    if (!is_array($array)) {
        return false;
    }

    foreach ($array as $v) {
        if (is_numeric($v)) {
            $format[] = '%d';
        }
        else if (is_float($v)) {
            $format[] = '%f';
        }
        else {
            $format[] = '%s';
        }
    }

    return $format;
}

function lbakut_get_chart($stat, $title = null, $width = null, $height = null) {
    $row = lbakut_get_latest_stats($stat, ARRAY_N);

    if ($title == null) {
        $title = '';
    }
    if ($width == null) {
        $width = 750;
    }
    if ($height == null) {
        $height = 300;
    }

    $script_names = unserialize($row[0]);
    array_multisort($script_names, SORT_NUMERIC, SORT_DESC);
    $outof = array_sum($script_names);
    $chd = 't:';
    $chdl = '';
    foreach ($script_names as $k => $v) {
        $percent = lbakut_percent($v, $outof);
        $chd .= $percent.',';
        $chdl .= $k.' ('.$percent.'%)|';
    }
    $chd = rtrim($chd, ',');
    $chdl = rtrim($chdl, '|');

    return "http://chart.apis.google.com/chart?chs=".$width."x".$height."
&cht=p&chtt=$title&chd=".$chd."&chl=".$chdl;
}

function lbakut_percent($number, $outof) {
    if ($outof == 0) {
        return 0;
    }
    return number_format($number/$outof*100, 1);
}

function lbakut_get_latest_stats($rows = '*', $return_type = OBJECT, $options = null) {
    global $wpdb;
    if ($options == null) {
        $options = lbakut_get_options();
    }
    return $wpdb->get_row('SELECT '.$rows.' FROM
            `'.$options['stats_table_name'].'` ORDER BY `time` DESC', $return_type);
}
function lbakut_get_stats_last_updated($options = null) {
    global $wpdb;
    if ($options == null) {
        $options = lbakut_get_options();
    }
    return $wpdb->get_var('SELECT `time` FROM
            `'.$options['stats_table_name'].'` ORDER BY `time` DESC');
}
?>
