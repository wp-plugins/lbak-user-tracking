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
    $page_array = array();
    $recognised = 0;
    $human = 0;

    //$wpdb->show_errors();

    $rows = $wpdb->get_results('SELECT DISTINCT `user_agent`
        FROM `' . $options['main_table_name'] . '`');

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
                } else if (is_float($data)) {
                    $format[] = '%f';
                } else {
                    $format[] = '%s';
                }
            }
            $existing = $wpdb->get_row("SELECT `id` FROM `".$options['browscache_table_name']."`
                WHERE `user_agent`='".$browscap['user_agent']."'");
            if ($existing) {
                $wpdb->update($options['browscache_table_name'], $browscap,
                        array('id' => $existing->id), $format, '%d');
            }
            else {
                $wpdb->insert($options['browscache_table_name'], $browscap, $format);
            }
        }
    }

    $no_of['rows'] = $wpdb->get_var('SELECT COUNT(`id`)
        FROM `' . $options['main_table_name'] . '`');

    $rows = $wpdb->get_results('SELECT `id`, `user_agent`, `script_name`, `page`
        FROM `' . $options['main_table_name'] . '`');
    foreach ($rows as $row) {
        $browscap = lbakut_browser_info($row->user_agent, $brows);
        if (!$browscap['crawler'] && !$browscap['issyndicationreader']) {
            $human++;
        }

        if (!$browscap['crawler'] && !$browscap['issyndicationreader'] && $row->script_name) {
            if (!isset($script_name_array[$row->script_name])) {
                $script_name_array[$row->script_name] = 0;
            }
            $script_name_array[$row->script_name]++;
        }
        if (!$browscap['crawler'] && !$browscap['issyndicationreader'] && $row->page) {
            if (!isset($page_array[$row->page])) {
                $page_array[$row->page] = 0;
            }
            $page_array[$row->page]++;
        }

        if ($browscap) {
            $recognised++;
            foreach ($browscap as $k => $v) {
                if ($k == 'browser') {
                    if (!$browscap['crawler'] && !$browscap['issyndicationreader']) {
                        if (!isset($browser_array[$v])) {
                            $browser_array[$v] = 0;
                        }
                        $browser_array[$v]++;
                    }
                } else if ($k == 'platform') {
                    if (!$browscap['crawler'] && !$browscap['issyndicationreader']) {
                        if (!isset($platform_array[$v])) {
                            $platform_array[$v] = 0;
                        }
                        $platform_array[$v]++;
                    }
                } else if ($v == true && strlen($v) < 2
                        && $k != 'majorver' && $k != 'minorver' && $k != 'id') {
                    if (!isset($no_of[$k])) {
                        $no_of[$k] = 0;
                    }
                    $no_of[$k]++;
                }
            }
        }
    }

    $other = 0;
    $count = 0;
    asort($page_array, SORT_NUMERIC);
    $page_array = array_reverse($page_array, true);

    foreach ($page_array as $k => $v) {
        if ($count < 15) {
            
        } else {
            $other += $v;
            unset($page_array[$k]);
        }

        $count++;
    }
    if ($other > 0) {
        $page_array['Other'] = $other;
    }

    $other = 0;
    $count = 0;
    asort($browser_array, SORT_NUMERIC);
    $browser_array = array_reverse($browser_array, true);
    foreach ($browser_array as $k => $v) {
        if ($count < 15) {
            
        } else {
            $other += $v;
            unset($browser_array[$k]);
        }

        $count++;
    }
    if ($other > 0) {
        $browser_array['Other'] = $other;
    }

    $other = 0;
    $count = 0;
    asort($platform_array, SORT_NUMERIC);
    $platform_array = array_reverse($platform_array, true);
    foreach ($platform_array as $k => $v) {
        if ($count < 15) {
            
        } else {
            $other += $v;
            unset($platform_array[$k]);
        }

        $count++;
    }
    if ($other > 0) {
        $platform_array['Other'] = $other;
    }

    $other = 0;
    $count = 0;
    asort($script_name_array, SORT_NUMERIC);
    $script_name_array = array_reverse($script_name_array, true);
    foreach ($script_name_array as $k => $v) {
        if ($count < 10) {
            
        } else {
            $other += $v;
            unset($script_name_array[$k]);
        }

        $count++;
    }
    if ($other > 0) {
        $script_name_array['Other'] = $other;
    }

    $no_of['browser_array'] = serialize($browser_array);
    $no_of['platform_array'] = serialize($platform_array);
    $no_of['script_name_array'] = serialize($script_name_array);
    $no_of['page_array'] = serialize($page_array);
    $no_of['time'] = time();
    $no_of['tables_'] = $no_of['tables'];
    $no_of['recognised'] = $recognised;
    $no_of['unique_ips'] = sizeof($wpdb->get_results('SELECT DISTINCT `ip_address`
        FROM `' . $options['main_table_name'] . '`'));
    $no_of['human'] = $human;
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

    $row = $wpdb->get_row("SELECT * FROM `" . $options['browscache_table_name'] . "`
        WHERE `user_agent`='$user_agent' LIMIT 1", ARRAY_A);
    //Becuase tables is a mysql keyword, convert it on retrieval.
    if ($row) {
        $row['tables'] = $row['tables_'];
        unset($row['tables_']);
        return $row;
    } else {
        return false;
    }
}

/*
 * This function populates the user stats table with information about the
 * habits of each unique IP address. By default, it will only use records
 * that were created since the last update but you can make it do a full
 * scan (from the start) by passing true as the first argument.
 */

function lbakut_do_user_stats($from_start = false) {
    set_time_limit(0);
    ignore_user_abort(true);
    global $wpdb;
    $options = lbakut_get_options();
    $unique_ip_array = array();
    $page_views_array = array();

    if ($from_start == true) {
        $last_updated = 0;
    } else {
        $last_updated = intval(lbakut_get_stats_last_updated());
    }

    //$wpdb->show_errors();

    $unique_ips = $wpdb->get_results('SELECT DISTINCT `ip_address`
        FROM `' . $options['main_table_name'] . '`
        WHERE `time` > ' . $last_updated . '');

    foreach ($unique_ips as $row) {
        $first = $wpdb->get_row('SELECT `time` FROM
            `' . $options['main_table_name'] . '` WHERE `ip_address`="' . $row->ip_address . '"
                ORDER BY `time` ASC');
        $last = $wpdb->get_row('SELECT `time` FROM
            `' . $options['main_table_name'] . '` WHERE `ip_address`="' . $row->ip_address . '"
                ORDER BY `time` DESC');
        $user_agents = $wpdb->get_results('SELECT `user_agent`, COUNT(*) as `count` FROM
            `' . $options['main_table_name'] . '` WHERE `ip_address`="' . $row->ip_address . '"
                AND `time` > ' . $last_updated . '
                    GROUP BY `user_agent`');
        $page_views = $wpdb->get_results('SELECT `page`, COUNT(*) as `count`
            FROM `' . $options['main_table_name'] . '` WHERE `ip_address`="' . $row->ip_address . '"
                AND `time` > ' . $last_updated . '
                AND `page`!=""
                GROUP BY `page`');
        $user_ids = $wpdb->get_results('SELECT `user_id`, COUNT(*) as `count`
            FROM `' . $options['main_table_name'] . '` WHERE `ip_address`="' . $row->ip_address . '"
                AND `time` > ' . $last_updated . '
                GROUP BY `user_id`');

        /*
         * Format each of the above arrays to that their keys are the page
         * or browser name or whatever and the value is the number of clicks
         * for each record.
         */

        $page_views_array = array();
        foreach ($page_views as $r) {
            $page_views_array[$r->page] = intval($r->count);
        }

        $user_ids_array = array();
        foreach ($user_ids as $r) {
            $user_ids_array[$r->user_id] = intval($r->count);
        }

        $user_agents_array = array();
        foreach ($user_agents as $r) {
            $user_agents_array[$r->user_agent] = intval($r->count);
        }

        $unique_ip_array[$row->ip_address]['first_visit'] = $first->time;
        $unique_ip_array[$row->ip_address]['last_visit'] = $last->time;
        $unique_ip_array[$row->ip_address]['user_agents'] = $user_agents_array;
        $unique_ip_array[$row->ip_address]['page_views'] = $page_views_array;
        $unique_ip_array[$row->ip_address]['user_ids'] = $user_ids_array;
    }

    foreach ($unique_ip_array as $ip => $row) {
        $exists = $wpdb->get_var('SELECT `ip` FROM `' . $options['user_stats_table_name'] . '`
                WHERE `ip`="' . $ip . '"');
        if ($exists) {
            //get the current stats from the database
            $curr = $wpdb->get_row('SELECT * FROM `' . $options['user_stats_table_name'] . '` WHERE `ip`="' . $ip . '"', ARRAY_A);
            //unserialize any serialized results
            foreach ($curr as $k => $v) {
                if (unserialize($curr[$k]) != false) {
                    $curr[$k] = unserialize($curr[$k]);
                }
            }
            //merge the current stats to the new ones
            foreach ($row as $k => $v) {
                //Ignore merging the first and last visit
                if ($k != 'first_visit' && $k != 'last_visit') {
                    //If the current value is an array, continue
                    if (is_array($v)) {
                        //Unwrap the second level array.
                        foreach ($v as $k2 => $v2) {
                            //If the stat is set, add to it, else set it.
                            if (isset($row[$k][$k2])) {
                                $row[$k][$k2] += $curr[$k][$k2];
                            } else {
                                $row[$k][$k2] = $curr[$k][$k2];
                            }
                        }
                    }
                    //If the value is not an array and isn't empty.
                    else if (!is_array($v) && !empty($v)) {
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
        } else {
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
        } else if (is_float($v)) {
            $format[] = '%f';
        } else {
            $format[] = '%s';
        }
    }

    return $format;
}

function lbakut_get_chart($stat, $title = null, $width = null, $height = null) {
    $row = lbakut_get_latest_stats($stat, ARRAY_N);
    if (!$row) {
        return false;
    }
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
        $chd .= $percent . ',';
        $chdl .= urlencode($k) . ' (' . $percent . '%)|';
    }
    $chd = rtrim($chd, ',');
    $chdl = rtrim($chdl, '|');

    return "http://chart.apis.google.com/chart?chs=" . $width . "x" . $height . "
&cht=p&chtt=$title&chd=" . $chd . "&chl=" . $chdl;
}

function lbakut_percent($number, $outof) {
    if ($outof == 0) {
        return 0;
    }
    return number_format($number / $outof * 100, 1);
}

function lbakut_get_latest_stats($rows = '*', $return_type = OBJECT, $options = null) {
    global $wpdb;
    if ($options == null) {
        $options = lbakut_get_options();
    }
    return $wpdb->get_row('SELECT ' . $rows . ' FROM
            `' . $options['stats_table_name'] . '` ORDER BY `time` DESC', $return_type);
}

function lbakut_get_stats_last_updated($options = null) {
    global $wpdb;
    if ($options == null) {
        $options = lbakut_get_options();
    }
    return $wpdb->get_var('SELECT `time` FROM
            `' . $options['stats_table_name'] . '` ORDER BY `time` DESC');
}

?>
