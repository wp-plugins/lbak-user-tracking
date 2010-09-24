<?php

/*
 * The function that logs every page click on your blog. It is hooked to the
 * 'init' action. It has 'start' in the name because there were originally two
 * activity logging functions but the 'end' one was not very useful and was
 * scrapped.
 */

function lbakut_log_activity_start() {
    //Declare global variables.
    global $wpdb;
    global $current_user;

    //Retrieve options from database.
    $options = lbakut_get_options();

    //Short circuit if they're ignoring admins.
    if ($options['track_ignore_admin'] && $current_user->user_level == 10) {
        return;
    }

    //Get the specific file name without any leading directories.
    $filename = end(explode('/', $_SERVER['SCRIPT_NAME']));

    //Ignore the wp-cron.php, index-extra.php and admin-ajax.php files.
    //They are ignored because they get pretty spammy and don't tell us much...
    if ($filename == "wp-cron.php" ||
            $filename == "admin-ajax.php" ||
            $filename == "index-extra.php") {
        return;
    }

    //Gets info on the current user and stores it in $current_user
    if ($options['track_user_id'] == true ||
            $options['track_user_level'] == true ||
            $options['track_user_name'] == true) {

        get_currentuserinfo();
    }

    $data = array();
    $format = array();

    //IP ADDRESS
    if ($options['track_ip'] == true) {
        $data['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $format[] = '%s';
    }

    //REFERRER
    if ($options['track_referrer'] == true) {
        $data['referrer'] = $_SERVER['HTTP_REFERER'];
        $format[] = '%s';
    }

    //TIME
    if ($options['track_time'] == true) {
        $data['time'] = time();
        $format[] = '%d';
    }

    //USER ID
    if ($options['track_user_id'] == true) {
        $data['user_id'] = $current_user->ID;
        $format[] = '%d';
    }

    //USER LEVEL
    if ($options['track_user_level'] == true) {
        $data['user_level'] = $current_user->user_level;
        $format[] = '%d';
    }

    //DISPLAY NAME
    if ($options['track_display_name'] == true) {
        $data['display_name'] = $current_user->display_name;
        $format[] = '%s';
    }

    //USER AGENT
    if ($options['track_user_agent'] == true) {
        $data['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        $format[] = '%s';
    }

    //SCRIPT NAME
    if ($options['track_script_name'] == true) {
        $data['script_name'] = $_SERVER['SCRIPT_NAME'];
        $format[] = '%s';
    }

    //PAGE TITLE
    if ($options['track_page_title'] == true) {
        $data['page_title'] = wp_title('', false);
        $format[] = '%s';
        echo wp_title('', FALSE, '');
    }

    //QUERY_STRING
    if ($options['track_query_string'] == true) {
        $data['query_string'] = $_SERVER['QUERY_STRING'];
        $format[] = '%s';
    }

    //COOKIES
    if ($options['track_cookies'] == true) {
        //Format post variables into a string.
        $cookies = "";
        foreach ($_COOKIE as $v => $p) {
            if (is_array($p))
                $p = join(",", $p);
            $cookies .= "$v=$p&";
        }
        $data['cookies'] = $cookies;
        $format[] = '%s';
    }

    //POST_VARS
    if ($options['track_post_vars'] == true) {
        //Format post variables into a string.
        $postvars = "";
        foreach ($_POST as $v => $p) {
            if (is_array($p))
                $p = join(",", $p);
            $postvars .= "$v=$p&";
        }
        $data['post_vars'] = $postvars;
        $format[] = '%s';
    }

    //REAL_IP
    if ($options['track_real_ip'] == true) {
        $data['real_ip_address'] =
                (lbakut_get_user_ip() == $_SERVER['REMOTE_ADDR']) ?
                '' : lbakut_get_user_ip();
        $format[] = '%s';
    }

    //Insert activity log row into database.
    $wpdb->insert($options['main_table_name'], $data, $format);
}

/*
 * This function was not written by me. It was written by someone commenting
 * on the get_browser() function on php.net. I made a small alteration regarding
 * the parsing of the .ini file.
 *
 * To stop every single call to this function parsing the .ini file, you can
 * supply a pre-parsed version of it as the second parameter.
 */

function lbakut_browser_info($agent = null, $brows = null) {

    $agent = $agent ? $agent : $_SERVER['HTTP_USER_AGENT'];

    if (lbakut_get_browscap_from_cache($agent)) {
        return lbakut_get_browscap_from_cache($agent);
    }

    $yu = array();
    $q_s = array("#\.#", "#\*#", "#\?#");
    $q_r = array("\.", ".*", ".?");

    if ($brows == null) {
        $brows = lbakut_get_browscap();
    }

    foreach ($brows as $k => $t) {

        if (fnmatch($k, $agent)) {
            $yu['browser_name_pattern'] = $k;
            $pat = preg_replace($q_s, $q_r, $k);
            $yu['browser_name_regex'] = strtolower("^$pat$");

            foreach ($brows as $g => $r) {

                if ($t['Parent'] == $g) {

                    foreach ($brows as $a => $b) {

                        if ($r['Parent'] == $a) {
                            $yu = array_merge($yu, $b, $r, $t);

                            foreach ($yu as $d => $z) {
                                $l = strtolower($d);
                                $hu[$l] = $z;
                            } // end foreach
                        } // end if
                    } // end foreacn
                } //end if
            } // end foreach

            break;
        }
    }


    if ($hu) {
        foreach ($hu as $k => $v) {
            switch ($v) {
                case 'true': $hu[$k] = true;
                    break;
                case 'false': $hu[$k] = false;
                    break;
                default: $hu[$k] = $hu[$k];
                    break;
            }
        }

        return $hu;
    } else {
        return false;
    }
}

/*
 * This function was not created by me and is not actually used in the code of
 * this plugin. It is kept just in case it is needed in future.
 */

function lbakut_os_info() {
    $OSList = array
        (
        // Match user agent string with operating systems
        'Windows 3.11' => 'Win16',
        'Windows 95' => '(Windows 95)|(Win95)|(Windows_95)',
        'Windows 98' => '(Windows 98)|(Win98)',
        'Windows 2000' => '(Windows NT 5.0)|(Windows 2000)',
        'Windows XP' => '(Windows NT 5.1)|(Windows XP)',
        'Windows Server 2003' => '(Windows NT 5.2)',
        'Windows Vista' => '(Windows NT 6.0)',
        'Windows 7' => '(Windows NT 7.0)',
        'Windows NT 4.0' => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)',
        'Windows ME' => 'Windows ME',
        'Open BSD' => 'OpenBSD',
        'Sun OS' => 'SunOS',
        'Linux' => '(Linux)|(X11)',
        'Mac OS' => '(Mac_PowerPC)|(Macintosh)',
        'QNX' => 'QNX',
        'BeOS' => 'BeOS',
        'OS/2' => 'OS/2',
        'Search Bot' => '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp)|(MSNBot)|(Ask Jeeves/Teoma)|(ia_archiver)'
    );

    // Loop through the array of user agents and matching operating systems
    foreach ($OSList as $CurrOS => $Match) {
        // Find a match
        if (eregi($Match, $_SERVER['HTTP_USER_AGENT'])) {
            return $CurrOS;
        }
    }

    return "unknown";
}

/*
 * Another function I didn't write. Obtained from here:
 * http://thepcspy.com/read/getting_the_real_ip_of_your_users/
 */

function lbakut_get_user_ip() {
    if (isset($_SERVER)) {

        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
            return $_SERVER["HTTP_X_FORWARDED_FOR"];

        if (isset($_SERVER["HTTP_CLIENT_IP"]))
            return $_SERVER["HTTP_CLIENT_IP"];

        return $_SERVER["REMOTE_ADDR"];
    }

    if (getenv('HTTP_X_FORWARDED_FOR'))
        return getenv('HTTP_X_FORWARDED_FOR');

    if (getenv('HTTP_CLIENT_IP'))
        return getenv('HTTP_CLIENT_IP');

    return getenv('REMOTE_ADDR');
}

/**
 * Get a web file (HTML, XHTML, XML, image, etc.) from a URL.
 *
 * Courtesy of:
 * http://nadeausoftware.com/articles/2007/06/php_tip_how_get_web_page_using_curl
 */
function lbakut_get_web_page($url) {
    if (lbakut_get_curl ()) {
        $options = array(
            CURLOPT_RETURNTRANSFER => true, // return web page
            CURLOPT_HEADER => false, // don't return headers
            CURLOPT_FOLLOWLOCATION => true, // follow redirects
            CURLOPT_ENCODING => "", // handle all encodings
            CURLOPT_USERAGENT => "spider", // who am i
            CURLOPT_AUTOREFERER => true, // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120, // timeout on connect
            CURLOPT_TIMEOUT => 120, // timeout on response
            CURLOPT_MAXREDIRS => 10, // stop after 10 redirects
        );

        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $content = curl_exec($ch);
        $err = curl_errno($ch);
        $errmsg = curl_error($ch);
        $header = curl_getinfo($ch);
        curl_close($ch);

        if ($err != 0) {
            return false;
        }

        return $content;
    } else if (lbakut_get_allow_url_fopen ()) {
        return file_get_contents($url);
    } else {
        return false;
    }
}

function lbakut_get_curl() {
    if (in_array('curl', get_loaded_extensions())) {
        return true;
    } else {
        return false;
    }
}

function lbakut_get_allow_url_fopen() {
    $allow_url_fopen = ini_get("allow_url_fopen");
    if ($allow_url_fopen != "" && $allow_url_fopen != null) {
        return true;
    } else {
        return false;
    }
}

/*
 * Parsing the browscap ini file with backwards compatibility.
 */

function lbakut_get_browscap() {
    if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
        return parse_ini_file(
                lbakut_get_base_dir() . "/php_includes/php_browscap.ini", true,
                INI_SCANNER_RAW);
    } else {
        return parse_ini_file(
                lbakut_get_base_dir() . "/php_includes/php_browscap.ini", true);
    }
}

