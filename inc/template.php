<?php

//  directly access denied
 defined('ABSPATH') || exit;

/**
 * is_user_logged_in()
 * This function define the user is logged in or not.
 *
 * @param [type] integer $user_id
 * @return boolean
 */
function is_user_online( $user_id ) {
    $threshold     = 1 * MINUTE_IN_SECONDS;
    $last_activity = get_user_meta($user_id, 'last_activity', true);
    $last_activity = (int) $last_activity;
    
    return (current_time('timestamp') - $last_activity) < $threshold;
}