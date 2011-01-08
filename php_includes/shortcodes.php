<?php
/*
 * This function turns the arguments of a [checkout] tag into an associative
 * array.
*/
function lbakut_process_shortcode($shortcode) {
    $split = preg_split('/"(\ |^)/i', $shortcode);
    $return = array();
    for ($i = 0; $i < sizeof($split); $i++) {
        $kvpair = explode("=", $split[$i]);
        $return[trim($kvpair[0])] = str_replace('"', '', $kvpair[1]);
    }
    return $return;
}

/*
 * Replaces the [checkout] short codes with their appropriate form code.
*/
function lbakut_parse_shortcode($the_content) {
    global $wpdb;
    $options = lbakut_get_options();

    if ($options['stats_enable_shortcodes']) {

        $regex_pattern = '/\[lbakut(?:.*)\]/i';
        $regex_pattern_grouped = '/\[lbakut(.*)\]/i';
        $matches = array();

        //Match all instances of [checkout] and store them in $matches.
        preg_match_all($regex_pattern, $the_content, $matches);

        //If there are matches, execute following loop.
        if (sizeof($matches[0]) > 0) {
            //For each of the matches (it's $matches[0] because of how preg_match_all works)
            foreach ($matches[0] as $match) {

                //Get the arguments for the checkout tag as an associative array.
                $args = lbakut_process_shortcode(preg_replace($regex_pattern_grouped, '$1', $match));

                if (isset($args['type'])) {
                    $title = $args['title'] ? $args['title'] : null;
                    $width = $args['width'] ? $args['width'] : null;
                    $height = $args['height'] ? $args['height'] : null;
                    //Create image tag.script_name
                    $replace = '<img src="'.lbakut_get_chart(lbakut_chart_type($args['type']), $title, $width, $height).'" />';
                    //Replace the content with the appropriate code.
                    $the_content = preg_replace($regex_pattern, $replace, $the_content, 1);
                }
            }
        }
    }

    //Return the content. NECESSARY.
    return $the_content;
}

function lbakut_chart_type($type) {
    switch ($type) {
        case 'browser': return '`browser_array`';
            break;
        case 'pageviews': return '`page_array`';
            break;
        case 'os': return '`platform_array`';
            break;
        default: return null;
    }
}
?>
