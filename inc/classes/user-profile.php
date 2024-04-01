<?php

// directly access denied
defined('ABSPATH') || exit;

global $wpdb;
$current_user = wp_get_current_user();
$first_name   = $current_user->first_name;
$last_name    = $current_user->last_name;
$display_name = $current_user->display_name;
$user_email   = $current_user->user_email;
$current_url  = esc_url( home_url( $_SERVER['REQUEST_URI'] ) );
$logout_url   = wp_logout_url( $current_url );

$get_registration_date = $wpdb->get_var( $wpdb->prepare(
    "SELECT user_registered FROM {$wpdb->users} WHERE ID = %d",
    $current_user->ID
) );

// Convert the registration date to UTC
$registration_date = gmdate( 'd M, Y | H:i:s', strtotime( $get_registration_date ) );


?>

<div class="forum-wrap uf-reset">
    <div class="forum-post">
        <div action="" class="add-new-forum">

            <?php
                if ( ! empty( $first_name ) ){
                    $profile_name = $first_name . ' ' .$last_name;
                }else{
                    $profile_name = $display_name;
                }
                printf( '<h2 class="profile-name">%s</h2>', esc_html( $profile_name ) );
                printf( 
                    '<span class="registration-name">%s: %s</span>',
                    esc_html__( 'Registration Date', 'uni-forum' ),
                    esc_html( $registration_date )
                );
                echo '<div class="link">';
                printf( '<a href="javascript:void(0)" class="profile-button" id="profile-edit-form-toggle">%s</a>', 'Edit Profile' );
                printf( '<a href="%1$s" class="profile-button">%2$s</a>', esc_url( $logout_url ), 'Sign Out' );
                echo '</div>';
            ?>
            <form action="#" class="profile-edit-form" id="profile-edit-form">
                <input type="text" id="user-f-name" placeholder="First Name" value="<?php echo esc_attr( $first_name ); ?>">
                <input type="text" id="user-l-name" placeholder="Last Name" value="<?php echo esc_attr( $last_name ); ?>">
                <input type="hidden" id="user-id" value="<?php echo esc_attr( $current_user->ID ); ?>">
                <input type="submit" value="Save Changes">
            </form>

        </div>
    </div>
    <h2 class="prevent-forum-post-title">Create New Forum Post</h2>
    <div class="forum-post">
        <form action="" class="add-new-forum" id="add-new-forum-post">
            <input type="text" name="forum_tite" placeholder="Forum Title">
            <textarea name="forum_content" placeholder="Write content"></textarea>
            <?php wp_nonce_field( 'add_new_forum_post', 'forum_nonce' ); ?>
            <div class="text-right">
                <input type="submit" class="button-submit" value="Publish">
            </div>
        </form>
    </div>

    <ul class="forum-items">
        <?php uf_get_forum_posts( $current_user->ID ); ?>
    </ul>
</div>