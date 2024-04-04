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
function is_user_online( int $user_id ) {
    $threshold     = 1 * MINUTE_IN_SECONDS;
    $last_activity = get_user_meta($user_id, 'last_activity', true);
    $last_activity = (int) $last_activity;
    
    return (current_time('timestamp') - $last_activity) < $threshold;
}

/**
 * get current profile name
 *
 * @return void
 */
function uf_profile_name( int $author_id ){

    $first_name   = get_the_author_meta('first_name', $author_id);
    $last_name    = get_the_author_meta('last_name', $author_id);
    $display_name = get_the_author_meta( 'display_name', $author_id );

    if ( ! empty( $first_name ) ){
        $profile_name = $first_name . ' ' .$last_name;
    }else{
        $profile_name = $display_name;
    }
    echo esc_html( $profile_name );
}

/**
 * get row count of liked or dislike.
 *
 * @return $count
 */
function get_row_count( int $post_id ){
    global $wpdb;
    $table     = $wpdb->prefix . 'uf_likes';
    $sql       = $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE post_id = %d", $post_id );
    $cache_key = md5( $sql );

    $cached_result = wp_cache_get( $cache_key, 'uf_likes_row_count' );
    
    if ( false !== $cached_result ) {
        return $cached_result;
    }

    $result_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE post_id = %d", $post_id ) );

    wp_cache_set( $cache_key, $result_count, 'uf_likes_row_count' );

    return $result_count;
}

/**
 * create a funciton to check that user is doing like or not.
 *
 * @return $status
 */
function is_user_doing_like( int $post_id, int $current_user_id ){
    global $wpdb;
    $table  = $wpdb->prefix . 'uf_likes';
    $sql    = $wpdb->prepare( "SELECT * FROM $table WHERE post_id = %d AND user_id = %d", $post_id, $current_user_id );
    $result = $wpdb->get_results( $sql );

    $text = 'like';

    if( count( $result ) == 1 ){
        $text = 'liked';
    }
    
    return $text;
} 

/**
 * retrive the forum post item
 *
 * @param [type] $author_id
 * @return void
 */
function uf_get_forum_posts( int $author_id = null ){
    $get_option =  get_option( 'uf_settings_post_per_page', 10 );
    $args1 = [
        'post_type'      => 'forum',
        'posts_per_page' => (int)$get_option,
        'paged'          => get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1,
    ];

    $args2 = [];

    if( ! is_null( $author_id ) ){
        $args2['author'] = intval( $author_id );
    }

    $args = array_merge( $args1, $args2 );

    $forums = new WP_Query( $args );

    if( $forums->have_posts() ): 
        while( $forums->have_posts() ): $forums->the_post();
            $post_id         = get_the_ID();
            $author_id       = get_the_author_meta('ID');
            $author_name     = get_the_author_meta( 'display_name', $author_id );
            $trimmed_content = wp_trim_words( get_the_content( get_the_ID() ), 20 );
            $comment_count   = get_comments_number( $post_id );
            $active_status   = is_user_online( $author_id ) ? 'active' : 'inactive';
            ?>
                <li data-item="<?php echo esc_attr( $post_id ); ?>">
                    <div class="forum-header">
                        <div class="author-name">
                            <span><?php esc_html( uf_profile_name( $author_id ) ); ?></span>
                            <span class="author-status <?php echo esc_attr( $active_status );?>" data-id="user-<?php echo esc_attr( $author_id ); ?>-status"></span>
                        </div>
                        <?php if ( is_user_logged_in() && get_current_user_id() == $author_id ) : ?>
                            <div class="own-post-manage">
                                <button type="button" class="button edit">Edit</button>
                                <button type="button" class="button delete">Delete</button>
                            </div>
                        <?php endif; ?>
                    </div>
                    <h2 class="forum-title"><?php the_title(); ?></h2>
                    <p class="text-uf-default"><?php echo esc_html( $trimmed_content ); ?></p>
                    <a href="<?php the_permalink(); ?>" class="permalink">Read More</a>
                    <div class="forum-footer">
                        <button type="button" class="button button-like <?php echo esc_attr( is_user_doing_like( $post_id, get_current_user_id() ) ); ?>">
                            <span class="like-count">
                                <?php
                                    $get_liked_count = get_row_count( $post_id );
                                    if( $get_liked_count == false ){
                                        $get_liked_count ='';
                                    }
                                    echo esc_html( $get_liked_count );
                                 ?>
                            </span>
                            <span class="like-text text-capitalize">
                                <?php
                                    $like = is_user_doing_like( $post_id, get_current_user_id() );
                                    echo esc_html( $like );
                                ?>
                            </span>
                        </button>
                        <button type="button" class="button comment">
                        <?php 
                            if( $comment_count == 0 ){
                                $comment_text = 'No comments';
                            }elseif( $comment_count == 1 ){
                                $comment_text = $comment_count . ' Comment';
                            }else{
                                $comment_text = $comment_count .' Comments';
                            }
                            echo esc_html( $comment_text );
                         ?>
                        </button>
                    </div>
                </li>
            <?php
        endwhile;

        // pagination
        $paginate_args = [
            'total'     => $forums->max_num_pages,
            'prev_text' => '«',
            'next_text' => '»',
        ];
        $pagination  = '<li class="pagination">';
        $pagination .= paginate_links( $paginate_args );
        $pagination .= '</li>';

        echo esc_html( $pagination );

        wp_reset_postdata();

    else:
        printf( '<h3>%s</h3>', 'No Posts Available' );
    endif;
}

/**
 * get user registration date
 *
 * @param [type] $user_id
 * @return void
 */
function get_user_registration_date( $user_id ) {
    // Check if the registration date is cached
    $cache_key         = 'user_registration_date_' . $user_id;
    $registration_date = wp_cache_get( $cache_key, 'user_registration_dates' );

    // If cached data is available, return it
    if ( false !== $registration_date ) {
        return $registration_date;
    }

    global $wpdb;

    // Retrieve registration date from the database
    $registration_date = $wpdb->get_var( $wpdb->prepare(
        "SELECT user_registered FROM {$wpdb->users} WHERE ID = %d",
        $user_id
    ) );

    // Convert the registration date to UTC
    $registration_date_utc = gmdate( 'd M, Y | H:i:s', strtotime( $registration_date ) );

    // Cache the registration date
    wp_cache_set( $cache_key, $registration_date_utc, 'user_registration_dates' );

    return $registration_date_utc;
}