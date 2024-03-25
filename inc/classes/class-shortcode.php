<?php

namespace UniForum\Inc\Classes;

// directly access denied
defined('ABSPATH') || exit;

class Shortcode{

    public function __construct(){
        // registration form
        add_shortcode( 'uf_registration_form', [ $this, 'registration_form' ]);

        // render forum post
        add_shortcode( 'uf_render_forum', [ $this, 'render_forum_post' ]);

        // user profile
        add_shortcode( 'uf_user_profile', [ $this, 'user_profile' ]);
    }

    /**
     * user_profile() method are showing the user details.
     *
     * @return void
     */
    public function user_profile(){
        $current_user = wp_get_current_user();
        $user_name    = $current_user->display_name;
        $user_email   = $current_user->user_email;

        if( ! is_user_logged_in() ) {
            ob_start();
            echo '<div class="uf-login-form-parent">';
            wp_login_form();
            echo '</div>';
            $login_form = ob_get_clean();

            return $login_form;
        }

        ob_start();
        require_once dirname(__FILE__) . '/user-profile.php';

        return ob_get_clean();
    }

    /**
     * render forum post
     *
     * @return void
     */
    public function render_forum_post(){
        if ( is_user_logged_in() ) {
            ob_start();
            require_once dirname( __FILE__ ) . '/render-forum-post.php';

            return ob_get_clean();
        } else {
            ob_start();
            echo '<div class="uf-login-form-parent">';
            wp_login_form();
            echo '</div>';
            $login_form = ob_get_clean();

            return $login_form;
        }
    }

    /**
     * create registration form
     *
     * @return void
     */
    public function registration_form(){
        ob_start();
        require_once dirname( __FILE__ ) . '/registration-form.php';
        
        return ob_get_clean();
    }
}