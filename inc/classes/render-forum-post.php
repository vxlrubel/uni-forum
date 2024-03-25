<?php
// directly access denied
defined('ABSPATH') || exit;
?>

<div class="forum-wrap">
    <div class="forum-post">
        <form action="" class="add-new-forum">
            <input type="text" name="forum_tite" placeholder="Forum Title">
            <textarea name="forum_content" placeholder="Write content"></textarea>
            <?php wp_nonce_field( 'add_new_forum_post', 'forum_nonce' ); ?>
            <div class="submit-button">
                <input type="submit" value="Publish">
            </div>
        </form>
    </div>
    <ul class="forum-items">

        <?php
            $args = [
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