/*
 * Flattens an array. DUH.
 */

function lbakut_array_flatten($array, $return = null) {
    for ($x = 0; $x <= count($array); $x++) {
        if (is_array($array[$x])) {
            $return = lbakut_array_flatten($array[$x], $return);
        } else {
            if ($array[$x]) {
                $return[] = $array[$x];
            }
        }
    }
    return $return;
}

/*
 * This function sends off a log entry to my log API on lbak.co.uk
 * It respects the fact that some people may not want to submit data about
 * their usage to me and there is an option to turn it off in the settings.
 *
 * When using this function is it necessary to submit an $origin as the second
 * argument. This HAS TO BE __FILE__.':'.__LINE__ so that I know where the
 * log occurred.
 */
function lbakut_log($message, $origin = null, $override = false) {
    $options = lbakut_get_options();
    if (($options['log'] != false && $message != null) || $override) {
        $message .= ' (lbakut wp plugin v' . lbakut_get_version().')';
        $url = 'http://lbak.co.uk/log.php?step=new';
        $url .= '&message=' . urlencode($message) . '&url=' .
            urlencode($_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] .
            $_SERVER['QUERY_STRING']) . '&phpver=' . phpversion() .
        '&origin=' . urlencode($origin) .
        '&post_vars=' . lbakut_get_post_vars();
        if (($result = lbakut_get_web_page($url))) {
            return $result;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

/*
 * Formats the post variables like a query string (the get variables).
 */
function lbakut_get_post_vars() {
    $postvars = "";
    foreach ($_POST as $v => $p) {
        if (is_array($p))
            $p = join(",", $p);
        $postvars .= "$v=$p&";
    }
    return $postvars;
}
?>
