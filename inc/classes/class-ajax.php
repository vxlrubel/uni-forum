<?php

namespace UniForum\Inc\Classes;

// directly access denied
defined('ABSPATH') || exit;

class Ajax_Handle{

    private $add_forum_post = 'add_new_forum_post';
    private $del_forum_post = 'delete_forum_post';

    public function __construct(){
        // add nrw forum post
        add_action("wp_ajax_{$this->add_forum_post}", [ $this, 'add_new_forum_post' ] );
        add_action("wp_ajax_nopriv_{$this->add_forum_post}", [ $this, 'add_new_forum_post' ] );

        // delete forum post
        add_action("wp_ajax_{$this->del_forum_post}", [ $this, 'delete_forum_post' ] );
        add_action("wp_ajax_nopriv_{$this->del_forum_post}", [ $this, 'delete_forum_post' ] );
    }

    /**
     * add new forum post
     *
     * @return void
     */
    public function add_new_forum_post(){
        if ( ! defined('DOING_AJAX') || ! DOING_AJAX ){
            wp_send_json_error( 'Invalid AJAX request.' );
        }

        $nonce = sanitize_text_field( $_POST['security'] );

        if ( ! wp_verify_nonce( $nonce, 'add_new_forum_post' ) ) {
            wp_send_json_error( 'Nonce verification failed.' );
            wp_die();
        }

        $title   = sanitize_text_field($_POST['title']);
        $content = wp_kses_post($_POST['content']);

        $forum_post = array(
            'post_title'    => $title,
            'post_content'  => $content,
            'post_status'   => 'publish',
            'post_type'     => 'forum'
        );

        $post_id = wp_insert_post( $forum_post );

        if( $post_id ){
            $post             = get_post($post_id);
            $excerpt          = wp_trim_words($post->post_content, 20);
            $permalink        = esc_url( get_permalink($post_id) );
            $post_author      = get_userdata($post->post_author);
            $post_author_name = $post_author ? $post_author->display_name : 'Unknown';
            $comment_count    = get_comments_number( $post_id );

            $response  = [
                'id'            => $post_id,
                'title'         => $title,
                'excerpt'       => $excerpt,
                'permalink'     => $permalink,
                'author'        => $post_author_name,
                'access'        => true,
                'comment_count' => $comment_count
            ];

            wp_send_json_success( $response );
        }else{
            wp_send_json_success( 'failed' );
        }
    }

    /**
     * delete forum post
     *
     * @return void
     */
    public function delete_forum_post(){
        if ( ! defined('DOING_AJAX') || ! DOING_AJAX ){
            wp_send_json_error( 'Invalid AJAX request.' );
        }

        $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : '';

        if( empty( $post_id ) ){
            wp_send_json_error( 'Post id not valid.' );
        }

        $delete_post = wp_delete_post( $post_id, true );

        if( $delete_post ){
            wp_send_json_success( [ 'status' => '200' ] );
        }else{
            wp_send_json_error( 'Error deleting post.' );
        }
    }
}