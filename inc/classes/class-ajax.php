<?php

namespace UniForum\Inc\Classes;

// directly access denied
defined('ABSPATH') || exit;

class Ajax_Handle{

    private $add_forum_post   = 'add_new_forum_post';

    private $del_forum_post   = 'delete_forum_post';
    
    private $fetch_forum_post = 'fetch_forum_post_by_id';
    
    private $update_forum_post = 'update_forum_post_by_id';

    private $update_user_profile = 'update_user_profile';

    public function __construct(){
        // add nrw forum post
        add_action("wp_ajax_{$this->add_forum_post}", [ $this, 'add_new_forum_post' ] );
        add_action("wp_ajax_nopriv_{$this->add_forum_post}", [ $this, 'add_new_forum_post' ] );

        // delete forum post
        add_action("wp_ajax_{$this->del_forum_post}", [ $this, 'delete_forum_post' ] );
        add_action("wp_ajax_nopriv_{$this->del_forum_post}", [ $this, 'delete_forum_post' ] );

        // delete forum post
        add_action("wp_ajax_{$this->fetch_forum_post}", [ $this, 'fetch_forum_post_by_id' ] );
        add_action("wp_ajax_nopriv_{$this->fetch_forum_post}", [ $this, 'fetch_forum_post_by_id' ] );

        // delete forum post
        add_action("wp_ajax_{$this->update_forum_post}", [ $this, 'update_forum_post_by_id' ] );
        add_action("wp_ajax_nopriv_{$this->update_forum_post}", [ $this, 'update_forum_post_by_id' ] );

        // update user profile
        add_action("wp_ajax_{$this->update_user_profile}", [ $this, 'update_user_profile' ] );
        add_action("wp_ajax_nopriv_{$this->update_user_profile}", [ $this, 'update_user_profile' ] );
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

    /**
     * fetch forum single post by their id
     *
     * @return void
     */
    public function fetch_forum_post_by_id(){
        if ( ! defined('DOING_AJAX') || ! DOING_AJAX ){
            wp_send_json_error( 'Invalid AJAX request.' );
        }

        $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : '';

        if( empty( $post_id ) ){
            wp_send_json_error( 'Post id not valid.' );
        }

        $post = get_post( $post_id );

        if ( ! $post ) {
            wp_send_json_error( 'Post not found.' );
        }
        $data = array(
            'post_id'      => $post->ID,
            'post_title'   => $post->post_title,
            'post_content' => $post->post_content
        );

        wp_send_json_success( $data );
    }

    /**
     * update forum post by their id
     *
     * @return void
     */
    public function update_forum_post_by_id(){
        if ( ! defined('DOING_AJAX') || ! DOING_AJAX ){
            wp_send_json_error( 'Invalid AJAX request.' );
        }

        $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : '';

        if( empty( $post_id ) ){
            wp_send_json_error( 'Post id not valid.' );
        }
        
        $title   = isset( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';
        $content = isset( $_POST['content'] ) ? sanitize_textarea_field( $_POST['content'] ) : '';

        $post_data = [
            'ID'         => $post_id,
            'post_title' => $title,
            'post_content' => $content
        ];

        $updated = wp_update_post( $post_data );

        if( $updated ){
            wp_send_json_success( [ 'status' => 200 ] );
        }else{
            wp_send_json_error( [ 'status' => 403 ] );
        }
    }

    public function update_user_profile(){
        if ( ! defined('DOING_AJAX') || ! DOING_AJAX ){
            wp_send_json_error( 'Invalid AJAX request.' );
        }
    
        $user_id    = $_POST['id'];
        $first_name = isset( $_POST['f_name'] ) ? sanitize_text_field( $_POST['f_name'] ) : '';
        $last_name  = isset( $_POST['l_name'] ) ? sanitize_text_field( $_POST['l_name'] ) : '';

        if( empty( $user_id ) ){
            wp_send_json_error( 'User id not valid.' );
        }
        $result = wp_update_user(
            [
                'ID'         => $user_id,
                'first_name' => $first_name,
                'last_name'  => $last_name
            ]
        );
    
        if ( is_wp_error( $result ) ) {
            wp_send_json_error( $result->get_error_message() );
        } else {
            $profile_name = $first_name . ' ' . $last_name;
            wp_send_json_success( $profile_name );
        }
    }
}