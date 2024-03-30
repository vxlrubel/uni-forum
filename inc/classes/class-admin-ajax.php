<?php

namespace UniForum\Inc\Classes;

// directly access denied
defined('ABSPATH') || exit;

class Admin_Ajax{

    private $update_settings_options   = 'uf_update_settings_options';

    public function __construct(){
        // add nrw forum post
        add_action("wp_ajax_{$this->update_settings_options}", [ $this, 'update_settings' ] );
        add_action("wp_ajax_nopriv_{$this->update_settings_options}", [ $this, 'update_settings' ] );
    }

    /**
     * update settings options
     *
     * @return void
     */
    public function update_settings(){
        if ( ! defined('DOING_AJAX') || ! DOING_AJAX ){
            wp_send_json_error( $this->notice( 'notice-error', 'Invalid AJAX request.' ) );
        }

        if ( ! isset( $_POST['update_forum_nonce'] ) || ! wp_verify_nonce( $_POST['update_forum_nonce'], 'update_settings_options' ) ) {
            wp_send_json_error( '' );
            wp_send_json_error( $this->notice( 'notice-error', 'Nonce verification failed.' ) );
        }

        $post_per_page = isset( $_POST['post_per_page'] ) ? (int) $_POST['post_per_page'] : 10;

        update_option( 'uf_settings_post_per_page', $post_per_page );
        
        wp_send_json_success( $this->notice( 'notice-success', 'Save Settings successfull.' ) );
    }

    /**
     * create notice text
     *
     * @param string $notice_type
     * @param string $message
     * @return void
     */
    private function notice( string $notice_type, string $message ) {
        $notice = "<div class=\"notice {$notice_type} is-dismissible\"><p>{$message}</p></div>";
        return $notice;
    }

}