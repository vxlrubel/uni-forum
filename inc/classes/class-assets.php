<?php

namespace UniForum\Inc\Classes;

// directly access denied
defined('ABSPATH') || exit;

class Assets{
    public function __construct(){
        
        // register stylesheets
        add_action( 'wp_enqueue_scripts', [ $this, 'register_stylesheets' ] );
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
}