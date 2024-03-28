<?php

namespace UniForum\Inc\Classes;

// directly access denied
defined('ABSPATH') || exit;

class Assets{
    public function __construct(){
        
        // register stylesheets
        add_action( 'wp_enqueue_scripts', [ $this, 'register_stylesheets' ] );

        // register scripts
        add_action( 'wp_enqueue_scripts', [ $this, 'register_scripts' ] );

        // register admin enqueue scripts
        add_action( 'admin_enqueue_scripts', [ $this, 'register_admin_scripts' ] );
    }

    /**
     * register stylesheets
     *
     * @return void
     */
    public function register_stylesheets(){
        $get_style = $this->get_styles();

        foreach ( $get_style as $handle => $style ){
            $deps = isset( $style['deps'] ) ? $style['deps'] : '';
            wp_enqueue_style(
                $handle,
                $style['src'],
                $deps,
                UF_VERSION,
                'all'
            );
        }
    }

    /**
     * register scripts
     *
     * @return void
     */
    public function register_scripts(){
        $get_scripts = $this->get_scripts();

        foreach ( $get_scripts as $handle => $script ){
            wp_enqueue_script(
                $handle,
                $script['src'],
                $script['deps'],
                UF_VERSION,
                true
            );
        }

        $args = [
            'ajax_url'     => admin_url( 'admin-ajax.php' ),
            'nonce_delete' => wp_create_nonce( 'forum_nonce_delete' ),
            'nonce_edit'   => wp_create_nonce( 'forum_nonce_edit' ),
            'nonce_update' => wp_create_nonce( 'forum_nonce_update' ),
        ];
        
        wp_localize_script( 'uni-forum-script', 'UF', $args );
    }
    
    /**
     * get the stylesheets
     *
     * @return $stylesheets
     */
    public function get_styles(){
        $stylesheets = [
            'uni-forum-style' => [
                'src' => UF_ASSETS_CSS . 'main.css'
            ]
        ];

        $stylesheets = apply_filters( 'uni_forum_stylesheets', $stylesheets );

        return $stylesheets;
    }

    /**
     * get scripts
     *
     * @return void
     */
    public function get_scripts(){
        $scripts = [
            'uni-forum-script' => [
                'src'  => UF_ASSETS_JS . 'custom.js',
                'deps' => ['jquery']
            ]
        ];

        $scripts = apply_filters( 'uni_forum_scripts', $scripts );
        
        return $scripts;
    }

    /**
     * register admin scripts and stylesheet
     *
     * @return void
     */
    public function register_admin_scripts(){
        $get_admin_style   = $this->get_admin_style();
        $get_admin_scripts = $this->get_admin_scripts();

        foreach ( $get_admin_style as $handle => $style ){
            $deps = isset( $style['deps'] ) ? $style['deps'] : '';
            wp_enqueue_style(
                $handle,
                $style['src'],
                $deps,
                UF_VERSION,
                'all'
            );
        }

        foreach ( $get_admin_scripts as $handle => $script ){
            wp_enqueue_script(
                $handle,
                $script['src'],
                $script['deps'],
                UF_VERSION,
                true
            );
        }
    }
    
    /**
     * get admin style
     *
     * @return $stylesheets
     */
    public function get_admin_style(){
        $stylesheets = [
            'uni-forum-style' => [
                'src' => UF_ASSETS_ADMIN_CSS . 'uf-admin-style.css'
            ]
        ];

        $stylesheets = apply_filters( 'uni_forum_admin_stylesheets', $stylesheets );

        return $stylesheets;
    }

    /**
     * get admin script
     *
     * @return $scripts
     */
    public function get_admin_scripts(){
        $scripts = [
            'uni-forum-script' => [
                'src'  => UF_ASSETS_ADMIN_JS . 'uf-admin-scripts.js',
                'deps' => ['jquery']
            ]
        ];

        $scripts = apply_filters( 'uni_forum_admin_scripts', $scripts );
        
        return $scripts;
    }
}