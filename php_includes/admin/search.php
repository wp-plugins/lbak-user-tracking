<div id="poststuff" class="ui-sortable meta-box-sortable">
    <div class="postbox">
        <h3><?php _e('What are you looking for?', 'lbakut'); ?></h3>
        <div class="inside">
            <p>
                <?php _e('Below are the fields that you are search in. User comma
                separated values to search for multiple values. Unfortunately
                searching on the Browser and OS field is not viable due to the
                way that they are worked out. A solution to this may be worked
                out in future but currently it is not an option.', 'lbakut'); ?>
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
                            <label for="page_name">
                                <?php echo __('Page', 'lbakut'); ?>
                            </label>
                        </td>
                        <td>
                            <?php echo lbakut_is_isnt_box('page_name_is'); ?>
                            <input type="text" name="page_name"
                                   id="page_name"
                                   value="<?php echo $_GET['page_name']; ?>"/>
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
                <a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=lbakut&step=displaysettings">click here</a>.
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
            $page_name = lbakut_search_var_prepare('page_name', 'string');

            $query = "
                                WHERE 1
                    $time
                    $display_name
                    $user_id
                    $user_level
                    $ip_address
                    $real_ip_address
                    $page_name";

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
            ?>
        </div>
    </div>
</div>