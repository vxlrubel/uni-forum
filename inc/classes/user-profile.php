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

$registration_date = date( 'd M, Y | H:i:s', strtotime( $get_registration_date ) );

?>

<div class="forum-wrap">
    <div class="forum-post">
        <div action="" class="add-new-forum">

            <?php
                if ( ! empty( $first_name ) ){
                    $profile_name = 'Name: '. $first_name . ' ' .$last_name;
                }else{
                    $profile_name = 'Display Name: '. $display_name;
                }
                printf( '<span class="profile-name">%s</span>', $profile_name );
                printf( '<span class="registration-name">%s: %s</span>', 'Registration Date', $registration_date );
                printf( '<a href="javascript:void(0)" class="profile-button" id="profile-edit-form-toggle">%s</a>', 'Edit Profile' );
                printf( '<a href="%1$s" class="profile-button">%2$s</a>', esc_url( $logout_url ), 'Sign Out' );
                
            ?>
            <form action="#" class="profile-edit-form" id="profile-edit-form">
                <input type="text" id="user-f-name" placeholder="First Name" value="<?php echo esc_attr( $first_name ); ?>">
                <input type="text" id="user-l-name" placeholder="Last Name" value="<?php echo esc_attr( $last_name ); ?>">
                <input type="hidden" id="user-id" value="<?php echo esc_attr( $current_user->ID ); ?>">
                <input type="submit" value="Save Changes">
            </form>

        </div>
    </div>
    <ul class="forum-items">

        <?php
            $args = [
                'author'         => $current_user->ID,
                'post_type'      => 'forum',
                'posts_per_page' => 10,
                'paged'          => get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1,
            ];

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
                            <p>
                                <span class="author-status <?php echo esc_attr( $active_status );?>" data-id="user-<?php echo esc_attr( $author_id ); ?>-status"></span>
                                <span class="author-name"><?php echo esc_html( $author_name ); ?></span>
                            </p>
                            <h2 class="forum-title"><?php the_title(); ?></h2>
                            <p class="text-uf-default"><?php echo esc_html( $trimmed_content ); ?></p>
                            <a href="<?php the_permalink(); ?>" class="permalink">Read More</a>
                            <?php if ( is_user_logged_in() && get_current_user_id() == $author_id ) : ?>
                                <a href="javascript:void(0)" class="permalink edit">Edit</a>
                                <a href="javascript:void(0)" class="permalink delete" >Delete</a>
                            <?php endif; ?>
                            <span class="uf-comment-count">
                                <?php 
                                    if( $comment_count == 0 ){
                                        $comment_text = 'No comments';
                                    }elseif( $comment_count == 1 ){
                                        $comment_text = $comment_count . ' Comment';
                                    }else{
                                        $comment_text = $comment_count .' Comments';
                                    }
                                    echo $comment_text;
                                 ?>
                            </span>
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

                echo $pagination;

            else:
                printf( '<h3>%s</h3>', 'No Posts Available' );
            endif;
         ?>
    </ul>
</div>