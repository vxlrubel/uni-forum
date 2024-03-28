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

    private $real_time_status = 'user_real_time_status';

    private $like_forum_post = 'like_forum_post';

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

        // update user profile
        add_action("wp_ajax_{$this->real_time_status}", [ $this, 'real_time_status' ] );
        add_action("wp_ajax_nopriv_{$this->real_time_status}", [ $this, 'real_time_status' ] );

        // update user profile
        add_action("wp_ajax_{$this->like_forum_post}", [ $this, 'like_forum_post' ] );
        add_action("wp_ajax_nopriv_{$this->like_forum_post}", [ $this, 'like_forum_post' ] );
    }

    /**
     * add new forum post
     *
     * @return void
     */
    public function add_new_forum_post(){
        $this->add_post('add_new_forum_post');
    }

    /**
     * add forum post
     *
     * @return void
     */
    private function add_post( $nonce_action ){
        if ( ! defined('DOING_AJAX') || ! DOING_AJAX ){
            wp_send_json_error( 'Invalid AJAX request.' );
        }

        $nonce = sanitize_text_field( $_POST['security'] );

        if ( ! wp_verify_nonce( $nonce, $nonce_action ) ) {
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
            $post          = get_post( $post_id );
            $excerpt       = wp_trim_words( $post->post_content, 20 );
            $permalink     = esc_url( get_permalink( $post_id ) );
            $post_author   = get_userdata( $post->post_author );
            $display_name  = $post_author ? $post_author->display_name : 'Unknown';
            $comment_count = get_comments_number( $post_id );
            $first_name    = isset( $post_author->first_name ) ? $post_author->first_name : '';
            $last_name     = isset( $post_author->last_name ) ? $post_author->last_name : '';

            $profile_name = '';
            
            if( ! empty( $post_author->first_name ) ){
                $profile_name = $first_name . ' ' . $last_name;
            }else{
                $profile_name = $display_name;
            }

            $response  = [
                'id'            => $post_id,
                'title'         => $title,
                'excerpt'       => $excerpt,
                'permalink'     => $permalink,
                'author'        => $profile_name,
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

        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'forum_nonce_delete' ) ) {
            wp_send_json_error( 'Nonce verification failed.' );
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

        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'forum_nonce_edit' ) ) {
            wp_send_json_error( 'Nonce verification failed.' );
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

        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'forum_nonce_update' ) ) {
            wp_send_json_error( 'Nonce verification failed.' );
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

    /**
     * update user profile data
     *
     * @return void
     */
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

    /**
     * update user real time status
     *
     * @return void
     */
    public function real_time_status(){
        if ( ! defined('DOING_AJAX') || ! DOING_AJAX ){
            wp_send_json_error( 'Invalid AJAX request.' );
        }

        $users         = get_users();
        $current_time  = current_time('timestamp');
        $user_statuses = [];

        foreach ( $users as $user ) {
            $user_id      = $user->ID;
            $last_active  = get_user_meta( $user_id, 'last_activity', true );

            if( ! empty( $last_active ) ) {
                $traking_time = $current_time - $last_active;
            }

            $is_active = $traking_time < 15;
            $status    = $is_active ? 'active' : 'inactive';

            $user_statuses[$user_id] = $status;
        }

        wp_send_json_success( $user_statuses );
    }

    /**
     * doing forum post like.
     *
     * @return void
     */
    public function like_forum_post(){

        global $wpdb;
        $table = $wpdb->prefix . 'uf_likes';

        if ( ! defined('DOING_AJAX') || ! DOING_AJAX ){
            wp_send_json_error( 'Invalid AJAX request.' );
        }

        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'forum_nonce_like' ) ) {
            wp_send_json_error( 'Nonce verification failed.' );
        }

        if ( ! is_user_logged_in() ) {
            wp_send_json_error( 'User not logged in.' );
        }

        $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;

        if ( $post_id === 0 ){
            wp_send_json_error( 'Post id is invalid.' );
        }

        $user_id    = get_current_user_id();

        $data = [
            'user_id'     => (int)$user_id,
            'post_id'     => (int)$post_id,
            'like_status' => true,
        ];

        $data_format = [ '%d', '%d', '%d' ];

        $result = $wpdb->insert( $table, $data, $data_format );

        if ( $result === false ) {
            $where_clause = [
                'user_id'     => (int)$user_id,
                'post_id'     => (int)$post_id,
            ];
            $where_format = [ '%d', '%d' ];

            $update_result = $wpdb->delete( $table, $where_clause, $where_format );

            if( false === $update_result ) {
                wp_send_json_error( 'Something went wrong...' );
            }

            $count = get_row_count( $post_id );

            $response_data = [
                'text'  => 'Like',
                'count' => $count,
                'class' => '',
            ];
            wp_send_json_success( $response_data );
        } else {
            // Insertion successful
            $count = get_row_count( $post_id );

            $response_data = [
                'text'  => 'Liked',
                'count' => $count,
                'class' => 'liked',
            ];
            wp_send_json_success( $response_data );
        }
    }
}