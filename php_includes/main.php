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
        $data['ip_address'] = $wpdb->escape($_SERVER['REMOTE_ADDR']);
        $format[] = '%s';
    }
    
    //REFERRER
    if ($options['track_referrer'] == true) {
        $data['referrer'] = $wpdb->escape($_SERVER['HTTP_REFERER']);
        $format[] = '%s';
    }
    
    //TIME
    if ($options['track_time'] == true) {
        $data['time'] = time();
        $format[] = '%d';
    }
    
    //USER ID
    if ($options['track_user_id'] == true) {
        $data['user_id'] = $wpdb->escape($current_user->ID);
        $format[] = '%d';
    }
    
    //USER LEVEL
    if ($options['track_user_level'] == true) {
        $data['user_level'] = $wpdb->escape($current_user->user_level);
        $format[] = '%d';
    }
    
    //DISPLAY NAME
    if ($options['track_display_name'] == true) {
        $data['display_name'] = $wpdb->escape($current_user->display_name);
        $format[] = '%s';
    }
    
    //USER AGENT
    if ($options['track_user_agent'] == true) {
        $data['user_agent'] = $wpdb->escape($_SERVER['HTTP_USER_AGENT']);
        $format[] = '%s';
    }
    
    //SCRIPT NAME
    if ($options['track_script_name'] == true) {
        $data['script_name'] = $wpdb->escape($_SERVER['SCRIPT_NAME']);
        $format[] = '%s';
    }
    
    //QUERY_STRING
    if ($options['track_query_string'] == true) {
        $data['query_string'] = $wpdb->escape($_SERVER['QUERY_STRING']);
        $format[] = '%s';
    }

    //COOKIES
    if ($options['track_cookies'] == true) {
        //Format post variables into a string.
        $cookies = "";
        foreach ($_COOKIE as $v=>$p) {
            if (is_array($p)) $p = join(",",$p);
            $cookies .= "$v=$p&";
        }
        $data['cookies'] = $wpdb->escape($cookies);
        $format[] = '%s';
    }

    //POST_VARS
    if ($options['track_post_vars'] == true) {
        //Format post variables into a string.
        $postvars = "";
        foreach ($_POST as $v=>$p) {
            if (is_array($p)) $p = join(",",$p);
            $postvars .= "$v=$p&";
        }
        $data['post_vars'] = $wpdb->escape($postvars);
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
function lbakut_browser_info($agent, $brows = null) {
    $agent=$agent?$agent:$_SERVER['HTTP_USER_AGENT'];
    $yu=array();
    $q_s=array("#\.#","#\*#","#\?#");
    $q_r=array("\.",".*",".?");
    if ($brows == null) {
        if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
            $brows = parse_ini_file("php_browscap.ini", true, INI_SCANNER_RAW);
        } else {
            $brows = parse_ini_file("php_browscap.ini", true);
        }
    }
    foreach($brows as $k=>$t) {
        if(fnmatch($k,$agent)) {
            $yu['browser_name_pattern']=$k;
            $pat=preg_replace($q_s,$q_r,$k);
            $yu['browser_name_regex']=strtolower("^$pat$");
            foreach($brows as $g=>$r) {
                if($t['Parent']==$g) {
                    foreach($brows as $a=>$b) {
                        if($r['Parent']==$a) {
                            $yu=array_merge($yu,$b,$r,$t);
                            foreach($yu as $d=>$z) {
                                $l=strtolower($d);
                                $hu[$l]=$z;
                            }
                        }
                    }
                }
            }
            break;
        }
    }
    return $hu;
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
            'Search Bot'=>'(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp)|(MSNBot)|(Ask Jeeves/Teoma)|(ia_archiver)'
    );

    // Loop through the array of user agents and matching operating systems
    foreach($OSList as $CurrOS=>$Match) {
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

/*
 * A function that gets the current version of the lbakut plugin.
 */
function lbakut_get_version() {
    return 'ALPHA';
}

?>
