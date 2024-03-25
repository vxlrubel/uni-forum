<?php

namespace UniForum\Inc\Classes;

// directly access denied
defined('ABSPATH') || exit;

class Shortcode{

    public function __construct(){
        add_shortcode( 'uf_registration_form', [ $this, 'registration_form' ]);
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