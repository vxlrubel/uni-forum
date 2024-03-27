<?php

namespace UniForum\Inc\Classes;

// directly access denied
defined('ABSPATH') || exit;

class Admin_Menu{
    public function __construct(){
        add_action( 'admin_menu', [ $this, 'admin_menu'] );
    }

    /**
     * create a new admin menu to manage forum settings
     *
     * @return void
     */
    public function admin_menu(){
        add_submenu_page( 
            'edit.php?post_type=forum',    // Parent menu slug (forum post type)
            'Forum Settings',              // Page title
            __( 'Settings', 'uni-forum' ), // Menu title
            'edit_posts',                  // Capability required to access the page
            'forum-settings',              // Menu slug
            [ $this, 'settings_page' ]     // Callback function to display the page content
        );
    }

    /**
     * Callback to display the submenu page content
     *
     * @return void
     */
    public function settings_page(){ ?>
        <div class="wrap">
            <h1>Forum Settings</h1>
        </div>
        <?php
    }
}