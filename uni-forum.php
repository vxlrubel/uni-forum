<?php
/*
 * Plugin Name:       Uni Forum
 * Plugin URI:        https://github.com/vxlrubel/uni-forum
 * Description:       Uni Forum is a comprehensive forum management plugin for WordPress, designed to empower website owners to effortlessly create and manage online communities. With Uni Forum, users can register on your site, initiate discussions, and seamlessly interact with others in a user-friendly environment.
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Rubel Mahmud ( Sujan )
 * Author URI:        https://www.linkedin.com/in/vxlrubel/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       uni-forum
 * Domain Path:       /lang
 */

//  directly access denied
 defined('ABSPATH') || exit;



 final class Uni_Forum{

    // instance of the class
    private static $instance;

    // plugin version
    private static $version = '1.0';

    /**
     * create a new instance
     *
     * @return void
     */
    public static function init(){
        if( is_null( self::$instance ) ){
            self::$instance = new self();
        }

        return self::$instance;
    }

 }

 function uni_forum(){
    return Uni_Forum::init();
 }

 uni_forum();